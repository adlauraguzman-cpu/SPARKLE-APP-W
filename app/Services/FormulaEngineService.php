<?php

namespace App\Services;

use App\Models\Aroma;
use App\Models\ReglaAfinidad;
use App\Models\User;

class FormulaEngineService
{
    /**
     * Calculate a personalized formula based on safety, affinity rules, sensory profile and past ratings.
     *
     * @param array{
     *     objetivo: string,
     *     momento: string,
     *     intensidad: string,
     *     espacio_tipo?: string|null,
     *     emocion?: string|null,
     *     energia?: int|null,
     * } $context
     * @return array{
     *     success: bool,
     *     error_code?: string,
     *     message?: string,
     *     componentes?: array<int, array{aroma: Aroma, porcentaje: int}>,
     *     explicacion?: string,
     *     ritual?: array{gotas: int, duracion_min: int, pasos: array<string>},
     *     version_reglas?: string,
     *     seguridad_analisis?: array<string, mixed>,
     * }
     */
    public function calcular(array $context, ?User $user = null, ?array $guestSafety = null): array
    {
        // 1. Obtener perfil de seguridad (de usuario o de invitado)
        $alergias = [];
        $mascotas = false;
        $embarazo = 'no';

        if ($user) {
            $perfilSeguridad = $user->perfilSeguridad;
            if ($perfilSeguridad) {
                $alergias = $perfilSeguridad->alergias ?? [];
                $mascotas = (bool) $perfilSeguridad->mascotas_en_casa;
                $embarazo = $perfilSeguridad->embarazo_o_lactancia ?? 'no';
            }
        } elseif ($guestSafety) {
            $alergias = $guestSafety['alergias'] ?? [];
            $mascotas = ! empty($guestSafety['mascotas']);
            $embarazo = $guestSafety['embarazo'] ?? 'no';
        }

        // Obtener todos los aromas activos
        $aromas = Aroma::where('activo', true)->get();
        if ($aromas->isEmpty()) {
            return [
                'success' => false,
                'error_code' => 'SIN_AROMAS',
                'message' => 'No hay aromas registrados en el catálogo.',
            ];
        }

        // 1. FILTRO DE SEGURIDAD (Descarte estricto)
        $aromasSeguros = [];
        $motivosDescarte = [];

        foreach ($aromas as $aroma) {
            $descartado = false;
            $advertencias = $aroma->advertencias_seguridad ?? [];

            // A. Chequeo de alergias
            if (! empty($alergias) && ! empty($advertencias['alergias'])) {
                foreach ($alergias as $alergia) {
                    $alergiaNorm = mb_strtolower(trim($alergia));
                    foreach ($advertencias['alergias'] as $alergeno) {
                        if (str_contains(mb_strtolower($alergeno), $alergiaNorm) || str_contains($alergiaNorm, mb_strtolower($alergeno))) {
                            $descartado = true;
                            $motivosDescarte[$aroma->nombre] = "Contraindicado por alergia reportada a {$alergia}.";
                            break 2;
                        }
                    }
                }
            }

            // B. Chequeo de mascotas (ej. gatos o caninos vulnerables a ciertos aceites)
            if (! $descartado && $mascotas && isset($advertencias['mascotas_seguro']) && $advertencias['mascotas_seguro'] === false) {
                $descartado = true;
                $motivosDescarte[$aroma->nombre] = 'No recomendado en hogares con mascotas vulnerables a estos componentes aromáticos.';
            }

            // C. Chequeo de embarazo / lactancia
            if (! $descartado && $embarazo === 'si' && isset($advertencias['embarazo_seguro']) && $advertencias['embarazo_seguro'] === false) {
                $descartado = true;
                $motivosDescarte[$aroma->nombre] = 'Contraindicado durante estado de embarazo o lactancia activa.';
            }

            if (! $descartado) {
                $aromasSeguros[] = $aroma;
            }
        }

        // Si la seguridad descarta todos los aromas, activar variante de error "Sin fórmula segura"
        if (empty($aromasSeguros)) {
            return [
                'success' => false,
                'error_code' => 'SIN_FORMULA_SEGURA',
                'message' => 'No es posible formular una mezcla segura para tus condiciones médicas o del hogar reportadas.',
                'motivos' => $motivosDescarte,
            ];
        }

        // 2. PUNTAJE DE AFINIDAD (Basado en reglas activas publicadas)
        $reglas = ReglaAfinidad::where('estado', 'publicada')->get();
        $versionReglas = $reglas->first()?->version ?? 'v1.0';

        $puntuaciones = [];
        foreach ($aromasSeguros as $aroma) {
            $puntuaciones[$aroma->id] = 1.0; // Base inicial para asegurar presencia mínima
        }

        foreach ($aromasSeguros as $aroma) {
            $reglasAroma = $reglas->where('aroma_id', $aroma->id);

            // Variable 1: Objetivo
            if (! empty($context['objetivo'])) {
                $match = $reglasAroma->first(fn ($r) => $r->variable === 'objetivo' && $r->valor === $context['objetivo']);
                if ($match) {
                    $puntuaciones[$aroma->id] += (float) $match->peso;
                }
            }

            // Variable 2: Espacio
            if (! empty($context['espacio_tipo'])) {
                $match = $reglasAroma->first(fn ($r) => $r->variable === 'espacio' && $r->valor === $context['espacio_tipo']);
                if ($match) {
                    $puntuaciones[$aroma->id] += (float) $match->peso;
                }
            }

            // Variable 3: Momento
            if (! empty($context['momento'])) {
                $match = $reglasAroma->first(fn ($r) => $r->variable === 'momento' && $r->valor === $context['momento']);
                if ($match) {
                    $puntuaciones[$aroma->id] += (float) $match->peso;
                }
            }

            // Variable 4: Emoción
            if (! empty($context['emocion'])) {
                $match = $reglasAroma->first(fn ($r) => $r->variable === 'emocion' && $r->valor === $context['emocion']);
                if ($match) {
                    $puntuaciones[$aroma->id] += (float) $match->peso;
                }
            }
        }

        // 3. AJUSTE PERSONAL (Perfil Sensorial + Historial de Calificaciones)
        if ($user) {
            $perfilSensorial = $user->perfilSensorial;
            if ($perfilSensorial) {
                $preferidas = $perfilSensorial->familias_preferidas ?? [];
                $evitadas = $perfilSensorial->familias_evitadas ?? [];

                foreach ($aromasSeguros as $aroma) {
                    if (in_array($aroma->familia, $preferidas, true)) {
                        $puntuaciones[$aroma->id] += 2.0; // Bonificación positiva
                    }
                    if (in_array($aroma->familia, $evitadas, true)) {
                        $puntuaciones[$aroma->id] = max(0.2, $puntuaciones[$aroma->id] - 2.0); // Penalización
                    }
                }
            }

            // Historial de diario: aromas en fórmulas con altas calificaciones (>=4) ganan +1
            $entradasRecientes = $user->entradasDiario()->with('formula.componentes')->latest()->take(10)->get();
            foreach ($entradasRecientes as $entrada) {
                if ($entrada->formula && $entrada->calificacion >= 4) {
                    foreach ($entrada->formula->componentes as $comp) {
                        if (isset($puntuaciones[$comp->aroma_id])) {
                            $puntuaciones[$comp->aroma_id] += 0.5;
                        }
                    }
                } elseif ($entrada->formula && $entrada->calificacion <= 2) {
                    foreach ($entrada->formula->componentes as $comp) {
                        if (isset($puntuaciones[$comp->aroma_id])) {
                            $puntuaciones[$comp->aroma_id] = max(0.2, $puntuaciones[$comp->aroma_id] - 0.5);
                        }
                    }
                }
            }
        }

        // 4. NORMALIZACIÓN Y CORTE (< 10%)
        $totalPuntos = array_sum($puntuaciones);
        if ($totalPuntos <= 0) {
            $totalPuntos = count($puntuaciones);
            foreach ($puntuaciones as $id => $v) {
                $puntuaciones[$id] = 1.0;
            }
        }

        // Porcentajes iniciales
        $porcentajes = [];
        foreach ($puntuaciones as $id => $pts) {
            $porcentajes[$id] = ($pts / $totalPuntos) * 100;
        }

        // Filtrar aromas con menos del 10%
        $filtrados = array_filter($porcentajes, fn ($pct) => $pct >= 10.0);

        // Si todos cayeron por debajo del 10% (caso raro), tomar el aroma con mayor puntaje
        if (empty($filtrados)) {
            arsort($porcentajes);
            $topKey = array_key_first($porcentajes);
            $filtrados[$topKey] = 100.0;
        }

        // Renormalizar para que sumen exactamente 100
        $sumaFiltrados = array_sum($filtrados);
        $componentes = [];
        $acumulado = 0;
        $keys = array_keys($filtrados);
        $totalKeys = count($keys);

        foreach ($keys as $idx => $aromaId) {
            $aromaObj = $aromasSeguros[array_search($aromaId, array_column($aromasSeguros, 'id'))] ?? Aroma::find($aromaId);
            if ($idx === $totalKeys - 1) {
                // Último elemento absorbe cualquier residuo de redondeo
                $pctFinal = 100 - $acumulado;
            } else {
                $pctFinal = (int) round(($filtrados[$aromaId] / $sumaFiltrados) * 100);
                $acumulado += $pctFinal;
            }

            $componentes[] = [
                'aroma' => $aromaObj,
                'porcentaje' => max(5, $pctFinal),
            ];
        }

        // Ordenar de mayor a menor porcentaje
        usort($componentes, fn ($a, $b) => $b['porcentaje'] <=> $a['porcentaje']);

        // Ajustar exactamente a 100%
        $sumaFinal = array_sum(array_column($componentes, 'porcentaje'));
        if ($sumaFinal !== 100 && ! empty($componentes)) {
            $componentes[0]['porcentaje'] += (100 - $sumaFinal);
        }

        // 5. RITUAL SUGERIDO Y EXPLICACIÓN
        $intensidad = $context['intensidad'] ?? 'media';
        $gotas = match ($intensidad) {
            'suave' => 4,
            'intensa' => 9,
            default => 6,
        };

        $nombresAromas = array_map(fn ($c) => $c['aroma']->nombre." ({$c['porcentaje']}%)", $componentes);
        $listaNombres = implode(' + ', $nombresAromas);

        $objetivoTexto = match ($context['objetivo'] ?? '') {
            'concentrarme' => 'impulsar tu agudeza mental y claridad de pensamiento',
            'desacelerar' => 'liberar la sobrecarga sensorial y propiciar una calma profunda',
            'hacer_una_pausa' => 'restaurar tu equilibrio interior en un instante de pausa consciente',
            default => 'armonizar tu bienestar general',
        };

        $espacioNombre = $context['espacio_tipo'] ?? 'tu espacio';
        $momentoTexto = $context['momento'] ?? 'este momento';

        $explicacion = "Esta fórmula personalizada une {$listaNombres}. Ha sido calibrada para {$objetivoTexto} durante {$momentoTexto} en {$espacioNombre}, respetando rigurosamente tus parámetros de seguridad aromática.";

        $ritual = [
            'gotas' => $gotas,
            'duracion_min' => match ($context['objetivo'] ?? '') {
                'concentrarme' => 25,
                'desacelerar' => 20,
                default => 10,
            },
            'pasos' => [
                "1. Preparación: Agrega {$gotas} gotas de la mezcla en el difusor con agua limpia en {$espacioNombre}.",
                '2. Conexión táctil y olfativa: Inhala suavemente en 4 tiempos reteniendo 4 segundos y exhalando en 6.',
                '3. Inmersión: Permite que las notas aromáticas acompañen tu actividad sin forzar la mente.',
            ],
        ];

        return [
            'success' => true,
            'componentes' => $componentes,
            'explicacion' => $explicacion,
            'ritual' => $ritual,
            'version_reglas' => $versionReglas,
            'seguridad_analisis' => [
                'aromas_seguros' => count($aromasSeguros),
                'aromas_descartados' => count($motivosDescarte),
                'motivos' => $motivosDescarte,
            ],
        ];
    }
}

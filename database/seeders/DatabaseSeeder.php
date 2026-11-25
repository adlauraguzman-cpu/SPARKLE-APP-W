<?php

namespace Database\Seeders;

use App\Models\Aroma;
use App\Models\ComponenteFormula;
use App\Models\Consentimiento;
use App\Models\EntradaDiario;
use App\Models\Espacio;
use App\Models\Formula;
use App\Models\PerfilSeguridad;
use App\Models\PerfilSensorial;
use App\Models\RegistroAnimo;
use App\Models\RegistroCambio;
use App\Models\ReglaAfinidad;
use App\Models\Rutina;
use App\Models\Sesion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Usuarios Principales
        $usuario = User::firstOrCreate(
            ['email' => 'usuario@sparkle.com'],
            [
                'name' => 'María Torres',
                'password' => Hash::make('password'),
                'rol' => 'usuario',
                'zona_horaria' => 'America/Bogota',
                'activo' => true,
                'fecha_registro' => now()->subDays(14),
            ]
        );

        $especialista1 = User::firstOrCreate(
            ['email' => 'especialista1@sparkle.com'],
            [
                'name' => 'Dr. Carlos Especialista',
                'password' => Hash::make('password'),
                'rol' => 'especialista',
                'zona_horaria' => 'America/Bogota',
                'activo' => true,
                'fecha_registro' => now()->subMonths(3),
            ]
        );

        $especialista2 = User::firstOrCreate(
            ['email' => 'especialista2@sparkle.com'],
            [
                'name' => 'Dra. Elena Revisor',
                'password' => Hash::make('password'),
                'rol' => 'especialista',
                'zona_horaria' => 'America/Bogota',
                'activo' => true,
                'fecha_registro' => now()->subMonths(2),
            ]
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@sparkle.com'],
            [
                'name' => 'Laura Administradora',
                'password' => Hash::make('password'),
                'rol' => 'administrador',
                'zona_horaria' => 'America/Bogota',
                'activo' => true,
                'fecha_registro' => now()->subMonths(6),
            ]
        );

        // 2. Consentimientos para María
        foreach (['terminos', 'datos_personales', 'datos_bienestar'] as $tipo) {
            Consentimiento::create([
                'user_id' => $usuario->id,
                'tipo' => $tipo,
                'aceptado' => true,
                'version_del_texto' => '2.0',
                'fecha' => now()->subDays(14),
            ]);
        }

        // 3. Perfil Sensorial y de Seguridad de María
        PerfilSensorial::create([
            'user_id' => $usuario->id,
            'familias_preferidas' => ['Maderas', 'Floral'],
            'familias_evitadas' => [],
            'intensidad_preferida' => 'media',
        ]);

        PerfilSeguridad::create([
            'user_id' => $usuario->id,
            'alergias' => [],
            'mascotas_en_casa' => false,
            'tipo_mascotas' => [],
            'ninos_en_casa' => false,
            'embarazo_o_lactancia' => 'no',
        ]);

        // 4. Espacios de María
        $espacioEstudio = Espacio::create([
            'user_id' => $usuario->id,
            'nombre' => 'Estudio Creativo',
            'tipo' => 'estudio',
            'tamano' => 'medio',
            'ventilado' => true,
        ]);

        $espacioDormitorio = Espacio::create([
            'user_id' => $usuario->id,
            'nombre' => 'Dormitorio Principal',
            'tipo' => 'dormitorio',
            'tamano' => 'grande',
            'ventilado' => true,
        ]);

        // 5. Catálogo de Aromas Esenciales (ALPHA, HELIOS, JAZ)
        $alpha = Aroma::create([
            'nombre' => 'ALPHA',
            'familia' => 'Maderas',
            'notas' => 'Cedro Atlas, Vetiver de Haití, Sándalo Ambarino',
            'descripcion' => 'Notas profundas y amaderadas que brindan anclaje mental, propiciando concentración profunda y serenidad.',
            'advertencias_seguridad' => [
                'alergias' => ['cedro', 'maderas'],
                'mascotas_seguro' => true,
                'embarazo_seguro' => true,
            ],
            'activo' => true,
        ]);

        $helios = Aroma::create([
            'nombre' => 'HELIOS',
            'familia' => 'Cítricos',
            'notas' => 'Bergamota de Calabria, Mandarina Verde, Flor de Azahar',
            'descripcion' => 'Chispas cítricas solares que despiertan la energía, despejan la pesadez mental y dinamizan la mañana.',
            'advertencias_seguridad' => [
                'alergias' => ['citricos', 'bergamota', 'mandarina'],
                'mascotas_seguro' => false, // Gatos son sensibles a cítricos concentrados
                'embarazo_seguro' => true,
            ],
            'activo' => true,
        ]);

        $jaz = Aroma::create([
            'nombre' => 'JAZ',
            'familia' => 'Floral',
            'notas' => 'Jazmín Sambac, Lavanda Francesa, Nerolí Silvestre',
            'descripcion' => 'Bouquet floral reconfortante diseñado para desacelerar las pulsaciones, disolver el estrés y acoger el descanso.',
            'advertencias_seguridad' => [
                'alergias' => ['jazmin', 'lavanda', 'floral'],
                'mascotas_seguro' => true,
                'embarazo_seguro' => false, // Ciertas esencias florales restringidas en primer trimestre
            ],
            'activo' => true,
        ]);

        // 6. Reglas de Afinidad v1.0 (Publicadas)
        $reglasV1 = [
            // Objetivo
            ['aroma_id' => $alpha->id, 'variable' => 'objetivo', 'valor' => 'concentrarme', 'peso' => 3],
            ['aroma_id' => $alpha->id, 'variable' => 'objetivo', 'valor' => 'hacer_una_pausa', 'peso' => 2],
            ['aroma_id' => $helios->id, 'variable' => 'objetivo', 'valor' => 'concentrarme', 'peso' => 2],
            ['aroma_id' => $helios->id, 'variable' => 'objetivo', 'valor' => 'hacer_una_pausa', 'peso' => 3],
            ['aroma_id' => $jaz->id, 'variable' => 'objetivo', 'valor' => 'desacelerar', 'peso' => 3],
            ['aroma_id' => $jaz->id, 'variable' => 'objetivo', 'valor' => 'hacer_una_pausa', 'peso' => 2],

            // Espacio
            ['aroma_id' => $alpha->id, 'variable' => 'espacio', 'valor' => 'estudio', 'peso' => 3],
            ['aroma_id' => $helios->id, 'variable' => 'espacio', 'valor' => 'estudio', 'peso' => 2],
            ['aroma_id' => $helios->id, 'variable' => 'espacio', 'valor' => 'cocina', 'peso' => 3],
            ['aroma_id' => $jaz->id, 'variable' => 'espacio', 'valor' => 'dormitorio', 'peso' => 3],
            ['aroma_id' => $jaz->id, 'variable' => 'espacio', 'valor' => 'bano', 'peso' => 2],

            // Momento
            ['aroma_id' => $helios->id, 'variable' => 'momento', 'valor' => 'manana', 'peso' => 3],
            ['aroma_id' => $alpha->id, 'variable' => 'momento', 'valor' => 'tarde', 'peso' => 2],
            ['aroma_id' => $alpha->id, 'variable' => 'momento', 'valor' => 'manana', 'peso' => 2],
            ['aroma_id' => $jaz->id, 'variable' => 'momento', 'valor' => 'noche', 'peso' => 3],

            // Emoción
            ['aroma_id' => $jaz->id, 'variable' => 'emocion', 'valor' => 'estres', 'peso' => 3],
            ['aroma_id' => $jaz->id, 'variable' => 'emocion', 'valor' => 'cansancio', 'peso' => 2],
            ['aroma_id' => $alpha->id, 'variable' => 'emocion', 'valor' => 'calma', 'peso' => 2],
            ['aroma_id' => $alpha->id, 'variable' => 'emocion', 'valor' => 'enfoque', 'peso' => 3],
            ['aroma_id' => $helios->id, 'variable' => 'emocion', 'valor' => 'cansancio', 'peso' => 3],
            ['aroma_id' => $helios->id, 'variable' => 'emocion', 'valor' => 'energia', 'peso' => 3],
        ];

        foreach ($reglasV1 as $r) {
            ReglaAfinidad::create(array_merge($r, [
                'version' => 'v1.0',
                'estado' => 'publicada',
                'creado_por' => $especialista1->id,
                'aprobado_por' => $especialista2->id,
            ]));
        }

        // 7. Regla de Afinidad en borrador v2.0 (Maker-Checker Demo)
        ReglaAfinidad::create([
            'aroma_id' => $jaz->id,
            'variable' => 'objetivo',
            'valor' => 'hacer_una_pausa',
            'peso' => 3,
            'version' => 'v2.0',
            'estado' => 'en_revision',
            'creado_por' => $especialista1->id,
            'aprobado_por' => null,
        ]);

        // 8. Sesión y Fórmula Semilla para María
        $animo = RegistroAnimo::create([
            'user_id' => $usuario->id,
            'fecha_hora' => now()->subDays(5)->setHour(9)->setMinute(0),
            'emocion' => 'enfoque',
            'energia' => 4,
            'nota' => 'Lista para estructurar la semana con claridad mental.',
        ]);

        $sesion = Sesion::create([
            'user_id' => $usuario->id,
            'espacio_id' => $espacioEstudio->id,
            'animo_id' => $animo->id,
            'objetivo' => 'concentrarme',
            'momento' => 'manana',
            'intensidad' => 'media',
            'es_invitado' => false,
        ]);

        $formula = Formula::create([
            'sesion_id' => $sesion->id,
            'user_id' => $usuario->id,
            'nombre' => 'Enfoque Sereno v1',
            'version_de_reglas' => 'v1.0',
            'explicacion' => 'Fórmula balanceada con predominio amaderado (ALPHA 60%) para concentración estable y un toque energizante cítrico (HELIOS 40%) para agudeza matutina.',
            'ritual_sugerido' => [
                'gotas' => 6,
                'duracion_min' => 25,
                'pasos' => [
                    '1. Encender difusor en el Estudio Creativo con 6 gotas.',
                    '2. Tres respiraciones lentas inhalando el cedro y la bergamota.',
                    '3. Iniciar bloque de trabajo enfocado sin distracciones.',
                ],
            ],
            'estado' => 'guardada',
        ]);

        ComponenteFormula::create(['formula_id' => $formula->id, 'aroma_id' => $alpha->id, 'porcentaje' => 60]);
        ComponenteFormula::create(['formula_id' => $formula->id, 'aroma_id' => $helios->id, 'porcentaje' => 40]);

        // 9. Rutina activa para María
        $rutina = Rutina::create([
            'user_id' => $usuario->id,
            'formula_id' => $formula->id,
            'espacio_id' => $espacioEstudio->id,
            'nombre' => 'Ritual Matutino de Enfoque',
            'dias' => ['lun', 'mar', 'mie', 'jue', 'vie'],
            'hora' => '08:30',
            'duracion_minutos' => 20,
            'canal_recordatorio' => 'notificacion',
            'activa' => true,
        ]);

        // 10. Entradas de Diario Semilla (Demuestra Racha y Evolución de Ánimo)
        $diasAtras = [
            ['dias' => 4, 'antes' => 'estres', 'despues' => 'calma', 'calif' => 5, 'nota' => 'Excelente sesión, el cedro me centró mucho.'],
            ['dias' => 3, 'antes' => 'cansancio', 'despues' => 'energia', 'calif' => 4, 'nota' => 'El toque cítrico de Helios me activó.'],
            ['dias' => 2, 'antes' => 'estres', 'despues' => 'enfoque', 'calif' => 5, 'nota' => 'Pude redactar todo el informe con tranquilidad.'],
            ['dias' => 1, 'antes' => 'cansancio', 'despues' => 'calma', 'calif' => 5, 'nota' => 'Cierre de día suave y reconfortante.'],
        ];

        foreach ($diasAtras as $reg) {
            EntradaDiario::create([
                'user_id' => $usuario->id,
                'formula_id' => $formula->id,
                'rutina_id' => $rutina->id,
                'fecha' => now()->subDays($reg['dias'])->format('Y-m-d'),
                'emocion_antes' => $reg['antes'],
                'emocion_despues' => $reg['despues'],
                'energia_antes' => 2,
                'energia_despues' => 4,
                'calificacion' => $reg['calif'],
                'nota' => $reg['nota'],
                'etiquetas' => ['enfoque', 'productividad', 'manana'],
            ]);
        }

        // 11. Registro de Cambios para Auditoría Admin
        RegistroCambio::create([
            'tipo_entidad' => 'ReglaAfinidad',
            'entidad_id' => 1,
            'user_id' => $especialista1->id,
            'accion' => 'publicar',
            'fecha' => now()->subDays(10),
            'comentario' => 'Publicación de reglas base v1.0 para lanzamiento',
            'detalles' => ['version' => 'v1.0', 'reglas_count' => count($reglasV1)],
        ]);

        RegistroCambio::create([
            'tipo_entidad' => 'ReglaAfinidad',
            'entidad_id' => count($reglasV1) + 1,
            'user_id' => $especialista1->id,
            'accion' => 'enviar_revision',
            'fecha' => now()->subHours(6),
            'comentario' => 'Propuesta de aumento de peso para JAZ en pausa consciente v2.0',
            'detalles' => ['version' => 'v2.0', 'aroma' => 'JAZ', 'variable' => 'objetivo', 'peso' => 3],
        ]);
    }
}

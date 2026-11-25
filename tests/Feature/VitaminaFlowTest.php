<?php

namespace Tests\Feature;

use App\Models\Formula;
use App\Models\ReglaAfinidad;
use App\Models\Rutina;
use App\Models\User;
use App\Services\FormulaEngineService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VitaminaFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_landing_page_and_aromas_are_accessible(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('SPARKLE');
        $response->assertSee('Mi Espacio Vitamina');
        $response->assertSee('ALPHA');
        $response->assertSee('HELIOS');
        $response->assertSee('JAZ');

        $aromasResponse = $this->get('/aromas');
        $aromasResponse->assertStatus(200);
        $aromasResponse->assertSee('Catálogo de Aromas y Fichas de Seguridad');
    }

    public function test_guest_can_calculate_formula_in_demo_mode(): void
    {
        $response = $this->post('/configurador/calcular', [
            'objetivo' => 'concentrarme',
            'momento' => 'manana',
            'intensidad' => 'media',
            'espacio_tipo' => 'estudio',
            'emocion' => 'enfoque',
            'energia' => 4,
        ]);

        $response->assertStatus(200);
        $response->assertSee('Fórmula Calibrada para tu Sesión');
        $response->assertSee('Proporciones de la Mezcla');
        $response->assertSee('Ritual Sensorial Sugerido');
    }

    public function test_formula_engine_discards_contraindicated_aromas_due_to_allergies(): void
    {
        $user = User::where('email', 'usuario@sparkle.com')->first();

        // Actualizar perfil de seguridad para que tenga alergia a bergamota/cítricos
        $user->perfilSeguridad->update([
            'alergias' => ['citricos', 'bergamota'],
        ]);

        $engine = app(FormulaEngineService::class);
        $resultado = $engine->calcular([
            'objetivo' => 'concentrarme',
            'momento' => 'manana',
            'intensidad' => 'media',
        ], $user);

        $this->assertTrue($resultado['success']);
        // Verificar que HELIOS fue descartado y no aparece en los componentes
        $nombres = array_map(fn ($c) => $c['aroma']->nombre, $resultado['componentes']);
        $this->assertNotContains('HELIOS', $nombres);
        $this->assertContains('ALPHA', $nombres);
    }

    public function test_formula_engine_triggers_error_variant_when_no_safe_aromas_remain(): void
    {
        $user = User::where('email', 'usuario@sparkle.com')->first();

        // Configurar alergias y restricciones para descartar todos los aromas:
        // ALPHA: alergia a maderas/cedro
        // HELIOS: alergia a citricos
        // JAZ: embarazo=si (embarazo_seguro=false)
        $user->perfilSeguridad->update([
            'alergias' => ['cedro', 'citricos'],
            'embarazo_o_lactancia' => 'si',
        ]);

        $engine = app(FormulaEngineService::class);
        $resultado = $engine->calcular([
            'objetivo' => 'concentrarme',
            'momento' => 'manana',
            'intensidad' => 'media',
        ], $user);

        $this->assertFalse($resultado['success']);
        $this->assertEquals('SIN_FORMULA_SEGURA', $resultado['error_code']);
    }

    public function test_formula_percentages_always_sum_to_100_and_discard_under_10_percent(): void
    {
        $engine = app(FormulaEngineService::class);
        $resultado = $engine->calcular([
            'objetivo' => 'desacelerar',
            'momento' => 'noche',
            'intensidad' => 'intensa',
            'espacio_tipo' => 'dormitorio',
            'emocion' => 'estres',
        ]);

        $this->assertTrue($resultado['success']);
        $suma = array_sum(array_column($resultado['componentes'], 'porcentaje'));
        $this->assertEquals(100, $suma);

        foreach ($resultado['componentes'] as $comp) {
            $this->assertGreaterThanOrEqual(10, $comp['porcentaje']);
        }
    }

    public function test_user_registration_requires_mandatory_consent_checkboxes(): void
    {
        // Intentar registrarse sin consentimientos
        $response = $this->post('/register', [
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo@sparkle.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['consent_terminos', 'consent_datos_personales', 'consent_bienestar']);

        // Con consentimientos aprobados
        $successResponse = $this->post('/register', [
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo@sparkle.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'consent_terminos' => '1',
            'consent_datos_personales' => '1',
            'consent_bienestar' => '1',
        ]);

        $successResponse->assertRedirect(route('onboarding.index'));
        $this->assertDatabaseHas('users', ['email' => 'nuevo@sparkle.com']);
        $this->assertDatabaseHas('consentimientos', ['tipo' => 'datos_bienestar', 'aceptado' => true]);
    }

    public function test_validation_errors_are_shown_in_spanish(): void
    {
        $response = $this->from('/register')
            ->followingRedirects()
            ->post('/register', [
                'name' => 'Nuevo Usuario',
                'email' => 'usuario@sparkle.com',
                'password' => 'corta',
                'password_confirmation' => 'corta',
            ]);

        $response->assertSee('Revisa los siguientes datos:');
        $response->assertSee('Ya existe un registro con ese correo electrónico.');
        $response->assertSee('El campo contraseña debe tener al menos 8 caracteres.');
    }

    public function test_user_can_save_calculated_formula_and_create_routine(): void
    {
        $user = User::where('email', 'usuario@sparkle.com')->first();
        $this->actingAs($user);

        $response = $this->post('/configurador/guardar', [
            'nombre' => 'Enfoque de Prueba',
            'objetivo' => 'concentrarme',
            'momento' => 'manana',
            'intensidad' => 'media',
            'emocion' => 'calma',
            'energia' => 4,
            'explicacion' => 'Fórmula para prueba automatizada',
            'ritual_gotas' => 6,
            'ritual_duracion' => 15,
            'componentes' => [
                ['aroma_id' => 1, 'porcentaje' => 60],
                ['aroma_id' => 2, 'porcentaje' => 40],
            ],
        ]);

        $response->assertRedirect(route('rutinas.index'));
        $this->assertDatabaseHas('formulas', ['nombre' => 'Enfoque de Prueba', 'user_id' => $user->id]);

        $formula = Formula::where('nombre', 'Enfoque de Prueba')->first();

        // Crear una rutina para esta fórmula
        $rutinaResponse = $this->post('/rutinas', [
            'formula_id' => $formula->id,
            'nombre' => 'Rutina Diaria de Concentración',
            'dias' => ['lun', 'mar', 'mie'],
            'hora' => '09:00',
            'duracion_minutos' => 20,
            'canal_recordatorio' => 'notificacion',
        ]);

        $rutinaResponse->assertRedirect(route('rutinas.index'));
        $this->assertDatabaseHas('rutinas', ['nombre' => 'Rutina Diaria de Concentración', 'user_id' => $user->id]);
    }

    public function test_user_can_simulate_routine_reminder_notification(): void
    {
        $user = User::where('email', 'usuario@sparkle.com')->first();
        $this->actingAs($user);

        $rutina = $user->rutinas()->first();
        $response = $this->post(route('rutinas.simular', $rutina));

        $response->assertSessionHas('recordatorio_simulado');
        $this->assertDatabaseHas('recordatorios', ['rutina_id' => $rutina->id, 'estado' => 'enviado']);
    }

    public function test_user_diary_entry_updates_streak_and_influences_algorithm(): void
    {
        $user = User::where('email', 'usuario@sparkle.com')->first();
        $this->actingAs($user);

        $formula = $user->formulas()->first();

        $response = $this->post('/diario', [
            'fecha' => now()->format('Y-m-d'),
            'formula_id' => $formula->id,
            'emocion_antes' => 'estres',
            'emocion_despues' => 'calma',
            'energia_antes' => 2,
            'energia_despues' => 5,
            'calificacion' => 5,
            'nota' => 'Excelente experiencia sensorial de prueba.',
            'etiquetas' => 'prueba, enfoque, bienestar',
        ]);

        $response->assertRedirect(route('diario.index', ['estado' => 'success']));
        $this->assertDatabaseHas('entradas_diario', [
            'user_id' => $user->id,
            'calificacion' => 5,
            'emocion_despues' => 'calma',
        ]);
    }

    public function test_specialist_maker_checker_rule_cannot_be_approved_by_author(): void
    {
        $especialista1 = User::where('email', 'especialista1@sparkle.com')->first();
        $this->actingAs($especialista1);

        // Crear una regla propuesta por especialista1
        $regla = ReglaAfinidad::create([
            'aroma_id' => 1,
            'variable' => 'objetivo',
            'valor' => 'concentrarme',
            'peso' => 3,
            'version' => 'v2.0',
            'estado' => 'en_revision',
            'creado_por' => $especialista1->id,
            'aprobado_por' => null,
        ]);

        // Intentar que el mismo especialista1 la apruebe (violación Maker-Checker)
        $response = $this->post(route('especialista.reglas.aprobar', $regla));
        $response->assertSessionHas('maker_checker_error');

        // La regla NO debe quedar publicada
        $regla->refresh();
        $this->assertEquals('en_revision', $regla->estado);
        $this->assertNull($regla->aprobado_por);
    }

    public function test_specialist_maker_checker_rule_can_be_approved_by_second_specialist(): void
    {
        $especialista1 = User::where('email', 'especialista1@sparkle.com')->first();
        $especialista2 = User::where('email', 'especialista2@sparkle.com')->first();

        // Regla propuesta por Dr. Carlos (especialista 1)
        $regla = ReglaAfinidad::create([
            'aroma_id' => 1,
            'variable' => 'momento',
            'valor' => 'manana',
            'peso' => 3,
            'version' => 'v2.0',
            'estado' => 'en_revision',
            'creado_por' => $especialista1->id,
            'aprobado_por' => null,
        ]);

        // Inicia sesión Dra. Elena (especialista 2 / Checker)
        $this->actingAs($especialista2);
        $response = $this->post(route('especialista.reglas.aprobar', $regla));

        $response->assertRedirect(route('especialista.index', ['estado' => 'success']));

        $regla->refresh();
        $this->assertEquals('publicada', $regla->estado);
        $this->assertEquals($especialista2->id, $regla->aprobado_por);
    }

    public function test_admin_cannot_access_individual_diaries_and_non_admin_cannot_access_admin_panel(): void
    {
        $usuario = User::where('email', 'usuario@sparkle.com')->first();
        $admin = User::where('email', 'admin@sparkle.com')->first();

        // 1. Usuario normal intenta acceder a /admin -> 403 Forbidden
        $this->actingAs($usuario);
        $forbiddenResponse = $this->get('/admin');
        $forbiddenResponse->assertStatus(403);

        // 2. Administrador accede a /admin -> 200 OK
        $this->actingAs($admin);
        $adminResponse = $this->get('/admin');
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('Gobernanza, Roles y Auditoría de Seguridad');
        $adminResponse->assertSee('Principio de Privacidad por Diseño');

        // 3. Verificar que no existe ninguna ruta para que admin lea entradas personales de diario ajenas
        $this->assertFalse(\Route::has('admin.diario'));
        $this->assertFalse(\Route::has('admin.diarios'));
        $this->assertFalse(\Route::has('admin.perfil_medico'));
    }
}

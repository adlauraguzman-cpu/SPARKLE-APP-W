<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Consentimientos
        Schema::create('consentimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('tipo'); // terminos, datos_personales, datos_bienestar
            $table->boolean('aceptado')->default(true);
            $table->string('version_del_texto')->default('1.0');
            $table->timestamp('fecha')->useCurrent();
            $table->timestamps();
        });

        // 2. Perfiles Sensoriales
        Schema::create('perfiles_sensoriales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->json('familias_preferidas')->nullable();
            $table->json('familias_evitadas')->nullable();
            $table->string('intensidad_preferida')->default('media'); // suave, media, intensa
            $table->timestamps();
        });

        // 3. Perfiles de Seguridad
        Schema::create('perfiles_seguridad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->json('alergias')->nullable();
            $table->boolean('mascotas_en_casa')->default(false);
            $table->json('tipo_mascotas')->nullable();
            $table->boolean('ninos_en_casa')->default(false);
            $table->string('embarazo_o_lactancia')->default('no'); // si, no, prefiero_no_decir
            $table->timestamps();
        });

        // 4. Espacios
        Schema::create('espacios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nombre');
            $table->string('tipo'); // dormitorio, estudio, sala, bano, cocina, otro
            $table->string('tamano')->default('medio'); // pequeno, medio, grande
            $table->boolean('ventilado')->default(true);
            $table->timestamps();
        });

        // 5. Registros de Ánimo
        Schema::create('registros_animo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('fecha_hora')->useCurrent();
            $table->string('emocion'); // estres, cansancio, calma, enfoque, energia
            $table->unsignedTinyInteger('energia')->default(3); // 1-5
            $table->text('nota')->nullable();
            $table->timestamps();
        });

        // 6. Aromas
        Schema::create('aromas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // ALPHA, HELIOS, JAZ
            $table->string('familia'); // Maderas, Cítricos, Floral, etc.
            $table->text('notas')->nullable();
            $table->text('descripcion');
            $table->json('advertencias_seguridad')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // 7. Reglas de Afinidad
        Schema::create('reglas_afinidad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aroma_id')->constrained('aromas')->cascadeOnDelete();
            $table->string('variable'); // objetivo, espacio, momento, emocion
            $table->string('valor');
            $table->unsignedTinyInteger('peso')->default(1); // 0-3
            $table->string('version')->default('v1.0');
            $table->string('estado')->default('publicada'); // borrador, en_revision, publicada
            $table->foreignId('creado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('aprobado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 8. Sesiones
        Schema::create('sesiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('espacio_id')->nullable()->constrained('espacios')->nullOnDelete();
            $table->foreignId('animo_id')->nullable()->constrained('registros_animo')->nullOnDelete();
            $table->string('objetivo'); // concentrarme, desacelerar, hacer_una_pausa
            $table->string('momento'); // manana, tarde, noche
            $table->string('intensidad')->default('media'); // suave, media, intensa
            $table->boolean('es_invitado')->default(false);
            $table->timestamps();
        });

        // 9. Fórmulas
        Schema::create('formulas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesion_id')->nullable()->constrained('sesiones')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('nombre');
            $table->string('version_de_reglas')->default('v1.0');
            $table->text('explicacion')->nullable();
            $table->json('ritual_sugerido')->nullable();
            $table->string('estado')->default('guardada'); // guardada, archivada
            $table->timestamps();
        });

        // 10. Componentes de Fórmula
        Schema::create('componentes_formula', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formula_id')->constrained('formulas')->cascadeOnDelete();
            $table->foreignId('aroma_id')->constrained('aromas')->cascadeOnDelete();
            $table->unsignedInteger('porcentaje'); // 0 - 100
            $table->timestamps();
        });

        // 11. Rutinas
        Schema::create('rutinas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('formula_id')->constrained('formulas')->cascadeOnDelete();
            $table->foreignId('espacio_id')->nullable()->constrained('espacios')->nullOnDelete();
            $table->string('nombre');
            $table->json('dias'); // ['lun', 'mar', 'mie', ...]
            $table->string('hora', 10)->default('08:00');
            $table->unsignedInteger('duracion_minutos')->default(15);
            $table->string('canal_recordatorio')->default('notificacion'); // notificacion, correo, ninguno
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });

        // 12. Recordatorios
        Schema::create('recordatorios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rutina_id')->constrained('rutinas')->cascadeOnDelete();
            $table->dateTime('fecha_hora');
            $table->string('estado')->default('enviado'); // enviado, atendido, pospuesto
            $table->timestamps();
        });

        // 13. Entradas de Diario
        Schema::create('entradas_diario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('formula_id')->nullable()->constrained('formulas')->nullOnDelete();
            $table->foreignId('rutina_id')->nullable()->constrained('rutinas')->nullOnDelete();
            $table->date('fecha');
            $table->string('emocion_antes'); // estres, cansancio, calma, enfoque, energia
            $table->string('emocion_despues'); // estres, cansancio, calma, enfoque, energia
            $table->unsignedTinyInteger('energia_antes')->default(3);
            $table->unsignedTinyInteger('energia_despues')->default(3);
            $table->unsignedTinyInteger('calificacion')->default(5); // 1-5
            $table->text('nota')->nullable();
            $table->json('etiquetas')->nullable();
            $table->timestamps();
        });

        // 14. Registros de Cambios (Auditoría)
        Schema::create('registros_cambios', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_entidad');
            $table->unsignedBigInteger('entidad_id');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('accion'); // crear, modificar, enviar_revision, aprobar, rechazar, archivar, revocar_consentimiento
            $table->timestamp('fecha')->useCurrent();
            $table->text('comentario')->nullable();
            $table->json('detalles')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registros_cambios');
        Schema::dropIfExists('entradas_diario');
        Schema::dropIfExists('recordatorios');
        Schema::dropIfExists('rutinas');
        Schema::dropIfExists('componentes_formula');
        Schema::dropIfExists('formulas');
        Schema::dropIfExists('sesiones');
        Schema::dropIfExists('reglas_afinidad');
        Schema::dropIfExists('aromas');
        Schema::dropIfExists('registros_animo');
        Schema::dropIfExists('espacios');
        Schema::dropIfExists('perfiles_seguridad');
        Schema::dropIfExists('perfiles_sensoriales');
        Schema::dropIfExists('consentimientos');
    }
};

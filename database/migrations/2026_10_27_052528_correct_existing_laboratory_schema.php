<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasIndex('horarios', 'horarios_laboratorio_dia_index')) {
            Schema::table('horarios', function (Blueprint $table) {
                $table->index(
                    ['laboratorio_id', 'dia_semana'],
                    'horarios_laboratorio_dia_index'
                );
            });
        }

        if (!Schema::hasIndex('practicas', 'practicas_laboratorio_estado_index')) {
            Schema::table('practicas', function (Blueprint $table) {
                $table->index(
                    ['laboratorio_id', 'estado'],
                    'practicas_laboratorio_estado_index'
                );
            });
        }

        if (!Schema::hasIndex('reservas_laboratorio', 'reservas_laboratorio_fechas_index')) {
            Schema::table('reservas_laboratorio', function (Blueprint $table) {
                $table->index(
                    ['laboratorio_id', 'inicio', 'fin'],
                    'reservas_laboratorio_fechas_index'
                );
            });
        }

        if (!Schema::hasIndex('reservas_laboratorio', 'reservas_laboratorio_usuario_estado_index')) {
            Schema::table('reservas_laboratorio', function (Blueprint $table) {
                $table->index(
                    ['user_id', 'estado'],
                    'reservas_laboratorio_usuario_estado_index'
                );
            });
        }

        if (!Schema::hasIndex('asignacion_equipos', 'asignacion_equipos_equipo_estado_index')) {
            Schema::table('asignacion_equipos', function (Blueprint $table) {
                $table->index(
                    ['equipo_id', 'estado'],
                    'asignacion_equipos_equipo_estado_index'
                );
            });
        }

        if (!Schema::hasIndex('movimiento_equipos', 'movimiento_equipos_equipo_fecha_index')) {
            Schema::table('movimiento_equipos', function (Blueprint $table) {
                $table->index(
                    ['equipo_id', 'fecha_movimiento'],
                    'movimiento_equipos_equipo_fecha_index'
                );
            });
        }

        if (!Schema::hasIndex('incidencias', 'incidencias_estado_prioridad_index')) {
            Schema::table('incidencias', function (Blueprint $table) {
                $table->index(
                    ['estado', 'prioridad'],
                    'incidencias_estado_prioridad_index'
                );
            });
        }

        if (!Schema::hasIndex('procesos', 'procesos_tipo_estado_index')) {
            Schema::table('procesos', function (Blueprint $table) {
                $table->index(
                    ['tipo', 'estado'],
                    'procesos_tipo_estado_index'
                );
            });
        }

        if (!Schema::hasIndex('disponibilidad', 'disponibilidad_fechas_index')) {
            Schema::table('disponibilidad', function (Blueprint $table) {
                $table->index(
                    ['inicio', 'fin'],
                    'disponibilidad_fechas_index'
                );
            });
        }

        if (!Schema::hasIndex('movimientos', 'movimientos_entidad_index')) {
            Schema::table('movimientos', function (Blueprint $table) {
                $table->index(
                    ['entidad', 'entidad_id'],
                    'movimientos_entidad_index'
                );
            });
        }

        if (!Schema::hasIndex('bitacoras', 'bitacoras_modulo_fecha_index')) {
            Schema::table('bitacoras', function (Blueprint $table) {
                $table->index(
                    ['modulo', 'fecha'],
                    'bitacoras_modulo_fecha_index'
                );
            });
        }

        if (!Schema::hasIndex('licencias', 'licencias_software_estado_index')) {
            Schema::table('licencias', function (Blueprint $table) {
                $table->index(
                    ['software_id', 'estado'],
                    'licencias_software_estado_index'
                );
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasIndex('licencias', 'licencias_software_estado_index')) {
            Schema::table('licencias', function (Blueprint $table) {
                $table->dropIndex('licencias_software_estado_index');
            });
        }

        if (Schema::hasIndex('bitacoras', 'bitacoras_modulo_fecha_index')) {
            Schema::table('bitacoras', function (Blueprint $table) {
                $table->dropIndex('bitacoras_modulo_fecha_index');
            });
        }

        if (Schema::hasIndex('movimientos', 'movimientos_entidad_index')) {
            Schema::table('movimientos', function (Blueprint $table) {
                $table->dropIndex('movimientos_entidad_index');
            });
        }

        if (Schema::hasIndex('disponibilidad', 'disponibilidad_fechas_index')) {
            Schema::table('disponibilidad', function (Blueprint $table) {
                $table->dropIndex('disponibilidad_fechas_index');
            });
        }

        if (Schema::hasIndex('procesos', 'procesos_tipo_estado_index')) {
            Schema::table('procesos', function (Blueprint $table) {
                $table->dropIndex('procesos_tipo_estado_index');
            });
        }

        if (Schema::hasIndex('incidencias', 'incidencias_estado_prioridad_index')) {
            Schema::table('incidencias', function (Blueprint $table) {
                $table->dropIndex('incidencias_estado_prioridad_index');
            });
        }

        if (Schema::hasIndex('movimiento_equipos', 'movimiento_equipos_equipo_fecha_index')) {
            Schema::table('movimiento_equipos', function (Blueprint $table) {
                $table->dropIndex('movimiento_equipos_equipo_fecha_index');
            });
        }

        if (Schema::hasIndex('asignacion_equipos', 'asignacion_equipos_equipo_estado_index')) {
            Schema::table('asignacion_equipos', function (Blueprint $table) {
                $table->dropIndex('asignacion_equipos_equipo_estado_index');
            });
        }

        if (Schema::hasIndex('reservas_laboratorio', 'reservas_laboratorio_usuario_estado_index')) {
            Schema::table('reservas_laboratorio', function (Blueprint $table) {
                $table->dropIndex('reservas_laboratorio_usuario_estado_index');
            });
        }

        if (Schema::hasIndex('reservas_laboratorio', 'reservas_laboratorio_fechas_index')) {
            Schema::table('reservas_laboratorio', function (Blueprint $table) {
                $table->dropIndex('reservas_laboratorio_fechas_index');
            });
        }

        if (Schema::hasIndex('practicas', 'practicas_laboratorio_estado_index')) {
            Schema::table('practicas', function (Blueprint $table) {
                $table->dropIndex('practicas_laboratorio_estado_index');
            });
        }

        if (Schema::hasIndex('horarios', 'horarios_laboratorio_dia_index')) {
            Schema::table('horarios', function (Blueprint $table) {
                $table->dropIndex('horarios_laboratorio_dia_index');
            });
        }
    }
};

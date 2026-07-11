<?php

namespace App\Services;

/**
 * SanitizerService
 * ----------------
 * Clase de sanitización con métodos estáticos (Punto 29).
 * Se encarga de "limpiar" y normalizar datos de entrada:
 * quitar espacios, escapar caracteres, y homogeneizar formato (Punto 30).
 */
class SanitizerService
{
    /**
     * Limpia espacios en blanco al inicio/final y colapsa espacios internos.
     */
    public static function trim(string $value): string
    {
        return trim(preg_replace('/\s+/', ' ', $value));
    }

    /**
     * Convierte texto a Formato Título: "juan carlos" -> "Juan Carlos"
     * Requisito Punto 2, 3 y 30 (Nombre y Apellido).
     */
    public static function toTitleCase(string $value): string
    {
        $value = self::trim($value);
        return mb_convert_case(mb_strtolower($value, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Sanitiza texto plano contra HTML/JS malicioso (prevención XSS).
     */
    public static function sanitizeString(string $value): string
    {
        $value = self::trim($value);
        return htmlspecialchars(strip_tags($value), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitiza un correo electrónico.
     */
    public static function sanitizeEmail(string $value): string
    {
        $value = strtolower(self::trim($value));
        return filter_var($value, FILTER_SANITIZE_EMAIL);
    }

    /**
     * Sanitiza un número de celular dejando solo dígitos, +, y guiones.
     */
    public static function sanitizeCelular(string $value): string
    {
        return preg_replace('/[^0-9+\-\s]/', '', self::trim($value));
    }

    /**
     * Sanitiza un identificador tipo cédula, dejando letras, números y guiones.
     */
    public static function sanitizeIdentidad(string $value): string
    {
        return preg_replace('/[^A-Za-z0-9\-]/', '', self::trim($value));
    }

    /**
     * Fuerza un valor a entero seguro.
     */
    public static function toInt($value): int
    {
        return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Fuerza un valor numérico decimal seguro (ej. salario).
     */
    public static function toFloat($value): float
    {
        $clean = preg_replace('/[^0-9.\-]/', '', (string) $value);
        return (float) $clean;
    }

    /**
     * Sanitiza un arreglo completo de datos de colaborador.
     */
    public static function sanitizeColaborador(array $data): array
    {
        return [
            'identidad'      => self::sanitizeIdentidad($data['identidad'] ?? ''),
            'nombre'         => self::toTitleCase($data['nombre'] ?? ''),
            'apellido'       => self::toTitleCase($data['apellido'] ?? ''),
            'edad'           => self::toInt($data['edad'] ?? 0),
            'tipo_sangre_id' => self::toInt($data['tipo_sangre_id'] ?? 0),
            'sexo_id'        => self::toInt($data['sexo_id'] ?? 0),
            'nacionalidad'   => self::sanitizeString($data['nacionalidad'] ?? ''),
            'ruta_id'        => self::toInt($data['ruta_id'] ?? 0),
            'correo'         => self::sanitizeEmail($data['correo'] ?? ''),
            'celular'        => self::sanitizeCelular($data['celular'] ?? ''),
        ];
    }

    /**
     * Sanitiza un arreglo completo de datos de perfil laboral.
     */
    public static function sanitizePerfilLaboral(array $data): array
    {
        return [
            'colaborador_id'   => self::toInt($data['colaborador_id'] ?? 0),
            'ocupacion_id'     => self::toInt($data['ocupacion_id'] ?? 0),
            'tipo_planilla_id' => self::toInt($data['tipo_planilla_id'] ?? 0),
            'tipo_empleado_id' => self::toInt($data['tipo_empleado_id'] ?? 0),
            'salario'          => self::toFloat($data['salario'] ?? 0),
            'fecha_inicio'     => self::sanitizeString($data['fecha_inicio'] ?? ''),
            'fecha_fin'        => empty($data['fecha_fin']) ? null : self::sanitizeString($data['fecha_fin']),
            'motivo_baja_id'   => empty($data['motivo_baja_id']) ? null : self::toInt($data['motivo_baja_id']),
        ];
    }
}

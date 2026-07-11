<?php

namespace App\Services;

/**
 * ValidationService
 * -----------------
 * Clase de validación con métodos estáticos (Orientado a Objetos, Punto 28).
 * Cada método regresa bool o un mensaje de error específico.
 * No modifica datos, solo verifica si cumplen una regla.
 */
class ValidationService
{
    public static function isRequired($value): bool
    {
        return !(is_null($value) || trim((string)$value) === '');
    }

    public static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Valida celular con formato panameño, ej: +507 6000-0000 o 6000-0000
     */
    public static function isValidCelular(string $celular): bool
    {
        return (bool) preg_match('/^(\+507\s?)?[2-9]\d{3}-?\d{4}$/', trim($celular));
    }

    public static function isValidIdentidad(string $identidad): bool
    {
        // Cédula panameña genérica: letras, números, guiones (ej. 8-123-4567 / PE-1-123)
        return (bool) preg_match('/^[A-Za-z0-9]+(-[A-Za-z0-9]+)+$/', trim($identidad));
    }

    public static function isInRange($value, int $min, int $max): bool
    {
        return is_numeric($value) && $value >= $min && $value <= $max;
    }

    public static function isValidEdad($edad, int $min = 18, int $max = 65): bool
    {
        return self::isInRange($edad, $min, $max);
    }

    public static function isPositiveNumber($value): bool
    {
        return is_numeric($value) && $value > 0;
    }

    public static function isValidDate(string $date, string $format = 'Y-m-d'): bool
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Verifica que fecha_fin sea posterior a fecha_inicio (si existe).
     */
    public static function isFechaFinValida(?string $fechaInicio, ?string $fechaFin): bool
    {
        if (empty($fechaFin)) {
            return true; // fecha_fin es opcional
        }
        if (empty($fechaInicio)) {
            return false;
        }
        return strtotime($fechaFin) >= strtotime($fechaInicio);
    }

    public static function isInEnum($value, array $allowed): bool
    {
        return in_array($value, $allowed, true);
    }

    public static function maxLength(string $value, int $max): bool
    {
        return mb_strlen($value) <= $max;
    }

    /**
     * Valida un conjunto de datos del formulario de colaborador.
     * Devuelve un arreglo de errores (vacío si todo es válido).
     */
    public static function validateColaborador(array $data): array
    {
        $errors = [];

        if (!self::isRequired($data['identidad'] ?? null)) {
            $errors['identidad'] = 'La identidad es obligatoria.';
        }
        if (!self::isRequired($data['nombre'] ?? null)) {
            $errors['nombre'] = 'El nombre es obligatorio.';
        }
        if (!self::isRequired($data['apellido'] ?? null)) {
            $errors['apellido'] = 'El apellido es obligatorio.';
        }
        if (!self::isValidEdad($data['edad'] ?? null)) {
            $errors['edad'] = 'La edad debe estar entre 18 y 65 años.';
        }
        if (!self::isRequired($data['tipo_sangre_id'] ?? null)) {
            $errors['tipo_sangre_id'] = 'Debe seleccionar un tipo de sangre.';
        }
        if (!self::isRequired($data['sexo_id'] ?? null)) {
            $errors['sexo_id'] = 'Debe seleccionar el sexo.';
        }
        if (!self::isRequired($data['nacionalidad'] ?? null)) {
            $errors['nacionalidad'] = 'La nacionalidad es obligatoria.';
        }
        if (!self::isRequired($data['ruta_id'] ?? null)) {
            $errors['ruta_id'] = 'Debe seleccionar una ruta.';
        }
        if (!self::isValidEmail($data['correo'] ?? '')) {
            $errors['correo'] = 'El formato del correo no es válido.';
        }
        if (!self::isValidCelular($data['celular'] ?? '')) {
            $errors['celular'] = 'El formato del celular no es válido (ej. 6000-0000).';
        }

        return $errors;
    }

    /**
     * Valida un conjunto de datos del formulario de perfil laboral.
     */
    public static function validatePerfilLaboral(array $data): array
    {
        $errors = [];

        if (!self::isRequired($data['ocupacion_id'] ?? null)) {
            $errors['ocupacion_id'] = 'Debe seleccionar una ocupación.';
        }
        if (!self::isRequired($data['tipo_planilla_id'] ?? null)) {
            $errors['tipo_planilla_id'] = 'Debe seleccionar un tipo de planilla.';
        }
        if (!self::isPositiveNumber($data['salario'] ?? null)) {
            $errors['salario'] = 'El salario debe ser un número positivo.';
        }
        if (!self::isValidDate($data['fecha_inicio'] ?? '')) {
            $errors['fecha_inicio'] = 'La fecha de inicio no es válida.';
        }
        if (!empty($data['fecha_fin']) && !self::isValidDate($data['fecha_fin'])) {
            $errors['fecha_fin'] = 'La fecha de fin no es válida.';
        }
        if (!self::isFechaFinValida($data['fecha_inicio'] ?? null, $data['fecha_fin'] ?? null)) {
            $errors['fecha_fin'] = 'La fecha de fin no puede ser anterior a la fecha de inicio.';
        }
        if (empty($data['fecha_fin']) === false && !self::isRequired($data['motivo_baja'] ?? null)) {
            $errors['motivo_baja'] = 'Debe indicar el motivo de baja.';
        }

        return $errors;
    }
}

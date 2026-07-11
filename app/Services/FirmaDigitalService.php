<?php

namespace App\Services;

/**
 * FirmaDigitalService
 * -------------------
 * Genera y verifica la firma digital de los datos sensibles del perfil
 * laboral usando OpenSSL (criptografía asimétrica RSA), tal como exige
 * la rúbrica (Puntos 16 y 27):
 *   Salario, CódigoEmpleado, Tipo de Empleado, Planilla, Ocupación, Fecha_Inicio
 *
 * Cómo funciona:
 * 1. Al guardar un perfil laboral, se arma una cadena canónica con los
 *    datos sensibles y se firma con la LLAVE PRIVADA (openssl_sign).
 *    La firma resultante (binaria) se guarda en la BD codificada en base64.
 * 2. Al mostrar el reporte, se reconstruye la misma cadena con los datos
 *    actuales y se verifica la firma con la LLAVE PÚBLICA (openssl_verify).
 *      - Verifica OK  -> verde  (integridad validada, nadie tocó los datos)
 *      - Verifica MAL -> rojo   (los datos fueron alterados después de firmarse)
 *
 * Las llaves viven en app/Config/keys/ (private_key.pem y public_key.pem).
 * En un entorno real, private_key.pem NUNCA debería subirse a un repo público;
 * para efectos del parcial se incluye para que el proyecto funcione "out of the box".
 */
class FirmaDigitalService
{
    private const RUTA_LLAVE_PRIVADA = __DIR__ . '/../Config/keys/private_key.pem';
    private const RUTA_LLAVE_PUBLICA = __DIR__ . '/../Config/keys/public_key.pem';

    /**
     * Construye la cadena canónica a partir de los campos sensibles.
     * El orden de los campos SIEMPRE debe ser el mismo para que la
     * verificación sea consistente.
     */
    private static function buildPayload(array $datos): string
    {
        return implode('|', [
            'salario='         . number_format((float) $datos['salario'], 2, '.', ''),
            'codigo_empleado=' . $datos['codigo_empleado'],
            'tipo_empleado='   . $datos['tipo_empleado'],
            'planilla='        . $datos['planilla'],
            'ocupacion='       . $datos['ocupacion'],
            'fecha_inicio='    . $datos['fecha_inicio'],
        ]);
    }

    private static function obtenerLlavePrivada()
    {
        $contenido = file_get_contents(self::RUTA_LLAVE_PRIVADA);
        if ($contenido === false) {
            throw new \RuntimeException('No se pudo leer la llave privada en app/Config/keys/private_key.pem');
        }

        $llave = openssl_pkey_get_private($contenido);
        if ($llave === false) {
            throw new \RuntimeException('La llave privada no es válida: ' . openssl_error_string());
        }

        return $llave;
    }

    private static function obtenerLlavePublica()
    {
        $contenido = file_get_contents(self::RUTA_LLAVE_PUBLICA);
        if ($contenido === false) {
            throw new \RuntimeException('No se pudo leer la llave pública en app/Config/keys/public_key.pem');
        }

        $llave = openssl_pkey_get_public($contenido);
        if ($llave === false) {
            throw new \RuntimeException('La llave pública no es válida: ' . openssl_error_string());
        }

        return $llave;
    }

    /**
     * Genera la firma digital (OpenSSL RSA + SHA256) para un conjunto de datos.
     *
     * @param array $datos Debe incluir: salario, codigo_empleado, tipo_empleado,
     *                      planilla, ocupacion, fecha_inicio
     * @return string Firma codificada en base64 (lista para guardar en BD como texto)
     */
    public static function firmar(array $datos): string
    {
        $payload = self::buildPayload($datos);
        $llavePrivada = self::obtenerLlavePrivada();

        $firmaBinaria = '';
        $exito = openssl_sign($payload, $firmaBinaria, $llavePrivada, OPENSSL_ALGO_SHA256);

        if (!$exito) {
            throw new \RuntimeException('No se pudo generar la firma digital: ' . openssl_error_string());
        }

        return base64_encode($firmaBinaria);
    }

    /**
     * Verifica si la firma almacenada coincide con los datos actuales,
     * usando la llave pública (openssl_verify).
     *
     * @return bool true = integridad validada (verde), false = datos adulterados (rojo)
     */
    public static function verificar(array $datos, string $firmaAlmacenadaBase64): bool
    {
        if (empty($firmaAlmacenadaBase64)) {
            return false;
        }

        $payload = self::buildPayload($datos);
        $llavePublica = self::obtenerLlavePublica();
        $firmaBinaria = base64_decode($firmaAlmacenadaBase64);

        $resultado = openssl_verify($payload, $firmaBinaria, $llavePublica, OPENSSL_ALGO_SHA256);

        // openssl_verify regresa 1 (válida), 0 (inválida) o -1 (error)
        return $resultado === 1;
    }

    /**
     * Devuelve el indicador visual (para usar directamente en la vista del reporte).
     */
    public static function indicador(array $datos, string $firmaAlmacenada): array
    {
        $ok = self::verificar($datos, $firmaAlmacenada);
        return [
            'valido' => $ok,
            'color'  => $ok ? 'verde' : 'rojo',
            'icono'  => $ok ? '🟢' : '🔴',
            'texto'  => $ok ? 'Integridad validada' : 'Datos adulterados',
        ];
    }
}

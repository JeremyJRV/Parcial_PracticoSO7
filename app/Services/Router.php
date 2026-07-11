<?php

namespace App\Services;

/**
 * Router
 * ------
 * Enrutador minimalista basado en el parámetro GET "?ruta=".
 * Mapea "controlador/accion" a App\Controllers\{Controlador}Controller::{accion}
 *
 * Ejemplo de URL:
 *   index.php?ruta=colaborador/crear
 *   -> App\Controllers\ColaboradorController->crear()
 */
class Router
{
    private array $rutasPermitidas = [];

    /**
     * Registra una ruta permitida explícitamente (whitelist),
     * evitando que cualquier string arbitrario invoque una clase/método.
     */
    public function registrar(string $ruta, string $controlador, string $accion): void
    {
        $this->rutasPermitidas[$ruta] = [$controlador, $accion];
    }

    public function despachar(string $ruta): void
    {
        if (!isset($this->rutasPermitidas[$ruta])) {
            http_response_code(404);
            echo "Ruta no encontrada: " . htmlspecialchars($ruta);
            return;
        }

        [$controladorClase, $accion] = $this->rutasPermitidas[$ruta];
        $claseCompleta = "App\\Controllers\\{$controladorClase}";

        if (!class_exists($claseCompleta) || !method_exists($claseCompleta, $accion)) {
            http_response_code(500);
            echo "Controlador o acción no implementados.";
            return;
        }

        $controlador = new $claseCompleta();
        $controlador->$accion();
    }
}

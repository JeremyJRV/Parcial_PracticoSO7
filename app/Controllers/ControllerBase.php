<?php

namespace App\Controllers;

/**
 * ControllerBase
 * --------------
 * Funciones compartidas por todos los controladores:
 * renderizar vistas y redirigir dentro de la app.
 */
abstract class ControllerBase
{
    /**
     * Renderiza una vista pasando variables como array asociativo.
     * Las vistas viven en app/Views/{vista}.php
     */
    protected function render(string $vista, array $datos = []): void
    {
        extract($datos);
        $rutaVista = __DIR__ . '/../Views/' . $vista . '.php';

        if (!file_exists($rutaVista)) {
            die("Vista no encontrada: {$vista}");
        }

        require __DIR__ . '/../Views/layouts/header.php';
        require $rutaVista;
        require __DIR__ . '/../Views/layouts/footer.php';
    }

    /**
     * Redirige dentro de la app. Acepta parámetros extra en un array
     * para evitar construir URLs mal formadas (ej. dos "?" en la misma URL).
     *
     * Ejemplo: $this->redirigir('perfil/crear', ['colaborador_id' => $id]);
     * Genera: index.php?ruta=perfil/crear&colaborador_id=5
     */
    protected function redirigir(string $ruta, array $params = []): void
    {
        $query = 'ruta=' . urlencode($ruta);

        foreach ($params as $clave => $valor) {
            $query .= '&' . urlencode($clave) . '=' . urlencode((string) $valor);
        }

        header("Location: index.php?{$query}");
        exit;
    }

    /**
     * Guarda un mensaje flash en sesión para mostrarlo tras un redirect.
     */
    protected function flash(string $tipo, string $mensaje): void
    {
        $_SESSION['flash'] = ['tipo' => $tipo, 'mensaje' => $mensaje];
    }
}

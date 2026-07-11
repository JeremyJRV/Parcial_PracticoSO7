<?php

/**
 * Autoloader PSR-4 simple (sin Composer).
 * Mapea el namespace "App\" a la carpeta /app.
 */
spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return; // no es una clase de nuestra app
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

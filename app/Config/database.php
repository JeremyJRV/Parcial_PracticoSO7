<?php
/**
 * Configuración de conexión a la Base de Datos.
 * Ajusta estos valores según tu entorno de WampServer.
 */
return [
    'driver'   => 'mysql',
    'host'     => '127.0.0.1',
    'port'     => '3306',
    'database' => 'tiposangre',
    'username' => 'root',
    'password' => '',               // WampServer normalmente usa contraseña vacía
    'charset'  => 'utf8mb4',
];

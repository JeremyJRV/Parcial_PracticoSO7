<?php

/**
 * Front Controller
 * ----------------
 * Punto de entrada único de la aplicación.
 * Todas las peticiones pasan por aquí (ver .htaccess) y se
 * despachan según el parámetro "ruta".
 */

require_once __DIR__ . '/../app/autoload.php';

use App\Services\Router;

session_start();

$router = new Router();

// ===== Registro de rutas (whitelist) =====

// Colaborador
$router->registrar('colaborador/crear', 'ColaboradorController', 'mostrarFormulario');
$router->registrar('colaborador/guardar', 'ColaboradorController', 'guardar');
$router->registrar('colaborador/listado', 'ColaboradorController', 'listado');
$router->registrar('colaborador/editar', 'ColaboradorController', 'mostrarFormularioEdicion');
$router->registrar('colaborador/actualizar', 'ColaboradorController', 'actualizar');

// Perfil laboral
$router->registrar('perfil/crear', 'PerfilLaboralController', 'mostrarFormulario');
$router->registrar('perfil/guardar', 'PerfilLaboralController', 'guardar');
$router->registrar('perfil/promover', 'PerfilLaboralController', 'promover');
$router->registrar('perfil/finalizar', 'PerfilLaboralController', 'finalizar');

// Reportes
$router->registrar('reporte/index', 'ReporteController', 'index');
$router->registrar('reporte/exportarExcel', 'ReporteController', 'exportarExcel');

// ===== Despacho =====
$ruta = $_GET['ruta'] ?? 'colaborador/crear'; // ruta por defecto
$router->despachar($ruta);

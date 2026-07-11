<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iTECH Contrataciones - Gestión de Colaboradores</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <header class="app-header">
        <div class="contenedor">
            <h1>iTECH Contrataciones</h1>
            <nav class="app-nav">
                <a href="index.php?ruta=colaborador/crear">Registrar Colaborador</a>
                <a href="index.php?ruta=colaborador/listado">Listado</a>
                <a href="index.php?ruta=reporte/index">Reporte</a>
            </nav>
        </div>
    </header>
    <main class="contenedor">
        <?php if (!empty($_SESSION['flash'])): ?>
            <div class="alerta alerta-<?= htmlspecialchars($_SESSION['flash']['tipo']) ?>">
                <?= htmlspecialchars($_SESSION['flash']['mensaje']) ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

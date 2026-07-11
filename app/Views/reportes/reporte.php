<div class="tarjeta">
    <div class="cabecera-reporte">
        <h2>Reporte de Perfiles Laborales</h2>
        <a href="index.php?ruta=reporte/exportarExcel" class="btn btn-secundario">Exportar a Excel</a>
    </div>

    <table class="tabla">
        <thead>
            <tr>
                <th>Identidad</th>
                <th>Colaborador</th>
                <th>Ocupación</th>
                <th>Planilla</th>
                <th>Salario</th>
                <th>Fecha Inicio</th>
                <th>Estado</th>
                <th>Integridad</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($perfiles as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['identidad']) ?></td>
                    <td><?= htmlspecialchars($p['nombre'] . ' ' . $p['apellido']) ?></td>
                    <td><?= htmlspecialchars($p['ocupacion'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($p['tipo_planilla'] ?? '-') ?></td>
                    <td>$<?= number_format((float)$p['salario'], 2) ?></td>
                    <td><?= htmlspecialchars($p['fecha_inicio']) ?></td>
                    <td>
                        <?php if ($p['es_activo']): ?>
                            <span class="etiqueta etiqueta-verde">Activo</span>
                        <?php else: ?>
                            <span class="etiqueta etiqueta-gris">Histórico</span>
                        <?php endif; ?>
                    </td>
                    <td class="integridad-<?= $p['integridad']['color'] ?>">
                        <?= $p['integridad']['icono'] ?> <?= htmlspecialchars($p['integridad']['texto']) ?>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if (empty($perfiles)): ?>
                <tr><td colspan="8" class="sin-datos">No hay perfiles laborales registrados aún.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="tarjeta">
    <h2>Colaboradores Registrados</h2>

    <table class="tabla">
        <thead>
            <tr>
                <th>Identidad</th>
                <th>Nombre Completo</th>
                <th>Ocupación Actual</th>
                <th>Planilla</th>
                <th>Salario</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($colaboradores as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['identidad']) ?></td>
                    <td><?= htmlspecialchars($c['nombre'] . ' ' . $c['apellido']) ?></td>
                    <td><?= htmlspecialchars($c['ocupacion'] ?? 'Sin perfil laboral') ?></td>
                    <td><?= htmlspecialchars($c['tipo_planilla'] ?? '-') ?></td>
                    <td><?= $c['salario'] !== null ? '$' . number_format((float)$c['salario'], 2) : '-' ?></td>
                    <td>
                        <?php if ($c['empleado_activo']): ?>
                            <span class="etiqueta etiqueta-verde">Activo</span>
                        <?php else: ?>
                            <span class="etiqueta etiqueta-roja">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td class="acciones">
                        <a href="index.php?ruta=colaborador/editar&id=<?= $c['id'] ?>" class="btn btn-pequeno">Editar</a>
                        <?php if ($c['perfil_id']): ?>
                            <a href="index.php?ruta=perfil/crear&colaborador_id=<?= $c['id'] ?>" class="btn btn-pequeno btn-secundario">Promover</a>
                        <?php else: ?>
                            <a href="index.php?ruta=perfil/crear&colaborador_id=<?= $c['id'] ?>" class="btn btn-pequeno btn-secundario">Asignar Cargo</a>
                        <?php endif; ?>
                        <?php if ($c['perfil_id'] && $c['empleado_activo']): ?>
                            <button type="button" class="btn btn-pequeno btn-peligro"
                                    onclick="abrirModalBaja(<?= $c['perfil_id'] ?>, '<?= htmlspecialchars($c['nombre'] . ' ' . $c['apellido'], ENT_QUOTES) ?>')">
                                Dar de Baja
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if (empty($colaboradores)): ?>
                <tr><td colspan="7" class="sin-datos">No hay colaboradores registrados aún.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- ===== Modal: Dar de Baja (habilita el campo Motivo) ===== -->
<div id="modalBaja" class="modal-overlay" style="display:none;">
    <div class="modal-caja">
        <h3>Dar de Baja a <span id="modalBajaNombre"></span></h3>
        <p class="modal-descripcion">
            Al confirmar, este colaborador quedará marcado como inactivo
            (Empleado_Activo = 0) y su perfil laboral pasará al historial.
        </p>

        <form method="POST" action="index.php?ruta=perfil/finalizar">
            <input type="hidden" id="modalBajaPerfilId" name="perfil_id" value="">

            <div class="grupo-formulario">
                <label for="fecha_fin">Fecha de Baja</label>
                <input type="date" id="fecha_fin" name="fecha_fin" required value="<?= date('Y-m-d') ?>">
            </div>

            <div class="grupo-formulario">
                <label for="motivo_baja_id">Motivo de Baja</label>
                <select id="motivo_baja_id" name="motivo_baja_id" required>
                    <option value="">Seleccionar motivo...</option>
                    <?php foreach ($motivosBaja as $motivo): ?>
                        <option value="<?= $motivo['id'] ?>"><?= htmlspecialchars($motivo['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="modal-acciones">
                <button type="button" class="btn btn-pequeno" onclick="cerrarModalBaja()">Cancelar</button>
                <button type="submit" class="btn btn-peligro">Confirmar Baja</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalBaja(perfilId, nombreCompleto) {
    document.getElementById('modalBajaPerfilId').value = perfilId;
    document.getElementById('modalBajaNombre').textContent = nombreCompleto;
    document.getElementById('modalBaja').style.display = 'flex';
}

function cerrarModalBaja() {
    document.getElementById('modalBaja').style.display = 'none';
}
</script>

<?php $esPromocion = $perfilActivo !== null; ?>

<div class="tarjeta">
    <h2>
        <?= $esPromocion ? 'Promover a ' : 'Asignar Cargo a ' ?>
        <?= htmlspecialchars($colaborador['nombre'] . ' ' . $colaborador['apellido']) ?>
    </h2>

    <?php if ($esPromocion): ?>
        <div class="alerta alerta-info">
            Este colaborador ya tiene un cargo activo. Al guardar, el cargo actual
            se marcará como histórico (inactivo) y este nuevo cargo pasará a ser el activo.
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?ruta=<?= $esPromocion ? 'perfil/promover' : 'perfil/guardar' ?>" class="formulario">
        <input type="hidden" name="colaborador_id" value="<?= $colaborador['id'] ?>">

        <div class="fila">
            <div class="grupo-formulario">
                <label for="ocupacion_id">Puesto (Ocupación)</label>
                <select id="ocupacion_id" name="ocupacion_id" required>
                    <option value="">Seleccionar...</option>
                    <?php foreach ($ocupaciones as $o): ?>
                        <option value="<?= $o['id'] ?>"><?= htmlspecialchars($o['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errores['ocupacion_id'])): ?>
                    <span class="error"><?= htmlspecialchars($errores['ocupacion_id']) ?></span>
                <?php endif; ?>
            </div>

            <div class="grupo-formulario">
                <label for="tipo_planilla_id">Tipo de Planilla</label>
                <select id="tipo_planilla_id" name="tipo_planilla_id" required>
                    <option value="">Seleccionar...</option>
                    <?php foreach ($tiposPlanilla as $tp): ?>
                        <option value="<?= $tp['id'] ?>"><?= htmlspecialchars($tp['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errores['tipo_planilla_id'])): ?>
                    <span class="error"><?= htmlspecialchars($errores['tipo_planilla_id']) ?></span>
                <?php endif; ?>
            </div>

            <div class="grupo-formulario">
                <label for="tipo_empleado_id">Tipo de Empleado</label>
                <select id="tipo_empleado_id" name="tipo_empleado_id" required>
                    <option value="">Seleccionar...</option>
                    <?php foreach ($tiposEmpleado as $te): ?>
                        <option value="<?= $te['id'] ?>"><?= htmlspecialchars($te['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="fila">
            <div class="grupo-formulario">
                <label for="salario">Salario</label>
                <input type="number" step="0.01" id="salario" name="salario" required>
                <?php if (isset($errores['salario'])): ?>
                    <span class="error"><?= htmlspecialchars($errores['salario']) ?></span>
                <?php endif; ?>
            </div>

            <div class="grupo-formulario">
                <label for="fecha_inicio">Fecha de Inicio</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" required
                       value="<?= date('Y-m-d') ?>">
                <?php if (isset($errores['fecha_inicio'])): ?>
                    <span class="error"><?= htmlspecialchars($errores['fecha_inicio']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <button type="submit" class="btn btn-primario">
            <?= $esPromocion ? 'Confirmar Promoción' : 'Guardar Perfil Laboral' ?>
        </button>
    </form>

    <?php if ($esPromocion): ?>
        <hr>
        <h3>Cargo Actual (será marcado como histórico)</h3>
        <p>
            <strong>Ocupación:</strong> <?= htmlspecialchars($perfilActivo['ocupacion_id']) ?> &nbsp;|&nbsp;
            <strong>Salario:</strong> $<?= number_format((float)$perfilActivo['salario'], 2) ?> &nbsp;|&nbsp;
            <strong>Desde:</strong> <?= htmlspecialchars($perfilActivo['fecha_inicio']) ?>
        </p>
    <?php endif; ?>
</div>

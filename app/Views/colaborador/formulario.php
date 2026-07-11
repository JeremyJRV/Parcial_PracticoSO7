<?php $editando = $editando ?? false; ?>

<div class="tarjeta">
    <h2><?= $editando ? 'Editar Colaborador' : 'Registrar Nuevo Colaborador' ?></h2>

    <form method="POST" action="index.php?ruta=<?= $editando ? 'colaborador/actualizar' : 'colaborador/guardar' ?>" class="formulario">
        <?php if ($editando): ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($datosPrevios['id']) ?>">
        <?php endif; ?>

        <div class="grupo-formulario">
            <label for="identidad">Identidad (Cédula)</label>
            <input type="text" id="identidad" name="identidad" placeholder="8-123-4567"
                   value="<?= htmlspecialchars($datosPrevios['identidad'] ?? '') ?>"
                   <?= $editando ? 'readonly' : 'required' ?>>
            <?php if (isset($errores['identidad'])): ?>
                <span class="error"><?= htmlspecialchars($errores['identidad']) ?></span>
            <?php endif; ?>
        </div>

        <div class="fila">
            <div class="grupo-formulario">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" required
                       value="<?= htmlspecialchars($datosPrevios['nombre'] ?? '') ?>">
                <?php if (isset($errores['nombre'])): ?>
                    <span class="error"><?= htmlspecialchars($errores['nombre']) ?></span>
                <?php endif; ?>
            </div>

            <div class="grupo-formulario">
                <label for="apellido">Apellido</label>
                <input type="text" id="apellido" name="apellido" required
                       value="<?= htmlspecialchars($datosPrevios['apellido'] ?? '') ?>">
                <?php if (isset($errores['apellido'])): ?>
                    <span class="error"><?= htmlspecialchars($errores['apellido']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="fila">
            <div class="grupo-formulario">
                <label for="edad">Edad</label>
                <input type="number" id="edad" name="edad" min="18" max="65" required
                       value="<?= htmlspecialchars($datosPrevios['edad'] ?? '') ?>">
                <?php if (isset($errores['edad'])): ?>
                    <span class="error"><?= htmlspecialchars($errores['edad']) ?></span>
                <?php endif; ?>
            </div>

            <div class="grupo-formulario">
                <label for="tipo_sangre_id">Tipo de Sangre</label>
                <select id="tipo_sangre_id" name="tipo_sangre_id" required>
                    <option value="">Seleccionar...</option>
                    <?php foreach ($tiposSangre as $tipo): ?>
                        <option value="<?= $tipo['id'] ?>"
                            <?= (($datosPrevios['tipo_sangre_id'] ?? '') == $tipo['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars(trim($tipo['nombre'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errores['tipo_sangre_id'])): ?>
                    <span class="error"><?= htmlspecialchars($errores['tipo_sangre_id']) ?></span>
                <?php endif; ?>
            </div>

            <div class="grupo-formulario">
                <label for="sexo_id">Sexo</label>
                <select id="sexo_id" name="sexo_id" required>
                    <option value="">Seleccionar...</option>
                    <?php foreach ($sexos as $sexo): ?>
                        <option value="<?= $sexo['id'] ?>"
                            <?= (($datosPrevios['sexo_id'] ?? '') == $sexo['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sexo['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errores['sexo_id'])): ?>
                    <span class="error"><?= htmlspecialchars($errores['sexo_id']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="fila">
            <div class="grupo-formulario">
                <label for="nacionalidad">Nacionalidad</label>
                <input type="text" id="nacionalidad" name="nacionalidad" required
                       value="<?= htmlspecialchars($datosPrevios['nacionalidad'] ?? 'Panameña') ?>">
                <?php if (isset($errores['nacionalidad'])): ?>
                    <span class="error"><?= htmlspecialchars($errores['nacionalidad']) ?></span>
                <?php endif; ?>
            </div>

            <div class="grupo-formulario">
                <label for="ruta_id">Ruta</label>
                <select id="ruta_id" name="ruta_id" required>
                    <option value="">Seleccionar...</option>
                    <?php foreach ($rutas as $ruta): ?>
                        <option value="<?= $ruta['id'] ?>"
                            <?= (($datosPrevios['ruta_id'] ?? '') == $ruta['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ruta['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errores['ruta_id'])): ?>
                    <span class="error"><?= htmlspecialchars($errores['ruta_id']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="fila">
            <div class="grupo-formulario">
                <label for="correo">Correo</label>
                <input type="email" id="correo" name="correo" required
                       value="<?= htmlspecialchars($datosPrevios['correo'] ?? '') ?>">
                <?php if (isset($errores['correo'])): ?>
                    <span class="error"><?= htmlspecialchars($errores['correo']) ?></span>
                <?php endif; ?>
            </div>

            <div class="grupo-formulario">
                <label for="celular">Celular</label>
                <input type="text" id="celular" name="celular" placeholder="6000-0000" required
                       value="<?= htmlspecialchars($datosPrevios['celular'] ?? '') ?>">
                <?php if (isset($errores['celular'])): ?>
                    <span class="error"><?= htmlspecialchars($errores['celular']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <button type="submit" class="btn btn-primario">
            <?= $editando ? 'Actualizar Colaborador' : 'Registrar y Continuar a Perfil Laboral' ?>
        </button>
    </form>
</div>

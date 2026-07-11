-- ============================================================
-- Script complementario: agrega las tablas faltantes
-- (colaboradores, perfiles_laborales, cat_tipos_planilla)
-- y corrige `tiposangre` a InnoDB para soportar Foreign Keys.
--
-- IMPORTANTE: Ejecuta PRIMERO tu archivo `tiposangre.sql` original
-- (el que te dio el profesor) y LUEGO este script.
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- Corrección: `tiposangre` estaba en MyISAM (no soporta FKs).
-- La convertimos a InnoDB para poder referenciarla.
-- ------------------------------------------------------------
ALTER TABLE `tiposangre` ENGINE = InnoDB;

-- ------------------------------------------------------------
-- Tabla catálogo faltante: cat_tipos_planilla
-- Mencionada en el documento de contexto pero no venía en el dump.
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `cat_tipos_planilla`;
CREATE TABLE IF NOT EXISTS `cat_tipos_planilla` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_tipo_planilla_nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `cat_tipos_planilla` (`nombre`) VALUES
('Permanente'),
('Eventual'),
('Interino');

-- ------------------------------------------------------------
-- Tabla: colaboradores
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `colaboradores`;
CREATE TABLE IF NOT EXISTS `colaboradores` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `identidad` VARCHAR(20) NOT NULL,
  `nombre` VARCHAR(50) NOT NULL,
  `apellido` VARCHAR(50) NOT NULL,
  `edad` INT NOT NULL,
  `tipo_sangre_id` INT NOT NULL,
  `sexo_id` INT NOT NULL,
  `nacionalidad` VARCHAR(50) NOT NULL,
  `ruta_id` INT NOT NULL,
  `correo` VARCHAR(100) NOT NULL,
  `celular` VARCHAR(20) NOT NULL,
  `empleado_activo` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_colaborador_identidad` (`identidad`),
  UNIQUE KEY `uq_colaborador_correo` (`correo`),
  UNIQUE KEY `uq_colaborador_celular` (`celular`),
  KEY `fk_colaborador_tipo_sangre` (`tipo_sangre_id`),
  KEY `fk_colaborador_sexo` (`sexo_id`),
  KEY `fk_colaborador_ruta` (`ruta_id`),

  CONSTRAINT `fk_colaborador_tipo_sangre`
    FOREIGN KEY (`tipo_sangre_id`) REFERENCES `tiposangre` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,

  CONSTRAINT `fk_colaborador_sexo`
    FOREIGN KEY (`sexo_id`) REFERENCES ` cat_sexo` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,

  CONSTRAINT `fk_colaborador_ruta`
    FOREIGN KEY (`ruta_id`) REFERENCES `cat_rutas` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ------------------------------------------------------------
-- Tabla: perfiles_laborales
-- Vinculada por colaborador_id (el Id/Código autonumérico),
-- NO por identidad, tal como pide la regla de negocio #1.
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `perfiles_laborales`;
CREATE TABLE IF NOT EXISTS `perfiles_laborales` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `colaborador_id` INT NOT NULL,
  `ocupacion_id` INT NOT NULL,
  `tipo_planilla_id` INT NOT NULL,
  `tipo_empleado_id` INT NOT NULL,
  `salario` DECIMAL(10,2) NOT NULL,
  `fecha_inicio` DATE NOT NULL,
  `fecha_fin` DATE NULL DEFAULT NULL,
  `es_activo` TINYINT(1) NOT NULL DEFAULT 1,
  `motivo_baja_id` INT NULL DEFAULT NULL,
  `firma_digital` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_perfil_colaborador` (`colaborador_id`),
  KEY `fk_perfil_ocupacion` (`ocupacion_id`),
  KEY `fk_perfil_tipo_planilla` (`tipo_planilla_id`),
  KEY `fk_perfil_tipo_empleado` (`tipo_empleado_id`),
  KEY `fk_perfil_motivo_baja` (`motivo_baja_id`),

  CONSTRAINT `fk_perfil_colaborador`
    FOREIGN KEY (`colaborador_id`) REFERENCES `colaboradores` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,

  CONSTRAINT `fk_perfil_ocupacion`
    FOREIGN KEY (`ocupacion_id`) REFERENCES `cat_ocupaciones` (`C_OCUP`)
    ON DELETE RESTRICT ON UPDATE CASCADE,

  CONSTRAINT `fk_perfil_tipo_planilla`
    FOREIGN KEY (`tipo_planilla_id`) REFERENCES `cat_tipos_planilla` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,

  CONSTRAINT `fk_perfil_tipo_empleado`
    FOREIGN KEY (`tipo_empleado_id`) REFERENCES `cat_tipoempleado` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,

  CONSTRAINT `fk_perfil_motivo_baja`
    FOREIGN KEY (`motivo_baja_id`) REFERENCES `cat_motivos_terminacion` (`C_TERMINACION`)
    ON DELETE RESTRICT ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

SET FOREIGN_KEY_CHECKS = 1;

<?php

namespace App\Models;

/** Catálogo de motivos de baja/terminación (cat_motivos_terminacion). */
class CatMotivoTerminacion extends CatalogoBase
{
    protected string $tabla = 'cat_motivos_terminacion';
    protected string $columnaId = 'C_TERMINACION';
    protected string $columnaNombre = 'MOTIVO';
}

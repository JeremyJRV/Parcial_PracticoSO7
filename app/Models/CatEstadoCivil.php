<?php

namespace App\Models;

/** Catálogo de estado civil (cat_estadocivil). Uso opcional/complementario. */
class CatEstadoCivil extends CatalogoBase
{
    protected string $tabla = 'cat_estadocivil';
    protected string $columnaId = 'id';
    protected string $columnaNombre = 'nombre';
}

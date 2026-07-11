<?php

namespace App\Models;

/** Catálogo de tipos de sangre (tiposangre): A+, A-, B+, B-, AB+, AB-, O+, O-. */
class CatTipoSangre extends CatalogoBase
{
    protected string $tabla = 'tiposangre';
    protected string $columnaId = 'id';
    protected string $columnaNombre = 'Nombre';
}

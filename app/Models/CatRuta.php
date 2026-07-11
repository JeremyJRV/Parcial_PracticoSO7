<?php

namespace App\Models;

/** Catálogo de rutas (cat_rutas): Panamá Este, Oeste, Norte, Centro. */
class CatRuta extends CatalogoBase
{
    protected string $tabla = 'cat_rutas';
    protected string $columnaId = 'id';
    protected string $columnaNombre = 'Nombre';
}

<?php

namespace App\Models;

/**
 * Catálogo de tipos de planilla (cat_tipos_planilla).
 * NOTA: esta tabla NO viene en el dump original (tiposangre.sql);
 * hay que crearla nosotros. Contiene: Permanente, Eventual, Interino.
 */
class CatTipoPlanilla extends CatalogoBase
{
    protected string $tabla = 'cat_tipos_planilla';
    protected string $columnaId = 'id';
    protected string $columnaNombre = 'nombre';
}

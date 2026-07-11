<?php

namespace App\Models;

/**
 * Catálogo de sexo (` cat_sexo`).
 * Nota: la tabla original tiene un espacio en el nombre; se referencia
 * con backticks para evitar errores de sintaxis SQL.
 */
class CatSexo extends CatalogoBase
{
    protected string $tabla = ' cat_sexo';
    protected string $columnaId = 'id';
    protected string $columnaNombre = 'nombre';
}

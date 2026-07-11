<?php

namespace App\Models;

/** Catálogo de ocupaciones (cat_ocupaciones). */
class CatOcupacion extends CatalogoBase
{
    protected string $tabla = 'cat_ocupaciones';
    protected string $columnaId = 'C_OCUP';
    protected string $columnaNombre = 'OCUPACION';

    /** Solo las ocupaciones marcadas como activas (Activo = 1). */
    public function activos(): array
    {
        return $this->db->select(
            "SELECT C_OCUP AS id, OCUPACION AS nombre
             FROM cat_ocupaciones WHERE Activo = 1
             ORDER BY OCUPACION ASC"
        );
    }
}

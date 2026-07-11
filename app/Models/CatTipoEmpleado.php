<?php

namespace App\Models;

/** Catálogo de tipos de empleado (cat_tipoempleado): Permanente, Eventual, Interino, etc. */
class CatTipoEmpleado extends CatalogoBase
{
    protected string $tabla = 'cat_tipoempleado';
    protected string $columnaId = 'id';
    protected string $columnaNombre = 'Nombre';

    public function activos(): array
    {
        return $this->db->select(
            "SELECT id, Nombre AS nombre, Abreviatura
             FROM cat_tipoempleado WHERE Activo = 1
             ORDER BY Nombre ASC"
        );
    }
}

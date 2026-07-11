<?php

namespace App\Models;

use App\Services\DatabaseConnection;

/**
 * CatalogoBase
 * ------------
 * Clase base abstracta para tablas catálogo (listas de selección).
 * Evita repetir el mismo código en cada catálogo (principio DRY).
 * Cada subclase solo define el nombre de tabla y sus columnas.
 */
abstract class CatalogoBase
{
    protected DatabaseConnection $db;
    protected string $tabla;
    protected string $columnaId;
    protected string $columnaNombre;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance();
    }

    public function todos(): array
    {
        return $this->db->select(
            "SELECT `{$this->columnaId}` AS id, `{$this->columnaNombre}` AS nombre
             FROM `{$this->tabla}`
             ORDER BY `{$this->columnaNombre}` ASC"
        );
    }

    public function buscarPorId(int $id): ?array
    {
        return $this->db->selectOne(
            "SELECT `{$this->columnaId}` AS id, `{$this->columnaNombre}` AS nombre
             FROM `{$this->tabla}` WHERE `{$this->columnaId}` = :id",
            [':id' => $id]
        );
    }
}

<?php

namespace App\Models;

use App\Services\DatabaseConnection;

/**
 * Colaborador
 * -----------
 * Representa la entidad "colaborador" y encapsula todas las
 * operaciones CRUD relacionadas a la tabla `colaboradores`.
 */
class Colaborador
{
    private DatabaseConnection $db;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance();
    }

    public function crear(array $data): string
    {
        $sql = "INSERT INTO colaboradores
                    (identidad, nombre, apellido, edad, tipo_sangre_id, sexo_id,
                     nacionalidad, ruta_id, correo, celular, empleado_activo)
                VALUES
                    (:identidad, :nombre, :apellido, :edad, :tipo_sangre_id, :sexo_id,
                     :nacionalidad, :ruta_id, :correo, :celular, 1)";

        return $this->db->insert($sql, [
            ':identidad'      => $data['identidad'],
            ':nombre'         => $data['nombre'],
            ':apellido'       => $data['apellido'],
            ':edad'           => $data['edad'],
            ':tipo_sangre_id' => $data['tipo_sangre_id'],
            ':sexo_id'        => $data['sexo_id'],
            ':nacionalidad'   => $data['nacionalidad'],
            ':ruta_id'        => $data['ruta_id'],
            ':correo'         => $data['correo'],
            ':celular'        => $data['celular'],
        ]);
    }

    public function actualizar(int $id, array $data): int
    {
        $sql = "UPDATE colaboradores SET
                    nombre = :nombre,
                    apellido = :apellido,
                    edad = :edad,
                    tipo_sangre_id = :tipo_sangre_id,
                    sexo_id = :sexo_id,
                    nacionalidad = :nacionalidad,
                    ruta_id = :ruta_id,
                    correo = :correo,
                    celular = :celular
                WHERE id = :id";

        return $this->db->execute($sql, [
            ':nombre'         => $data['nombre'],
            ':apellido'       => $data['apellido'],
            ':edad'           => $data['edad'],
            ':tipo_sangre_id' => $data['tipo_sangre_id'],
            ':sexo_id'        => $data['sexo_id'],
            ':nacionalidad'   => $data['nacionalidad'],
            ':ruta_id'        => $data['ruta_id'],
            ':correo'         => $data['correo'],
            ':celular'        => $data['celular'],
            ':id'             => $id,
        ]);
    }

    public function buscarPorId(int $id): ?array
    {
        return $this->db->selectOne(
            "SELECT * FROM colaboradores WHERE id = :id",
            [':id' => $id]
        );
    }

    public function buscarPorIdentidad(string $identidad): ?array
    {
        return $this->db->selectOne(
            "SELECT * FROM colaboradores WHERE identidad = :identidad",
            [':identidad' => $identidad]
        );
    }

    public function existeCorreo(string $correo, ?int $exceptoId = null): bool
    {
        $sql = "SELECT id FROM colaboradores WHERE correo = :correo";
        $params = [':correo' => $correo];
        if ($exceptoId !== null) {
            $sql .= " AND id != :id";
            $params[':id'] = $exceptoId;
        }
        return $this->db->selectOne($sql, $params) !== null;
    }

    public function existeCelular(string $celular, ?int $exceptoId = null): bool
    {
        $sql = "SELECT id FROM colaboradores WHERE celular = :celular";
        $params = [':celular' => $celular];
        if ($exceptoId !== null) {
            $sql .= " AND id != :id";
            $params[':id'] = $exceptoId;
        }
        return $this->db->selectOne($sql, $params) !== null;
    }

    public function todos(): array
    {
        return $this->db->select("SELECT * FROM colaboradores ORDER BY apellido, nombre");
    }

    /**
     * Colaboradores con su perfil laboral activo (LEFT OUTER JOIN),
     * tal como pide la regla de negocio #4 del documento.
     */
    public function todosConPerfilActivo(): array
    {
        $sql = "SELECT
                    c.*,
                    p.id AS perfil_id,
                    p.salario,
                    p.fecha_inicio,
                    p.fecha_fin,
                    p.es_activo AS perfil_activo,
                    o.OCUPACION AS ocupacion,
                    tp.nombre AS tipo_planilla,
                    te.Nombre AS tipo_empleado
                FROM colaboradores c
                LEFT OUTER JOIN perfiles_laborales p
                    ON p.colaborador_id = c.id AND p.es_activo = 1
                LEFT OUTER JOIN cat_ocupaciones o ON o.C_OCUP = p.ocupacion_id
                LEFT OUTER JOIN cat_tipos_planilla tp ON tp.id = p.tipo_planilla_id
                LEFT OUTER JOIN cat_tipoempleado te ON te.id = p.tipo_empleado_id
                ORDER BY c.apellido, c.nombre";

        return $this->db->select($sql);
    }

    /**
     * No se permite eliminar si tiene perfiles asociados
     * (ON DELETE RESTRICT ya lo protege a nivel de BD, pero
     * validamos aquí también para dar un mensaje amigable).
     */
    public function eliminar(int $id): int
    {
        return $this->db->execute("DELETE FROM colaboradores WHERE id = :id", [':id' => $id]);
    }
}

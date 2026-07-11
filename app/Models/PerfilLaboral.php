<?php

namespace App\Models;

use App\Services\DatabaseConnection;
use App\Services\FirmaDigitalService;

/**
 * PerfilLaboral
 * -------------
 * Representa la entidad "perfil laboral" del colaborador.
 * Un colaborador puede tener MUCHOS perfiles a lo largo del tiempo,
 * pero solo UNO activo (es_activo = 1) en un momento dado.
 *
 * Regla de negocio clave: la "promoción" NO actualiza el perfil existente,
 * sino que:
 *   1) Desactiva el perfil anterior (es_activo = 0, fecha_fin = hoy)
 *   2) Crea un perfil nuevo (es_activo = 1) con el nuevo cargo/salario
 */
class PerfilLaboral
{
    private DatabaseConnection $db;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance();
    }

    /**
     * Crea el primer perfil laboral de un colaborador (o uno adicional
     * si ya no tiene perfil activo). Calcula y guarda la firma digital.
     */
    public function crear(array $data): string
    {
        $firma = $this->generarFirma($data);

        $sql = "INSERT INTO perfiles_laborales
                    (colaborador_id, ocupacion_id, tipo_planilla_id, tipo_empleado_id,
                     salario, fecha_inicio, fecha_fin, es_activo, motivo_baja_id, firma_digital)
                VALUES
                    (:colaborador_id, :ocupacion_id, :tipo_planilla_id, :tipo_empleado_id,
                     :salario, :fecha_inicio, :fecha_fin, 1, :motivo_baja_id, :firma_digital)";

        return $this->db->insert($sql, [
            ':colaborador_id'   => $data['colaborador_id'],
            ':ocupacion_id'     => $data['ocupacion_id'],
            ':tipo_planilla_id' => $data['tipo_planilla_id'],
            ':tipo_empleado_id' => $data['tipo_empleado_id'],
            ':salario'          => $data['salario'],
            ':fecha_inicio'     => $data['fecha_inicio'],
            ':fecha_fin'        => $data['fecha_fin'] ?? null,
            ':motivo_baja_id'   => $data['motivo_baja_id'] ?? null,
            ':firma_digital'    => $firma,
        ]);
    }

    /**
     * PROMOCIÓN (regla de negocio obligatoria, -5 pts si no se implementa):
     * Desactiva el perfil activo actual y crea uno nuevo con el nuevo cargo.
     * Se ejecuta dentro de una transacción para garantizar consistencia.
     */
    public function promover(int $colaboradorId, array $nuevoPerfil): string
    {
        $this->db->beginTransaction();
        try {
            $activo = $this->buscarActivoPorColaborador($colaboradorId);

            if ($activo !== null) {
                $this->desactivar(
                    (int) $activo['id'],
                    $nuevoPerfil['fecha_fin_anterior'] ?? date('Y-m-d')
                );
            }

            $nuevoPerfil['colaborador_id'] = $colaboradorId;
            $nuevoId = $this->crear($nuevoPerfil);

            $this->db->commit();
            return $nuevoId;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Marca un perfil como inactivo (histórico), registrando su fecha_fin.
     */
    public function desactivar(int $perfilId, string $fechaFin): int
    {
        $sql = "UPDATE perfiles_laborales
                SET es_activo = 0, fecha_fin = :fecha_fin
                WHERE id = :id";

        return $this->db->execute($sql, [
            ':fecha_fin' => $fechaFin,
            ':id'        => $perfilId,
        ]);
    }

    /**
     * Finaliza la relación laboral (baja) del perfil activo:
     * marca es_activo = 0, guarda fecha_fin y motivo_baja,
     * y marca empleado_activo = 0 en el colaborador (Punto 17).
     */
    public function finalizar(int $perfilId, string $fechaFin, int $motivoBajaId): void
    {
        $this->db->beginTransaction();
        try {
            $perfil = $this->buscarPorId($perfilId);
            if ($perfil === null) {
                throw new \RuntimeException('Perfil laboral no encontrado.');
            }

            $this->db->execute(
                "UPDATE perfiles_laborales
                 SET es_activo = 0, fecha_fin = :fecha_fin, motivo_baja_id = :motivo_baja_id
                 WHERE id = :id",
                [
                    ':fecha_fin'      => $fechaFin,
                    ':motivo_baja_id' => $motivoBajaId,
                    ':id'             => $perfilId,
                ]
            );

            $this->db->execute(
                "UPDATE colaboradores SET empleado_activo = 0 WHERE id = :id",
                [':id' => $perfil['colaborador_id']]
            );

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function buscarPorId(int $id): ?array
    {
        return $this->db->selectOne(
            "SELECT * FROM perfiles_laborales WHERE id = :id",
            [':id' => $id]
        );
    }

    public function buscarActivoPorColaborador(int $colaboradorId): ?array
    {
        return $this->db->selectOne(
            "SELECT * FROM perfiles_laborales
             WHERE colaborador_id = :colaborador_id AND es_activo = 1
             LIMIT 1",
            [':colaborador_id' => $colaboradorId]
        );
    }

    /**
     * Historial completo de perfiles (activos e inactivos) de un colaborador.
     */
    public function historialPorColaborador(int $colaboradorId): array
    {
        $sql = "SELECT p.*, o.OCUPACION AS ocupacion, tp.nombre AS tipo_planilla,
                       te.Nombre AS tipo_empleado, mt.MOTIVO AS motivo_baja
                FROM perfiles_laborales p
                LEFT OUTER JOIN cat_ocupaciones o ON o.C_OCUP = p.ocupacion_id
                LEFT OUTER JOIN cat_tipos_planilla tp ON tp.id = p.tipo_planilla_id
                LEFT OUTER JOIN cat_tipoempleado te ON te.id = p.tipo_empleado_id
                LEFT OUTER JOIN cat_motivos_terminacion mt ON mt.C_TERMINACION = p.motivo_baja_id
                WHERE p.colaborador_id = :colaborador_id
                ORDER BY p.fecha_inicio DESC";

        return $this->db->select($sql, [':colaborador_id' => $colaboradorId]);
    }

    /**
     * Todos los perfiles con datos relacionados, para el reporte general
     * con verificación de integridad (firma digital).
     */
    public function todosParaReporte(): array
    {
        $sql = "SELECT
                    p.*,
                    c.identidad, c.nombre, c.apellido,
                    o.OCUPACION AS ocupacion,
                    tp.nombre AS tipo_planilla,
                    te.Nombre AS tipo_empleado
                FROM perfiles_laborales p
                INNER JOIN colaboradores c ON c.id = p.colaborador_id
                LEFT OUTER JOIN cat_ocupaciones o ON o.C_OCUP = p.ocupacion_id
                LEFT OUTER JOIN cat_tipos_planilla tp ON tp.id = p.tipo_planilla_id
                LEFT OUTER JOIN cat_tipoempleado te ON te.id = p.tipo_empleado_id
                ORDER BY c.apellido, p.fecha_inicio DESC";

        return $this->db->select($sql);
    }

    /**
     * Genera la firma digital a partir de los datos sensibles del perfil,
     * resolviendo primero los nombres legibles de ocupación/planilla/tipoEmpleado
     * (la firma se calcula sobre datos "de negocio", no solo IDs).
     */
    private function generarFirma(array $data): string
    {
        $ocupacion = $this->db->selectOne(
            "SELECT OCUPACION FROM cat_ocupaciones WHERE C_OCUP = :id",
            [':id' => $data['ocupacion_id']]
        )['OCUPACION'] ?? '';

        $planilla = $this->db->selectOne(
            "SELECT nombre FROM cat_tipos_planilla WHERE id = :id",
            [':id' => $data['tipo_planilla_id']]
        )['nombre'] ?? '';

        $tipoEmpleado = $this->db->selectOne(
            "SELECT Nombre FROM cat_tipoempleado WHERE id = :id",
            [':id' => $data['tipo_empleado_id']]
        )['Nombre'] ?? '';

        return FirmaDigitalService::firmar([
            'salario'         => $data['salario'],
            'codigo_empleado' => $data['colaborador_id'],
            'tipo_empleado'   => $tipoEmpleado,
            'planilla'        => $planilla,
            'ocupacion'       => $ocupacion,
            'fecha_inicio'    => $data['fecha_inicio'],
        ]);
    }

    /**
     * Reconstruye los datos sensibles de un perfil ya guardado (para
     * verificar su firma en el reporte, comparando contra lo almacenado).
     */
    public function datosParaVerificacion(array $perfilRow): array
    {
        return [
            'salario'         => $perfilRow['salario'],
            'codigo_empleado' => $perfilRow['colaborador_id'],
            'tipo_empleado'   => $perfilRow['tipo_empleado'] ?? '',
            'planilla'        => $perfilRow['tipo_planilla'] ?? '',
            'ocupacion'       => $perfilRow['ocupacion'] ?? '',
            'fecha_inicio'    => $perfilRow['fecha_inicio'],
        ];
    }
}

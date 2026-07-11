<?php

namespace App\Controllers;

use App\Models\PerfilLaboral;
use App\Services\FirmaDigitalService;

class ReporteController extends ControllerBase
{
    private PerfilLaboral $perfilModel;

    public function __construct()
    {
        $this->perfilModel = new PerfilLaboral();
    }

    /** GET reporte/index - Reporte con indicadores verde/rojo de integridad */
    public function index(): void
    {
        $perfiles = $this->perfilModel->todosParaReporte();

        // Para cada fila, recalculamos la firma y comparamos (verde/rojo)
        foreach ($perfiles as &$perfil) {
            $datosVerificacion = $this->perfilModel->datosParaVerificacion($perfil);
            $perfil['integridad'] = FirmaDigitalService::indicador(
                $datosVerificacion,
                $perfil['firma_digital'] ?? ''
            );
        }
        unset($perfil);

        $this->render('reportes/reporte', ['perfiles' => $perfiles]);
    }

    /**
     * GET reporte/exportarExcel - Exporta el reporte a un archivo .xls
     * (formato HTML-table-as-Excel, compatible sin librerías externas).
     */
    public function exportarExcel(): void
    {
        $perfiles = $this->perfilModel->todosParaReporte();

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="reporte_colaboradores.xls"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo "\xEF\xBB\xBF"; // BOM para acentos correctos en Excel
        echo '<table border="1">';
        echo '<tr>
                <th>Identidad</th><th>Nombre</th><th>Apellido</th>
                <th>Ocupación</th><th>Planilla</th><th>Tipo Empleado</th>
                <th>Salario</th><th>Fecha Inicio</th><th>Fecha Fin</th>
                <th>Activo</th><th>Integridad</th>
              </tr>';

        foreach ($perfiles as $perfil) {
            $datosVerificacion = $this->perfilModel->datosParaVerificacion($perfil);
            $integridad = FirmaDigitalService::verificar($datosVerificacion, $perfil['firma_digital'] ?? '')
                ? 'Válida'
                : 'ADULTERADA';

            echo '<tr>';
            echo '<td>' . htmlspecialchars($perfil['identidad']) . '</td>';
            echo '<td>' . htmlspecialchars($perfil['nombre']) . '</td>';
            echo '<td>' . htmlspecialchars($perfil['apellido']) . '</td>';
            echo '<td>' . htmlspecialchars($perfil['ocupacion'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($perfil['tipo_planilla'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($perfil['tipo_empleado'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($perfil['salario']) . '</td>';
            echo '<td>' . htmlspecialchars($perfil['fecha_inicio']) . '</td>';
            echo '<td>' . htmlspecialchars($perfil['fecha_fin'] ?? '') . '</td>';
            echo '<td>' . ($perfil['es_activo'] ? 'Sí' : 'No') . '</td>';
            echo '<td>' . $integridad . '</td>';
            echo '</tr>';
        }

        echo '</table>';
        exit;
    }
}

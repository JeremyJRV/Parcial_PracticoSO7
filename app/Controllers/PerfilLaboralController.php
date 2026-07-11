<?php

namespace App\Controllers;

use App\Models\PerfilLaboral;
use App\Models\Colaborador;
use App\Models\CatOcupacion;
use App\Models\CatTipoPlanilla;
use App\Models\CatTipoEmpleado;
use App\Models\CatMotivoTerminacion;
use App\Services\ValidationService;
use App\Services\SanitizerService;

class PerfilLaboralController extends ControllerBase
{
    private PerfilLaboral $perfilModel;
    private Colaborador $colaboradorModel;

    public function __construct()
    {
        $this->perfilModel = new PerfilLaboral();
        $this->colaboradorModel = new Colaborador();
    }

    /** GET perfil/crear?colaborador_id=X - Formulario de perfil laboral */
    public function mostrarFormulario(): void
    {
        $colaboradorId = (int) ($_GET['colaborador_id'] ?? 0);
        $colaborador = $this->colaboradorModel->buscarPorId($colaboradorId);

        if ($colaborador === null) {
            $this->flash('error', 'Colaborador no encontrado.');
            $this->redirigir('colaborador/listado');
        }

        $perfilActivo = $this->perfilModel->buscarActivoPorColaborador($colaboradorId);

        $this->render('perfil/formulario', [
            'colaborador'    => $colaborador,
            'perfilActivo'   => $perfilActivo, // si existe, este formulario actuará como "promoción"
            'ocupaciones'    => (new CatOcupacion())->activos(),
            'tiposPlanilla'  => (new CatTipoPlanilla())->todos(),
            'tiposEmpleado'  => (new CatTipoEmpleado())->activos(),
            'motivosBaja'    => (new CatMotivoTerminacion())->todos(),
            'errores'        => $_SESSION['errores'] ?? [],
        ]);
        unset($_SESSION['errores']);
    }

    /** POST perfil/guardar - Crea el primer perfil laboral (sin perfil previo activo) */
    public function guardar(): void
    {
        $colaboradorId = (int) ($_POST['colaborador_id'] ?? 0);
        $errores = ValidationService::validatePerfilLaboral($_POST);

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $this->redirigir('perfil/crear', ['colaborador_id' => $colaboradorId]);
        }

        $datos = SanitizerService::sanitizePerfilLaboral($_POST);
        $this->perfilModel->crear($datos);

        $this->flash('exito', 'Perfil laboral registrado correctamente.');
        $this->redirigir('colaborador/listado');
    }

    /**
     * POST perfil/promover - Escenario de PROMOCIÓN.
     * Desactiva el perfil activo actual y crea uno nuevo con el nuevo cargo.
     * (Requisito obligatorio: -5 pts si no se implementa esto)
     */
    public function promover(): void
    {
        $colaboradorId = (int) ($_POST['colaborador_id'] ?? 0);
        $errores = ValidationService::validatePerfilLaboral($_POST);

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $this->redirigir('perfil/crear', ['colaborador_id' => $colaboradorId]);
        }

        $datos = SanitizerService::sanitizePerfilLaboral($_POST);

        $this->perfilModel->promover($colaboradorId, $datos);

        $this->flash('exito', 'Colaborador promovido correctamente. El cargo anterior quedó en el historial.');
        $this->redirigir('colaborador/listado');
    }

    /**
     * POST perfil/finalizar - Da de baja al colaborador:
     * marca perfil inactivo + empleado_activo = 0 + motivo de baja.
     */
    public function finalizar(): void
    {
        $perfilId = (int) ($_POST['perfil_id'] ?? 0);
        $fechaFin = $_POST['fecha_fin'] ?? date('Y-m-d');
        $motivoBajaId = (int) ($_POST['motivo_baja_id'] ?? 0);

        if (!ValidationService::isRequired($motivoBajaId) || $motivoBajaId === 0) {
            $this->flash('error', 'Debe indicar el motivo de baja.');
            $this->redirigir('colaborador/listado');
        }

        $this->perfilModel->finalizar($perfilId, $fechaFin, $motivoBajaId);

        $this->flash('exito', 'Colaborador dado de baja correctamente.');
        $this->redirigir('colaborador/listado');
    }
}

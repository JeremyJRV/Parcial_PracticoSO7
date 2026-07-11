<?php

namespace App\Controllers;

use App\Models\Colaborador;
use App\Models\CatRuta;
use App\Models\CatSexo;
use App\Models\CatTipoSangre;
use App\Models\CatMotivoTerminacion;
use App\Services\ValidationService;
use App\Services\SanitizerService;

class ColaboradorController extends ControllerBase
{
    private Colaborador $colaboradorModel;

    public function __construct()
    {
        $this->colaboradorModel = new Colaborador();
    }

    /** GET colaborador/crear - Muestra el formulario de registro */
    public function mostrarFormulario(): void
    {
        $this->render('colaborador/formulario', [
            'rutas'        => (new CatRuta())->todos(),
            'sexos'        => (new CatSexo())->todos(),
            'tiposSangre'  => (new CatTipoSangre())->todos(),
            'errores'      => $_SESSION['errores'] ?? [],
            'datosPrevios' => $_SESSION['datosPrevios'] ?? [],
        ]);
        unset($_SESSION['errores'], $_SESSION['datosPrevios']);
    }

    /** POST colaborador/guardar - Valida, sanitiza y guarda el colaborador */
    public function guardar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('colaborador/crear');
        }

        // 1. Validar datos crudos (antes de sanitizar, para checar formato tal cual lo escribió el usuario)
        $errores = ValidationService::validateColaborador($_POST);

        // 2. Sanitizar
        $datos = SanitizerService::sanitizeColaborador($_POST);

        // 3. Validaciones que dependen de la BD (unicidad)
        if ($this->colaboradorModel->buscarPorIdentidad($datos['identidad']) !== null) {
            $errores['identidad'] = 'Ya existe un colaborador con esa identidad.';
        }
        if ($this->colaboradorModel->existeCorreo($datos['correo'])) {
            $errores['correo'] = 'Ese correo ya está registrado.';
        }
        if ($this->colaboradorModel->existeCelular($datos['celular'])) {
            $errores['celular'] = 'Ese celular ya está registrado.';
        }

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['datosPrevios'] = $_POST;
            $this->redirigir('colaborador/crear');
        }

        $id = $this->colaboradorModel->crear($datos);

        $this->flash('exito', 'Colaborador registrado correctamente.');
        $this->redirigir('perfil/crear', ['colaborador_id' => $id]);
    }

    /** GET colaborador/listado - Lista todos los colaboradores con su perfil activo */
    public function listado(): void
    {
        $this->render('colaborador/listado', [
            'colaboradores' => $this->colaboradorModel->todosConPerfilActivo(),
            'motivosBaja'   => (new CatMotivoTerminacion())->todos(),
        ]);
    }

    public function mostrarFormularioEdicion(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $colaborador = $this->colaboradorModel->buscarPorId($id);

        if ($colaborador === null) {
            $this->flash('error', 'Colaborador no encontrado.');
            $this->redirigir('colaborador/listado');
        }

        $this->render('colaborador/formulario', [
            'rutas'        => (new CatRuta())->todos(),
            'sexos'        => (new CatSexo())->todos(),
            'tiposSangre'  => (new CatTipoSangre())->todos(),
            'errores'      => [],
            'datosPrevios' => $colaborador,
            'editando'     => true,
        ]);
    }

    public function actualizar(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        $errores = ValidationService::validateColaborador($_POST);
        $datos = SanitizerService::sanitizeColaborador($_POST);

        if ($this->colaboradorModel->existeCorreo($datos['correo'], $id)) {
            $errores['correo'] = 'Ese correo ya está registrado por otro colaborador.';
        }
        if ($this->colaboradorModel->existeCelular($datos['celular'], $id)) {
            $errores['celular'] = 'Ese celular ya está registrado por otro colaborador.';
        }

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['datosPrevios'] = $_POST;
            $this->redirigir('colaborador/editar', ['id' => $id]);
        }

        $this->colaboradorModel->actualizar($id, $datos);
        $this->flash('exito', 'Colaborador actualizado correctamente.');
        $this->redirigir('colaborador/listado');
    }
}

<?php

require_once __DIR__ . '/Conexion.php';

/**
 * Clase UsuarioDatos
 *
 * Esta clase pertenece a la capa de datos.
 * Se encarga de ejecutar las consultas SQL relacionadas con la tabla tbl_usuarios.
 */
class UsuarioDatos
{
    private $conexion;

    /**
     * Constructor de la clase.
     *
     * Inicializa una instancia de la clase Conexion.
     */
    public function __construct()
    {
        $this->conexion = new Conexion();
    }

    /**
     * Busca un usuario activo por su nombre de usuario.
     *
     * Este método se utiliza durante el proceso de login.
     * Retorna los datos del usuario si existe y no está eliminado.
     *
     * @param string $usuario Nombre de usuario ingresado en el formulario.
     * @return array|null Retorna los datos del usuario o null si no existe.
     */
    public function obtenerUsuarioPorNombre($usuario)
    {
        $this->conexion->query = "SELECT IdUsuario, NombreCompleto, Usuario, Password, TipoCuenta
                                    FROM tbl_usuarios
                                    WHERE Usuario = :usuario
                                    AND Eliminado = 'N'
                                    LIMIT 1";

        $resultado = $this->conexion->get_record([
            ':usuario' => $usuario
        ]);

        return $resultado ?: null;
    }
    
}

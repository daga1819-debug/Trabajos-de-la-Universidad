<?php

require_once __DIR__ . '/../../config/database.php';

/*
    Esta clase representa el acceso a la tabla usuarios
 */
class Usuario
{

    // Busca un usuario mediante su correo electrónico

    public static function buscarPorCorreo(string $correo): ?array
    {
        $conexion = Database::conectar();

        /*
        El marcador :correo evita concatenar directamente información ingresada por el usuario
        Esto protege contra inyección SQL
         */
        $sql = '
            SELECT
                id_usuario,
                nombre,
                correo,
                telefono,
                fotografia,
                contrasena,
                rol,
                estado,
                fecha_registro
            FROM usuarios
            WHERE correo = :correo
            LIMIT 1
        ';

        $consulta = $conexion->prepare($sql);

        $consulta->execute([
            'correo' => strtolower(trim($correo)),
        ]);
        // fetch() devuelve false cuando no encuentra registros.
        $usuario = $consulta->fetch();

        return $usuario !== false ? $usuario : null;
    }

    /*
        Verifica el correo y la contraseña de un usuario.
     */
    public static function autenticar(
        string $correo,
        string $contrasena
    ): ?array {
        $usuario = self::buscarPorCorreo($correo);


        // Se devuelve null cuando el correo no existe, el usuario está inactivo o la contraseña es incorrecta.

        if ($usuario === null) {
            return null;
        }

        if ($usuario['estado'] !== 'activo') {
            return null;
        }

        if (!password_verify($contrasena, $usuario['contrasena'])) {
            return null;
        }

        // La contraseña nunca debe almacenarse en la sesión ni enviarse a una vista.

        unset($usuario['contrasena']);

        return $usuario;
    }

    /*
        Registra un nuevo usuario cliente
     */
    public static function crear(
        string $nombre,
        string $correo,
        string $telefono,
        string $contrasena
    ): bool {
        $conexion = Database::conectar();

        $sql = '
            INSERT INTO usuarios (
                nombre,
                correo,
                telefono,
                contrasena,
                rol,
                estado
            ) VALUES (
                :nombre,
                :correo,
                :telefono,
                :contrasena,
                :rol,
                :estado
            )
        ';

        $consulta = $conexion->prepare($sql);

        /*
            Password_hash() transforma la contraseña en un hash seguro antes de guardarla.
         */
        $hash = password_hash(
            $contrasena,
            PASSWORD_DEFAULT
        );

        return $consulta->execute([
            'nombre' => trim($nombre),
            'correo' => strtolower(trim($correo)),
            'telefono' => trim($telefono),
            'contrasena' => $hash,
            'rol' => 'cliente',
            'estado' => 'activo',
        ]);
    }

    //  Verifica si ya existe un correo registrado.
    public static function existeCorreo(string $correo): bool
    {
        return self::buscarPorCorreo($correo) !== null;
    }
}

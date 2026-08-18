<?php

require_once __DIR__ . '/../../config/database.php';

/*
    Modelo responsable de autenticación, registro y recuperación de usuarios
 */
class Usuario
{
    /*
        Busca un usuario por correo electrónico
     */
    public static function buscarPorCorreo(string $correo): ?array
    {
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

        $consulta = Database::conectar()->prepare($sql);
        $consulta->execute([
            'correo' => strtolower(trim($correo)),
        ]);

        $usuario = $consulta->fetch();

        return $usuario !== false ? $usuario : null;
    }

    /*
        Valida correo, estado y contraseña. La contraseña nunca se devuelve a la vista
     */
    public static function autenticar(
        string $correo,
        string $contrasena
    ): ?array {
        $usuario = self::buscarPorCorreo($correo);

        if (
            $usuario === null
            || $usuario['estado'] !== 'activo'
            || !password_verify($contrasena, $usuario['contrasena'])
        ) {
            return null;
        }

        unset($usuario['contrasena']);

        return $usuario;
    }

    /*
        Registra un cliente nuevo y guarda únicamente el hash de su contraseña
     */
    public static function crear(
        string $nombre,
        string $correo,
        string $telefono,
        string $contrasena
    ): bool {
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

        $consulta = Database::conectar()->prepare($sql);

        return $consulta->execute([
            'nombre' => trim($nombre),
            'correo' => strtolower(trim($correo)),
            'telefono' => trim($telefono),
            'contrasena' => password_hash($contrasena, PASSWORD_DEFAULT),
            'rol' => 'cliente',
            'estado' => 'activo',
        ]);
    }

    /*
        Indica si el correo ya está registrado
     */
    public static function existeCorreo(string $correo): bool
    {
        return self::buscarPorCorreo($correo) !== null;
    }

    /*
        Genera un token temporal de recuperación válido durante 30 minutos
        En la base de datos se almacena el hash del token, no el token original
     */
    public static function crearTokenRecuperacion(string $correo): ?string
    {
        if (!self::existeCorreo($correo)) {
            return null;
        }

        $token = bin2hex(random_bytes(24));
        $consulta = Database::conectar()->prepare(
            'UPDATE usuarios
            SET token_recuperacion = :token,
                token_expiracion = DATE_ADD(NOW(), INTERVAL 30 MINUTE)
            WHERE correo = :correo'
        );

        $consulta->execute([
            'token' => hash('sha256', $token),
            'correo' => strtolower(trim($correo)),
        ]);

        return $token;
    }

    /*
        Reemplaza la contraseña cuando el token existe y todavía no ha vencido
     */
    public static function restablecerContrasena(
        string $token,
        string $contrasena
    ): bool {
        $conexion = Database::conectar();
        $consulta = $conexion->prepare(
            'SELECT id_usuario
            FROM usuarios
            WHERE token_recuperacion = :token
            AND token_expiracion > NOW()
            LIMIT 1'
        );

        $consulta->execute([
            'token' => hash('sha256', $token),
        ]);

        $usuario = $consulta->fetch();

        if ($usuario === false) {
            return false;
        }

        $consulta = $conexion->prepare(
            'UPDATE usuarios
            SET contrasena = :contrasena,
                token_recuperacion = NULL,
                token_expiracion = NULL
            WHERE id_usuario = :id'
        );

        return $consulta->execute([
            'contrasena' => password_hash($contrasena, PASSWORD_DEFAULT),
            'id' => $usuario['id_usuario'],
        ]);
    }
}

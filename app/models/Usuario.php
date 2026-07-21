<?php

/*
    Modelo temporal de usuario queontiene datos simulados para demostrar la autenticación.
 */
class Usuario
{
    /*
        Devuelve un arreglo con usuarios de prueba.
        Cunado este completo, los usuarios se almacenarán en MySQL y se consultarán mediante PDO.
     */
    public static function obtenerUsuariosPrueba(): array
    {
        return [
            [
                'nombre' => 'Administrador del sistema',
                'correo' => 'admin@viajarespuravida.cr',
                'contrasena' => 'Admin123',
                'rol' => 'administrador',
            ],
            [
                'nombre' => 'Cliente de prueba',
                'correo' => 'cliente@viajarespuravida.cr',
                'contrasena' => 'Cliente123',
                'rol' => 'cliente',
            ],
        ];
    }

    /*
        Busca un usuario por correo y contraseña.
     */
    public static function autenticar(string $correo, string $contrasena): ?array
    {
        foreach (self::obtenerUsuariosPrueba() as $usuario) {
            if (
                strtolower($usuario['correo']) === strtolower($correo)
                && $usuario['contrasena'] === $contrasena
            ) {
                return $usuario;
            }
        }

        return null;
    }
}

<?php

/*
    Gestiona una única conexión PDO con MySQL durante cada solicitud
 */
class Database
{
    private const HOST = '127.0.0.1';
    private const DB_NAME = 'viajar_es_pura_vida';
    private const USERNAME = 'root';
    private const PASSWORD = '';
    private const CHARSET = 'utf8mb4';

    private static ?PDO $conexion = null;

    private function __construct() {}

    /*
        Devuelve la conexión PDO configurada con excepciones y consultas preparadas reales. Si ya existe una conexión, reutiliza la misma
     */
    public static function conectar(): PDO
    {
        if (self::$conexion instanceof PDO) {
            return self::$conexion;
        }

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            self::HOST,
            self::DB_NAME,
            self::CHARSET
        );

        try {
            self::$conexion = new PDO(
                $dsn,
                self::USERNAME,
                self::PASSWORD,
                [
                    // Los errores de MySQL se convierten en excepciones controlables
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    // Las consultas devuelven arreglos asociativos por defecto
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    // Se utilizan consultas preparadas reales proporcionadas por MySQL
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );

            return self::$conexion;
        } catch (PDOException $excepcion) {
            error_log(
                '[' . date('Y-m-d H:i:s') . '] Error de conexión: '
                    . $excepcion->getMessage()
                    . PHP_EOL,
                3,
                __DIR__ . '/../storage/logs/errores.log'
            );

            throw new RuntimeException(
                'No fue posible conectar con la base de datos.'
            );
        }
    }
}

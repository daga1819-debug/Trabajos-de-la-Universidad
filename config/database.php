<?php
/*
    Clase de responsable de crear y proporcionar la conexión con MYsql
    */
class Database
{
    // Dirección del servidor de base de datos
    private const HOST = '127.0.0.1';
    // Nombre de la base de datos
    private const DB_NAME = 'viajar_es_pura_vida';
    // Usuario de la base de datos
    private const USERNAME = 'root';
    // Contraseña del usuario de la base de datos
    private const PASSWORD = '';
    //Codificación de caracteres para la conexión
    private const CHARSET = 'utf8mb4';

    /*
        Guarda una única instancia de la conexión
        Esto es para que no se abra una nueva conexión cada vez que se necesite acceder a la base de datos
    */
    private static ?PDO $conexion = null;

    // Constructor privado que impide la creación de instancias de la clase desde fuera
    private function __construct() {}

    /*
        Devuelve la instancia de la conexión PDO
        @throws PDOException si no se puede establecer la conexión
    */

    public static function conectar(): PDO
    {
        /*
            Si la conexión ya fue creada anteriormente simplemente se devuelve la misma instancia
         */
        if (self::$conexion instanceof PDO) {
            return self::$conexion;
        }

        /*
            Data Source Name, esto contiene los datos que PDO necesita para localizar MySQL
         */
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
                    // Hace que PDO lance excepciones cuando ocurre un error
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

                    // Hace que los resultados se devuelvan como arreglos asociativos
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

                    // Obliga a utilizar consultas preparadas reales proporcionadas por MySQL
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );

            return self::$conexion;
        } catch (PDOException $excepcion) {
            /*
                El error técnico se registra internamente, no se muestra directamente al usuario
             */
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

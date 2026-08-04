<?php
/*
    Function para detectar el sistema operativo y usar los datos de conexión adecuados
 */
function getDatabaseConfig(): array
{
    $os = strtoupper(substr(PHP_OS, 0, 3));

    if ($os === 'WIN') {
        // Configuración habitual de mysql en xampp para Windows
        return [
            'host' => 'localhost',
            'port' => '3306',
            'dbname' => 'archivo_fantasma',
            'username' => 'root',
            'password' => '',
            'socket' => null
        ];
    }

    // Posibles rutas del socket de mysql en macOS
    $possibleSockets = [
        '/tmp/mysql.sock',
        '/Applications/MAMP/tmp/mysql/mysql.sock',
        '/opt/homebrew/var/mysql/mysql.sock',
        '/usr/local/var/mysql/mysql.sock'
    ];

    $socket = null;

    // Se utiliza el primer socket que exista en el equipo
    foreach ($possibleSockets as $possibleSocket) {
        if (file_exists($possibleSocket)) {
            $socket = $possibleSocket;
            break;
        }
    }

    // Posibles credenciales de mysql en macOS, dependiendo de la instalación
    return [
        'host' => 'localhost',
        'port' => '3306',
        'dbname' => 'archivo_fantasma',
        'username' => 'devuser',
        'password' => 'devpass123',
        'socket' => $socket
    ];
}

/*
    Función para obtener la conexión PDO a la base de datos MySQL
 */
function getConnection(): PDO
{
    $config = getDatabaseConfig();

    // En macOS se usa el socket si fue encontrado; en caso contrario se usa host y puerto
    if (!empty($config['socket'])) {
        $dsn = "mysql:unix_socket={$config['socket']};dbname={$config['dbname']};charset=utf8mb4";
    } else {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset=utf8mb4";
    }

    return new PDO(
        $dsn,
        $config['username'],
        $config['password'],
        [
            // Convierte los errores de MySQL en excepciones controlables
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // Devuelve cada fila como un arreglo asociativo
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Utiliza consultas preparadas reales cuando el servidor lo permite
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
}
?>
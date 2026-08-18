<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/models/Sistema.php';

session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$idDestino = (int) ($_GET['id'] ?? $_POST['id_destino'] ?? 0);
$destino = Sistema::buscar('destinos', $idDestino);

if ($destino === null) {
    http_response_code(404);
    exit('Destino no encontrado.');
}

// Los comentarios se procesan antes de cargar el resto de la página para aplicar
// el patrón POST/Redirect/GET y evitar publicaciones duplicadas al actualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tokenFormulario = (string) ($_POST['csrf_token'] ?? '');
    $comentario = trim((string) ($_POST['comentario'] ?? ''));
    $calificacion = (int) ($_POST['calificacion'] ?? 0);

    try {
        if (!hash_equals((string) $_SESSION['csrf_token'], $tokenFormulario)) {
            throw new RuntimeException('La solicitud no es válida.');
        }

        if ($comentario === '' || mb_strlen($comentario) > 800) {
            throw new RuntimeException(
                'El comentario debe contener entre 1 y 800 caracteres.'
            );
        }

        if ($calificacion < 1 || $calificacion > 5) {
            throw new RuntimeException('La calificación debe estar entre 1 y 5.');
        }

        Sistema::comentar(
            (int) $_SESSION['usuario']['id_usuario'],
            $idDestino,
            $comentario,
            $calificacion
        );

        $_SESSION['mensaje_destino'] = 'El comentario se publicó correctamente.';
    } catch (Throwable $excepcion) {
        $_SESSION['error_destino'] = $excepcion instanceof RuntimeException
            ? $excepcion->getMessage()
            : 'No fue posible publicar el comentario.';
    }

    header('Location: destino.php?id=' . $idDestino . '#comentarios');
    exit;
}

$hoteles = Sistema::hotelesPorDestino($idDestino);
$actividades = Sistema::actividadesPorDestino($idDestino);
$comentarios = Sistema::comentarios($idDestino);
$mensaje = (string) ($_SESSION['mensaje_destino'] ?? '');
$error = (string) ($_SESSION['error_destino'] ?? '');
unset($_SESSION['mensaje_destino'], $_SESSION['error_destino']);

// Open-Meteo se consulta solo cuando el destino tiene coordenadas registradas
$clima = null;

if ($destino['latitud'] !== null && $destino['longitud'] !== null) {
    $urlClima = 'https://api.open-meteo.com/v1/forecast'
        . '?latitude=' . urlencode($destino['latitud'])
        . '&longitude=' . urlencode($destino['longitud'])
        . '&current=temperature_2m,weather_code,wind_speed_10m'
        . '&timezone=America%2FCosta_Rica';

    $contexto = stream_context_create([
        'http' => [
            'timeout' => 3,
        ],
    ]);

    $respuesta = @file_get_contents($urlClima, false, $contexto);

    if ($respuesta !== false) {
        $datosClima = json_decode($respuesta, true);

        if (is_array($datosClima) && isset($datosClima['current'])) {
            $clima = $datosClima;
        }
    }
}

$esAdministrador = $_SESSION['usuario']['rol'] === 'administrador';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($destino['nombre']) ?> | <?= APP_NAME ?></title>
    <link rel="stylesheet" href="assets/css/estilos.css">
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
</head>

<body class="pagina-sistema">
    <header class="barra-sistema">
        <a class="marca-sistema" href="principal.php">
            🌿 Viajar es Pura Vida
        </a>

        <nav>
            <a href="principal.php">Inicio</a>
            <a href="sistema.php?modulo=destinos">Destinos</a>
            <a href="sistema.php?modulo=reservaciones">Reservaciones</a>
            <a href="cerrar_sesion.php">Salir</a>
        </nav>
    </header>

    <main class="contenido-sistema">
        <section class="detalle-destino">
            <img
                src="<?= e(rutaImagen($destino['imagen_principal'], 'destinos')) ?>"
                alt="<?= e($destino['nombre']) ?>">

            <div>
                <span class="etiqueta"><?= e($destino['provincia']) ?></span>
                <h1><?= e($destino['nombre']) ?></h1>
                <p><?= e($destino['descripcion']) ?></p>
                <p><strong>Ubicación:</strong> <?= e($destino['ubicacion']) ?></p>

                <?php if ($clima !== null): ?>
                    <p>
                        <strong>Clima actual:</strong>
                        <?= e($clima['current']['temperature_2m']) ?> °C,
                        viento de <?= e($clima['current']['wind_speed_10m']) ?> km/h
                    </p>
                <?php else: ?>
                    <p>El clima no está disponible en este momento.</p>
                <?php endif; ?>
            </div>
        </section>

        <?php if ($destino['latitud'] !== null && $destino['longitud'] !== null): ?>
            <div id="mapa" class="mapa-destino" aria-label="Mapa del destino"></div>
        <?php endif; ?>

        <section class="seccion-detalle">
            <h2>Hoteles en <?= e($destino['nombre']) ?></h2>

            <div class="rejilla-catalogo">
                <?php foreach ($hoteles as $hotel): ?>
                    <article class="tarjeta-catalogo">
                        <img
                            class="imagen-catalogo"
                            src="<?= e(rutaImagen($hotel['imagen'], 'hoteles')) ?>"
                            alt="<?= e($hotel['nombre']) ?>">

                        <h3><?= e($hotel['nombre']) ?></h3>
                        <p><?= e($hotel['descripcion']) ?></p>
                        <strong>
                            ₡<?= number_format((float) $hotel['precio_noche'], 2) ?>
                            por noche
                        </strong>

                        <?php if ($esAdministrador): ?>
                            <a
                                class="boton boton-secundario boton-tarjeta"
                                href="sistema.php?modulo=hoteles&editar=<?= (int) $hotel['id_hotel'] ?>#formulario-mantenimiento">
                                Administrar hotel
                            </a>
                        <?php else: ?>
                            <a
                                class="boton boton-principal boton-tarjeta"
                                href="sistema.php?modulo=reservaciones&destino=<?= $idDestino ?>&hotel=<?= (int) $hotel['id_hotel'] ?>#formulario-reservacion">
                                Reservar hotel
                            </a>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if ($hoteles === []): ?>
                <p>Este destino todavía no tiene hoteles registrados.</p>
            <?php endif; ?>
        </section>

        <section class="seccion-detalle">
            <h2>Actividades disponibles</h2>

            <div class="rejilla-catalogo">
                <?php foreach ($actividades as $actividad): ?>
                    <article class="tarjeta-catalogo">
                        <img
                            class="imagen-catalogo"
                            src="<?= e(rutaImagen($actividad['imagen'], 'actividades')) ?>"
                            alt="<?= e($actividad['nombre']) ?>">

                        <h3><?= e($actividad['nombre']) ?></h3>
                        <p><?= e($actividad['descripcion']) ?></p>
                        <strong>
                            ₡<?= number_format((float) $actividad['precio'], 2) ?>
                        </strong>

                        <?php if ($esAdministrador): ?>
                            <a
                                class="boton boton-secundario boton-tarjeta"
                                href="sistema.php?modulo=actividades&editar=<?= (int) $actividad['id_actividad'] ?>#formulario-mantenimiento">
                                Administrar actividad
                            </a>
                        <?php else: ?>
                            <a
                                class="boton boton-principal boton-tarjeta"
                                href="sistema.php?modulo=reservaciones&destino=<?= $idDestino ?>&actividad=<?= (int) $actividad['id_actividad'] ?>#formulario-reservacion">
                                Agregar a reservación
                            </a>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if ($actividades === []): ?>
                <p>Este destino todavía no tiene actividades registradas.</p>
            <?php endif; ?>
        </section>

        <section class="panel-formulario" id="comentarios">
            <h2>Comentarios y calificaciones</h2>

            <?php if ($mensaje !== ''): ?>
                <div class="mensaje-exito"><?= e($mensaje) ?></div>
            <?php endif; ?>

            <?php if ($error !== ''): ?>
                <div class="mensaje mensaje-error"><?= e($error) ?></div>
            <?php endif; ?>

            <form class="formulario-crud" method="POST">
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="id_destino" value="<?= $idDestino ?>">

                <label>
                    Calificación
                    <select name="calificacion" required>
                        <?php for ($numero = 5; $numero >= 1; $numero--): ?>
                            <option value="<?= $numero ?>">
                                <?= $numero ?> estrellas
                            </option>
                        <?php endfor; ?>
                    </select>
                </label>

                <label class="ancho-completo">
                    Comentario
                    <textarea name="comentario" maxlength="800" required></textarea>
                </label>

                <button class="boton boton-principal" type="submit">
                    Publicar comentario
                </button>
            </form>

            <?php foreach ($comentarios as $comentario): ?>
                <article class="comentario-destino">
                    <strong>
                        <?= e($comentario['usuario']) ?>
                        ·
                        <?= str_repeat('★', (int) $comentario['calificacion']) ?>
                    </strong>
                    <p><?= e($comentario['comentario']) ?></p>
                </article>
            <?php endforeach; ?>
        </section>
    </main>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <?php if ($destino['latitud'] !== null && $destino['longitud'] !== null): ?>
        <script>
            const coordenadas = [
                <?= json_encode((float) $destino['latitud']) ?>,
                <?= json_encode((float) $destino['longitud']) ?>
            ];

            const mapa = L.map('mapa').setView(coordenadas, 12);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(mapa);

            L.marker(coordenadas).addTo(mapa);
        </script>
    <?php endif; ?>
</body>

</html>
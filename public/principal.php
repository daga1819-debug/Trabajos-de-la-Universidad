<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/models/Sistema.php';

session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

$usuario = $_SESSION['usuario'];
$destinos = Sistema::listarActivos('destinos');
$hoteles = Sistema::listarActivos('hoteles');
$actividades = Sistema::listarActivos('actividades');

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta
        name="description"
        content="Destinos, hoteles y actividades turísticas de Costa Rica.">
    <title>Inicio | <?= APP_NAME ?></title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>

<body>
    <header class="encabezado-principal">
        <a class="marca" href="principal.php">
            <span>🌿</span>
            <div>
                <strong>Viajar es Pura Vida</strong>
                <small>Turismo nacional</small>
            </div>
        </a>

        <button
            class="boton-menu"
            type="button"
            aria-expanded="false"
            aria-label="Abrir menú">
            ☰
        </button>

        <nav class="menu-principal">
            <a href="#destinos">Destinos</a>
            <a href="#hoteles">Hoteles</a>
            <a href="#actividades">Actividades</a>
            <a href="sistema.php?modulo=reservaciones">Reservaciones</a>
            <a href="sistema.php?modulo=perfil">Mi perfil</a>

            <?php if ($usuario['rol'] === 'administrador'): ?>
                <a href="sistema.php">Administración</a>
            <?php endif; ?>
        </nav>

        <div class="sesion-usuario">
            <span><?= e($usuario['nombre']) ?></span>
            <a class="boton boton-rojo" href="cerrar_sesion.php">Salir</a>
        </div>
    </header>

    <main>
        <section class="carrusel" aria-label="Destinos destacados">
            <?php foreach ($destinos as $indice => $destino): ?>
                <article class="diapositiva <?= $indice === 0 ? 'activa' : '' ?>">
                    <img
                        src="<?= e(rutaImagen($destino['imagen_principal'], 'destinos')) ?>"
                        alt="<?= e($destino['nombre']) ?>">

                    <div class="capa-carrusel">
                        <div class="contenido-carrusel">
                            <span class="etiqueta-clara">
                                <?= e($destino['provincia']) ?>
                            </span>

                            <h<?= $indice === 0 ? '1' : '2' ?>>
                                <?= e($destino['nombre']) ?>
                            </h<?= $indice === 0 ? '1' : '2' ?>>

                            <p><?= e($destino['descripcion']) ?></p>

                            <a
                                class="boton boton-rojo"
                                href="destino.php?id=<?= (int) $destino['id_destino'] ?>">
                                Ver destino
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>

            <button class="control-carrusel anterior" type="button" aria-label="Anterior">
                ‹
            </button>

            <button class="control-carrusel siguiente" type="button" aria-label="Siguiente">
                ›
            </button>

            <div class="indicadores-carrusel">
                <?php foreach ($destinos as $indice => $destino): ?>
                    <button
                        class="indicador <?= $indice === 0 ? 'activo' : '' ?>"
                        type="button"
                        aria-label="Mostrar <?= e($destino['nombre']) ?>"></button>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="panel-busqueda" aria-label="Buscador turístico">
            <form class="formulario-busqueda" id="buscador-principal">
                <label class="grupo-campo">
                    ¿Qué deseas buscar?
                    <input
                        id="texto-busqueda"
                        type="search"
                        placeholder="Destino, hotel o actividad">
                </label>

                <label class="grupo-campo">
                    Categoría
                    <select id="tipo-busqueda">
                        <option value="todos">Todo</option>
                        <option value="destino">Destinos</option>
                        <option value="hotel">Hoteles</option>
                        <option value="actividad">Actividades</option>
                    </select>
                </label>

                <button class="boton boton-principal" type="submit">
                    Buscar
                </button>
            </form>
        </section>

        <section class="seccion" id="destinos">
            <div class="encabezado-seccion">
                <div>
                    <span class="etiqueta">Explorá Costa Rica</span>
                    <h2>Destinos disponibles</h2>
                </div>

                <a href="sistema.php?modulo=destinos">Ver todos →</a>
            </div>

            <div class="rejilla-destinos">
                <?php foreach ($destinos as $destino): ?>
                    <article
                        class="tarjeta-destino elemento-buscable"
                        data-tipo="destino"
                        data-busqueda="<?= e($destino['nombre'] . ' ' . $destino['provincia']) ?>">
                        <img
                            src="<?= e(rutaImagen($destino['imagen_principal'], 'destinos')) ?>"
                            alt="<?= e($destino['nombre']) ?>">

                        <div>
                            <span><?= e($destino['provincia']) ?></span>
                            <h3><?= e($destino['nombre']) ?></h3>
                            <p><?= e($destino['descripcion']) ?></p>

                            <a
                                class="boton boton-principal boton-tarjeta"
                                href="destino.php?id=<?= (int) $destino['id_destino'] ?>">
                                Ver hoteles y actividades
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="seccion seccion-clara" id="hoteles">
            <div class="encabezado-seccion">
                <div>
                    <span class="etiqueta">Hospedaje</span>
                    <h2>Hoteles disponibles</h2>
                </div>

                <a href="sistema.php?modulo=hoteles">Buscar hoteles →</a>
            </div>

            <div class="rejilla-hoteles">
                <?php foreach ($hoteles as $hotel): ?>
                    <article
                        class="tarjeta-hotel elemento-buscable"
                        data-tipo="hotel"
                        data-busqueda="<?= e($hotel['nombre'] . ' ' . $hotel['descripcion']) ?>">
                        <img
                            src="<?= e(rutaImagen($hotel['imagen'], 'hoteles')) ?>"
                            alt="<?= e($hotel['nombre']) ?>">

                        <div>
                            <span><?= (int) $hotel['categoria'] ?> estrellas</span>
                            <h3><?= e($hotel['nombre']) ?></h3>
                            <p><?= e($hotel['descripcion']) ?></p>
                            <strong>
                                ₡<?= number_format((float) $hotel['precio_noche'], 2) ?>
                            </strong>

                            <?php if ($usuario['rol'] === 'administrador'): ?>
                                <a
                                    class="boton boton-secundario boton-tarjeta"
                                    href="sistema.php?modulo=hoteles&editar=<?= (int) $hotel['id_hotel'] ?>#formulario-mantenimiento">
                                    Administrar hotel
                                </a>
                            <?php else: ?>
                                <a
                                    class="boton boton-principal boton-tarjeta"
                                    href="sistema.php?modulo=reservaciones&destino=<?= (int) $hotel['id_destino'] ?>&hotel=<?= (int) $hotel['id_hotel'] ?>#formulario-reservacion">
                                    Reservar este hotel
                                </a>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="seccion" id="actividades">
            <div class="encabezado-seccion">
                <div>
                    <span class="etiqueta">Experiencias</span>
                    <h2>Actividades turísticas</h2>
                </div>

                <a href="sistema.php?modulo=actividades">Ver actividades →</a>
            </div>

            <div class="rejilla-actividades">
                <?php foreach ($actividades as $actividad): ?>
                    <article
                        class="tarjeta-actividad elemento-buscable"
                        data-tipo="actividad"
                        data-busqueda="<?= e($actividad['nombre'] . ' ' . $actividad['descripcion']) ?>">
                        <img
                            src="<?= e(rutaImagen($actividad['imagen'], 'actividades')) ?>"
                            alt="<?= e($actividad['nombre']) ?>">

                        <div>
                            <h3><?= e($actividad['nombre']) ?></h3>
                            <p><?= e($actividad['descripcion']) ?></p>
                            <strong>
                                ₡<?= number_format((float) $actividad['precio'], 2) ?>
                            </strong>

                            <?php if ($usuario['rol'] === 'administrador'): ?>
                                <a
                                    class="boton boton-secundario boton-tarjeta"
                                    href="sistema.php?modulo=actividades&editar=<?= (int) $actividad['id_actividad'] ?>#formulario-mantenimiento">
                                    Administrar actividad
                                </a>
                            <?php else: ?>
                                <a
                                    class="boton boton-principal boton-tarjeta"
                                    href="sistema.php?modulo=reservaciones&destino=<?= (int) $actividad['id_destino'] ?>&actividad=<?= (int) $actividad['id_actividad'] ?>#formulario-reservacion">
                                    Agregar a reservación
                                </a>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="llamada-reserva">
            <div>
                <span class="etiqueta-clara">Planeá tu viaje</span>
                <h2>Elegí un destino y reservá opciones compatibles</h2>
            </div>

            <a
                class="boton boton-blanco"
                href="sistema.php?modulo=reservaciones">
                <?= $usuario['rol'] === 'administrador'
                    ? 'Ver reservaciones'
                    : 'Crear reservación' ?>
            </a>
        </section>
    </main>

    <script src="assets/js/principal.js"></script>
</body>

</html>
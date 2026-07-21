<?php
require_once __DIR__ . '/../config/config.php';
session_start();

// Protección de la página
if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Página principal.">
    <title>Inicio | <?= APP_NAME ?></title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>

<body>
    <?php require __DIR__ . '/../app/views/layouts/header.php'; ?>

    <main>
        <!-- Carrusel principal que se cambia automáticamente -->
        <section class="carrusel" id="inicio" aria-label="Destinos destacados">
            <article class="diapositiva activa">
                <img src="assets/img/arenal.jpg" alt="Ilustración del Volcán Arenal">
                <div class="capa-carrusel"></div>
                <div class="contenido-carrusel">
                    <span class="etiqueta-clara">La Fortuna, Alajuela</span>
                    <h1>Sentí la energía del Volcán Arenal</h1>
                    <p>Aguas termales, aventura y naturaleza en un destino inolvidable.</p>
                    <a class="boton boton-rojo" href="#destinos">Explorar destinos</a>
                </div>
            </article>

            <article class="diapositiva">
                <img src="assets/img/monteverde.jpg" alt="Ilustración del bosque nuboso de Monteverde">
                <div class="capa-carrusel"></div>
                <div class="contenido-carrusel">
                    <span class="etiqueta-clara">Monteverde, Puntarenas</span>
                    <h2>Caminá entre las nubes</h2>
                    <p>Senderos, canopy y biodiversidad en el corazón del bosque nuboso.</p>
                    <a class="boton boton-rojo" href="#actividades">Ver actividades</a>
                </div>
            </article>

            <article class="diapositiva">
                <img src="assets/img/manuel-antonio.jpg" alt="Ilustración de la playa de Manuel Antonio">
                <div class="capa-carrusel"></div>
                <div class="contenido-carrusel">
                    <span class="etiqueta-clara">Quepos, Puntarenas</span>
                    <h2>Playa, bosque y vida silvestre</h2>
                    <p>Viví una experiencia tropical en el Parque Nacional Manuel Antonio.</p>
                    <a class="boton boton-rojo" href="#hoteles">Buscar hoteles</a>
                </div>
            </article>

            <button class="control-carrusel anterior" type="button" aria-label="Diapositiva anterior">‹</button>
            <button class="control-carrusel siguiente" type="button" aria-label="Diapositiva siguiente">›</button>

            <div class="indicadores-carrusel" aria-label="Controles del carrusel">
                <button class="indicador activo" type="button" aria-label="Mostrar diapositiva 1"></button>
                <button class="indicador" type="button" aria-label="Mostrar diapositiva 2"></button>
                <button class="indicador" type="button" aria-label="Mostrar diapositiva 3"></button>
            </div>
        </section>

        <!-- Formulario visual de búsqueda boceto -->
        <section class="panel-busqueda" aria-labelledby="titulo-busqueda">
            <div class="encabezado-seccion compacto">
                <div>
                    <span class="etiqueta">Planeá tu viaje</span>
                    <h2 id="titulo-busqueda">¿Cuál será tu próximo destino?</h2>
                </div>
                <p>Este formulario funcionará con la base de datos en la siguiente entrega.</p>
            </div>

            <form class="formulario-busqueda" action="modulo_en_desarrollo.php" method="GET">
                <div class="grupo-campo">
                    <label for="destino">Destino</label>
                    <select id="destino" name="destino" required>
                        <option value="">Seleccionar destino</option>
                        <option value="arenal">La Fortuna y Arenal</option>
                        <option value="monteverde">Monteverde</option>
                        <option value="manuel-antonio">Manuel Antonio</option>
                        <option value="guanacaste">Guanacaste</option>
                        <option value="puerto-viejo">Puerto Viejo</option>
                        <option value="rio-celeste">Río Celeste</option>
                    </select>
                </div>

                <div class="grupo-campo">
                    <label for="entrada">Entrada</label>
                    <input type="date" id="entrada" name="entrada" required>
                </div>

                <div class="grupo-campo">
                    <label for="salida">Salida</label>
                    <input type="date" id="salida" name="salida" required>
                </div>

                <div class="grupo-campo">
                    <label for="personas">Personas</label>
                    <input type="number" id="personas" name="personas" min="1" max="20" value="2" required>
                </div>

                <button class="boton boton-principal" type="submit">Buscar opciones</button>
            </form>
        </section>
            <!-- Sección de destinos, hoteles y actividades -->
        <section class="seccion" id="destinos">
            <div class="encabezado-seccion">
                <div>
                    <span class="etiqueta">Conocé nuestro país</span>
                    <h2>Destinos destacados</h2>
                    <p>Una muestra del catálogo de destinos que tendrá el sistema completo.</p>
                </div>
                <a href="modulo_en_desarrollo.php">Ver todos los destinos →</a>
            </div>

            <div class="rejilla-tarjetas">
                <article class="tarjeta-destino">
                    <img src="assets/img/arenal.jpg" alt="Volcán Arenal">
                    <div class="contenido-tarjeta">
                        <span class="ubicacion">📍 Alajuela</span>
                        <h3>La Fortuna y Arenal</h3>
                        <p>Aguas termales, cataratas y aventura al pie del volcán.</p>
                        <div class="pie-tarjeta">
                            <strong>Desde ₡35.000</strong>
                            <a href="modulo_en_desarrollo.php">Ver destino</a>
                        </div>
                    </div>
                </article>

                <article class="tarjeta-destino">
                    <img src="assets/img/monteverde.jpg" alt="Monteverde">
                    <div class="contenido-tarjeta">
                        <span class="ubicacion">📍 Puntarenas</span>
                        <h3>Monteverde</h3>
                        <p>Bosque nuboso, senderos, puentes colgantes y canopy.</p>
                        <div class="pie-tarjeta">
                            <strong>Desde ₡28.000</strong>
                            <a href="modulo_en_desarrollo.php">Ver destino</a>
                        </div>
                    </div>
                </article>

                <article class="tarjeta-destino">
                    <img src="assets/img/manuel-antonio.jpg" alt="Manuel Antonio">
                    <div class="contenido-tarjeta">
                        <span class="ubicacion">📍 Puntarenas</span>
                        <h3>Manuel Antonio</h3>
                        <p>Playas, senderos y vida silvestre en un entorno tropical.</p>
                        <div class="pie-tarjeta">
                            <strong>Desde ₡42.000</strong>
                            <a href="modulo_en_desarrollo.php">Ver destino</a>
                        </div>
                    </div>
                </article>
            </div>
        </section>
            <!-- Sección de hoteles recomendados -->
        <section class="seccion seccion-clara" id="hoteles">
            <div class="encabezado-seccion">
                <div>
                    <span class="etiqueta">Descansá y disfrutá</span>
                    <h2>Hoteles recomendados</h2>
                    <p>El precio y la disponibilidad se conectarán con MySQL posteriormente.</p>
                </div>
                <a href="modulo_en_desarrollo.php">Buscar hoteles →</a>
            </div>

            <div class="rejilla-hoteles">
                <article class="tarjeta-hotel">
                    <div class="imagen-hotel hotel-arenal"><span>4.8 ★</span></div>
                    <div class="contenido-tarjeta">
                        <span class="ubicacion">La Fortuna</span>
                        <h3>Arenal Vista Lodge</h3>
                        <p>Habitaciones con vista al volcán, piscina y desayuno incluido.</p>
                        <strong>₡55.000 <small>/ noche</small></strong>
                    </div>
                </article>

                <article class="tarjeta-hotel">
                    <div class="imagen-hotel hotel-monteverde"><span>4.7 ★</span></div>
                    <div class="contenido-tarjeta">
                        <span class="ubicacion">Monteverde</span>
                        <h3>Bosque Nuboso Hotel</h3>
                        <p>Hospedaje acogedor cerca de reservas y senderos naturales.</p>
                        <strong>₡48.000 <small>/ noche</small></strong>
                    </div>
                </article>

                <article class="tarjeta-hotel">
                    <div class="imagen-hotel hotel-pacifico"><span>4.9 ★</span></div>
                    <div class="contenido-tarjeta">
                        <span class="ubicacion">Manuel Antonio</span>
                        <h3>Pacífico Tropical Resort</h3>
                        <p>Vista al océano, restaurante, piscina y acceso cercano a la playa.</p>
                        <strong>₡72.000 <small>/ noche</small></strong>
                    </div>
                </article>
            </div>
        </section>
            <!-- Sección de actividades populares -->
        <section class="seccion" id="actividades">
            <div class="encabezado-seccion">
                <div>
                    <span class="etiqueta">Aventura y naturaleza</span>
                    <h2>Actividades populares</h2>
                    <p>Opciones que el cliente podrá agregar a su reservación.</p>
                </div>
            </div>

            <div class="rejilla-actividades">
                <article class="tarjeta-actividad"><span>🌿</span>
                    <h3>Senderismo</h3>
                    <p>Recorridos por parques nacionales y reservas.</p>
                </article>
                <article class="tarjeta-actividad"><span>🧗</span>
                    <h3>Canopy</h3>
                    <p>Aventura entre árboles y puentes colgantes.</p>
                </article>
                <article class="tarjeta-actividad"><span>🚣</span>
                    <h3>Rafting</h3>
                    <p>Experiencias para distintos niveles de dificultad.</p>
                </article>
                <article class="tarjeta-actividad"><span>🤿</span>
                    <h3>Buceo</h3>
                    <p>Exploración de la riqueza marina costarricense.</p>
                </article>
            </div>
        </section>

        <section class="seccion banda-informativa" id="reservaciones">
            <div>
                <span class="etiqueta-clara">Próximamente</span>
                <h2>Reservá hospedaje y actividades en un solo proceso</h2>
                <p>
                    El sistema final almacenará fechas, cantidad de personas, hotel,
                    actividades, estado de la reserva e ingresos estimados.
                </p>
            </div>
            <a class="boton boton-blanco" href="modulo_en_desarrollo.php">Ver mis reservaciones</a>
        </section>
            <!-- Sección de resumen de módulos previstos -->
        <section class="seccion resumen-futuro">
            <div class="encabezado-seccion">
                <div>
                    <span class="etiqueta">Visión del proyecto completo</span>
                    <h2>Módulos previstos</h2>
                </div>
            </div>

            <div class="rejilla-modulos">
                <article><span>👤</span>
                    <h3>Perfil</h3>
                    <p>Datos personales, fotografía y contraseña.</p>
                </article>
                <article><span>❤️</span>
                    <h3>Favoritos</h3>
                    <p>Destinos, hoteles y actividades guardadas.</p>
                </article>
                <article><span>⭐</span>
                    <h3>Calificaciones</h3>
                    <p>Comentarios y opiniones de los clientes.</p>
                </article>
                <article><span>📊</span>
                    <h3>Reportes</h3>
                    <p>Reservas, ingresos y estadísticas generales.</p>
                </article>
                <article><span>🌦️</span>
                    <h3>Clima</h3>
                    <p>Integración futura con una API meteorológica.</p>
                </article>
                <article><span>💱</span>
                    <h3>Tipo de cambio</h3>
                    <p>Precios estimados en colones y dólares.</p>
                </article>
            </div>
        </section>
    </main>

    <?php require __DIR__ . '/../app/views/layouts/footer.php'; ?>
    <script src="assets/js/principal.js"></script>
</body>

</html>
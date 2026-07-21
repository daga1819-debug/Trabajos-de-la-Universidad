<header class="encabezado-principal">
    <a class="marca" href="principal.php" aria-label="Ir a la página principal">
        <span class="marca-icono">🌿</span>
        <span>
            <strong>Viajar es Pura Vida</strong>
            <small>Descubrí Costa Rica</small>
        </span>
    </a>

    <button class="boton-menu" type="button" aria-label="Abrir menú" aria-expanded="false">
        ☰
    </button>

    <nav class="menu-principal" aria-label="Navegación principal">
        <a href="#inicio">Inicio</a>
        <a href="#destinos">Destinos</a>
        <a href="#hoteles">Hoteles</a>
        <a href="#actividades">Actividades</a>
        <a href="#reservaciones">Reservaciones</a>
    </nav>

    <div class="sesion-usuario">
        <span>
            Hola,
            <strong><?= htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8') ?></strong>
        </span>
        <a class="boton boton-secundario" href="cerrar_sesion.php">Cerrar sesión</a>
    </div>
</header>
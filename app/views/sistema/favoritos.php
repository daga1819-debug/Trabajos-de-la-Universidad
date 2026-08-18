<section class="rejilla-catalogo">
    <?php foreach ($favoritos as $destino): ?>
        <article class="tarjeta-catalogo">
            <img
                class="imagen-catalogo"
                src="<?= e(rutaImagen($destino['imagen_principal'], 'destinos')) ?>"
                alt="<?= e($destino['nombre']) ?>">

            <h2><?= e($destino['nombre']) ?></h2>
            <p><?= e($destino['descripcion']) ?></p>

            <a
                class="boton boton-principal boton-tarjeta"
                href="destino.php?id=<?= (int) $destino['id_destino'] ?>">
                Ver destino
            </a>
        </article>
    <?php endforeach; ?>

    <?php if ($favoritos === []): ?>
        <p class="estado-vacio">
            Todavía no has agregado destinos a favoritos.
        </p>
    <?php endif; ?>
</section>
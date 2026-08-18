<?php if (!empty($usuario['fotografia'])): ?>
    <section class="perfil-actual">
        <img
            src="<?= e(rutaImagen($usuario['fotografia'], 'perfiles')) ?>"
            alt="Fotografía de <?= e($usuario['nombre']) ?>">
        <div>
            <strong>Fotografía actual</strong>
            <p>Podés reemplazarla seleccionando una imagen nueva.</p>
        </div>
    </section>
<?php endif; ?>

<form class="formulario-crud" method="POST" enctype="multipart/form-data">
    <input
        type="hidden"
        name="csrf_token"
        value="<?= e($_SESSION['csrf_token']) ?>">

    <input type="hidden" name="accion" value="perfil">

    <label>
        Nombre
        <input name="nombre" value="<?= e($usuario['nombre']) ?>" required>
    </label>

    <label>
        Correo electrónico
        <input
            type="email"
            name="correo"
            value="<?= e($usuario['correo']) ?>"
            required>
    </label>

    <label>
        Teléfono
        <input name="telefono" value="<?= e($usuario['telefono'] ?? '') ?>">
    </label>

    <label>
        Fotografía opcional
        <input
            type="file"
            name="fotografia"
            accept="image/jpeg,image/png,image/webp">
    </label>

    <label>
        Nueva contraseña
        <input
            type="password"
            name="contrasena"
            minlength="8"
            maxlength="72">
    </label>

    <button class="boton boton-principal" type="submit">
        Actualizar perfil
    </button>
</form>
<?php
$llaves = [
    'destinos' => 'id_destino',
    'hoteles' => 'id_hotel',
    'actividades' => 'id_actividad',
];
?>

<form class="buscador-modulo" method="GET">
    <input type="hidden" name="modulo" value="<?= e($modulo) ?>">

    <input
        type="search"
        name="buscar"
        value="<?= e($buscar) ?>"
        placeholder="Buscar por nombre<?= $modulo === 'destinos' ? ' o provincia' : '' ?>">

    <button class="boton boton-principal" type="submit">
        Buscar
    </button>
</form>

<?php if ($esAdministrador): ?>
    <?php
    $idEdicion = $edicion !== null
        ? (int) $edicion[$llaves[$modulo]]
        : 0;
    ?>

    <details
        id="formulario-mantenimiento"
        class="panel-formulario"
        <?= $edicion !== null ? 'open' : '' ?>>
        <summary>
            <?= $edicion !== null ? 'Editar registro' : 'Agregar nuevo registro' ?>
        </summary>

        <form
            class="formulario-crud"
            method="POST"
            enctype="multipart/form-data">
            <input
                type="hidden"
                name="csrf_token"
                value="<?= e($_SESSION['csrf_token']) ?>">

            <input type="hidden" name="id" value="<?= $idEdicion ?>">

            <?php if ($modulo === 'destinos'): ?>
                <input type="hidden" name="accion" value="guardar_destino">

                <input
                    type="hidden"
                    name="imagen_actual"
                    value="<?= e($edicion['imagen_principal'] ?? '') ?>">

                <label>
                    Nombre
                    <input
                        name="nombre"
                        maxlength="120"
                        value="<?= e($edicion['nombre'] ?? '') ?>"
                        required>
                </label>

                <label>
                    Provincia
                    <select name="provincia" required>
                        <?php
                        $provincias = [
                            'San José',
                            'Alajuela',
                            'Cartago',
                            'Heredia',
                            'Guanacaste',
                            'Puntarenas',
                            'Limón',
                        ];
                        ?>

                        <?php foreach ($provincias as $provincia): ?>
                            <option
                                value="<?= e($provincia) ?>"
                                <?= ($edicion['provincia'] ?? '') === $provincia
                                    ? 'selected'
                                    : '' ?>>
                                <?= e($provincia) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label class="ancho-completo">
                    Descripción
                    <textarea name="descripcion" required><?= e($edicion['descripcion'] ?? '') ?></textarea>
                </label>

                <label>
                    Imagen principal
                    <input
                        type="file"
                        name="imagen"
                        accept="image/jpeg,image/png,image/webp">
                </label>

                <label>
                    Ubicación
                    <input
                        name="ubicacion"
                        value="<?= e($edicion['ubicacion'] ?? '') ?>">
                </label>

                <label>
                    Latitud
                    <input
                        type="number"
                        step="any"
                        name="latitud"
                        value="<?= e($edicion['latitud'] ?? '') ?>">
                </label>

                <label>
                    Longitud
                    <input
                        type="number"
                        step="any"
                        name="longitud"
                        value="<?= e($edicion['longitud'] ?? '') ?>">
                </label>

            <?php elseif ($modulo === 'hoteles'): ?>
                <input type="hidden" name="accion" value="guardar_hotel">

                <input
                    type="hidden"
                    name="imagen_actual"
                    value="<?= e($edicion['imagen'] ?? '') ?>">

                <label>
                    Nombre
                    <input
                        name="nombre"
                        value="<?= e($edicion['nombre'] ?? '') ?>"
                        required>
                </label>

                <label>
                    Destino asociado
                    <select name="id_destino" required>
                        <?php foreach ($destinos as $destino): ?>
                            <option
                                value="<?= (int) $destino['id_destino'] ?>"
                                <?= (int) ($edicion['id_destino'] ?? 0)
                                    === (int) $destino['id_destino']
                                    ? 'selected'
                                    : '' ?>>
                                <?= e($destino['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    Categoría
                    <input
                        type="number"
                        min="1"
                        max="5"
                        name="categoria"
                        value="<?= e($edicion['categoria'] ?? 3) ?>"
                        required>
                </label>

                <label>
                    Precio por noche
                    <input
                        type="number"
                        min="0"
                        step="0.01"
                        name="precio_noche"
                        value="<?= e($edicion['precio_noche'] ?? '') ?>"
                        required>
                </label>

                <label>
                    Cantidad de habitaciones
                    <input
                        type="number"
                        min="0"
                        name="cantidad_habitaciones"
                        value="<?= e($edicion['cantidad_habitaciones'] ?? '') ?>"
                        required>
                </label>

                <label>
                    Dirección
                    <input
                        name="direccion"
                        value="<?= e($edicion['direccion'] ?? '') ?>"
                        required>
                </label>

                <label>
                    Teléfono
                    <input
                        name="telefono"
                        value="<?= e($edicion['telefono'] ?? '') ?>">
                </label>

                <label>
                    Correo electrónico
                    <input
                        type="email"
                        name="correo"
                        value="<?= e($edicion['correo'] ?? '') ?>">
                </label>

                <label>
                    Imagen del hotel
                    <input
                        type="file"
                        name="imagen_archivo"
                        accept="image/jpeg,image/png,image/webp">
                </label>

                <label class="ancho-completo">
                    Descripción
                    <textarea name="descripcion"><?= e($edicion['descripcion'] ?? '') ?></textarea>
                </label>

            <?php else: ?>
                <input type="hidden" name="accion" value="guardar_actividad">

                <input
                    type="hidden"
                    name="imagen_actual"
                    value="<?= e($edicion['imagen'] ?? '') ?>">

                <label>
                    Nombre
                    <input
                        name="nombre"
                        value="<?= e($edicion['nombre'] ?? '') ?>"
                        required>
                </label>

                <label>
                    Destino asociado
                    <select name="id_destino" required>
                        <?php foreach ($destinos as $destino): ?>
                            <option
                                value="<?= (int) $destino['id_destino'] ?>"
                                <?= (int) ($edicion['id_destino'] ?? 0)
                                    === (int) $destino['id_destino']
                                    ? 'selected'
                                    : '' ?>>
                                <?= e($destino['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    Tipo
                    <select name="tipo" required>
                        <?php
                        $tipos = [
                            'Canopy',
                            'Rafting',
                            'Senderismo',
                            'Buceo',
                            'Tour',
                            'Cabalgata',
                            'Otro',
                        ];
                        ?>

                        <?php foreach ($tipos as $tipo): ?>
                            <option
                                value="<?= e($tipo) ?>"
                                <?= ($edicion['tipo'] ?? '') === $tipo
                                    ? 'selected'
                                    : '' ?>>
                                <?= e($tipo) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    Precio
                    <input
                        type="number"
                        min="0"
                        step="0.01"
                        name="precio"
                        value="<?= e($edicion['precio'] ?? '') ?>"
                        required>
                </label>

                <label>
                    Duración en minutos
                    <input
                        type="number"
                        min="1"
                        name="duracion_minutos"
                        value="<?= e($edicion['duracion_minutos'] ?? '') ?>"
                        required>
                </label>

                <label>
                    Cupo máximo
                    <input
                        type="number"
                        min="1"
                        name="cupo_maximo"
                        value="<?= e($edicion['cupo_maximo'] ?? '') ?>"
                        required>
                </label>

                <label>
                    Imagen de la actividad
                    <input
                        type="file"
                        name="imagen_archivo"
                        accept="image/jpeg,image/png,image/webp">
                </label>

                <label class="ancho-completo">
                    Descripción
                    <textarea name="descripcion" required><?= e($edicion['descripcion'] ?? '') ?></textarea>
                </label>
            <?php endif; ?>

            <label>
                Estado
                <select name="estado">
                    <option
                        value="activo"
                        <?= ($edicion['estado'] ?? 'activo') === 'activo'
                            ? 'selected'
                            : '' ?>>
                        Activo
                    </option>

                    <option
                        value="inactivo"
                        <?= ($edicion['estado'] ?? '') === 'inactivo'
                            ? 'selected'
                            : '' ?>>
                        Inactivo
                    </option>
                </select>
            </label>

            <button class="boton boton-principal" type="submit">
                Guardar
            </button>
        </form>
    </details>
<?php endif; ?>

<?php $lista = ${$modulo}; ?>

<section class="rejilla-catalogo">
    <?php foreach ($lista as $fila): ?>
        <?php
        $llave = $llaves[$modulo] ?? '';
        $id = (int) $fila[$llave];
        $campoImagen = $modulo === 'destinos' ? 'imagen_principal' : 'imagen';
        ?>

        <article class="tarjeta-catalogo">
            <img
                class="imagen-catalogo"
                src="<?= e(rutaImagen($fila[$campoImagen] ?? '', $modulo)) ?>"
                alt="<?= e($fila['nombre']) ?>">

            <h2><?= e($fila['nombre']) ?></h2>

            <p>
                <?= e($fila['descripcion'] ?? $fila['provincia'] ?? '') ?>
            </p>

            <?php if (isset($fila['precio_noche'])): ?>
                <strong>
                    ₡<?= number_format((float) $fila['precio_noche'], 2) ?> por noche
                </strong>
            <?php endif; ?>

            <?php if (isset($fila['precio'])): ?>
                <strong>₡<?= number_format((float) $fila['precio'], 2) ?></strong>
            <?php endif; ?>

            <div class="acciones-tarjeta">
                <?php if ($modulo === 'destinos'): ?>
                    <a class="boton boton-principal" href="destino.php?id=<?= $id ?>">
                        Ver destino
                    </a>

                    <?php if (!$esAdministrador): ?>
                        <form method="POST">
                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= e($_SESSION['csrf_token']) ?>">

                            <input type="hidden" name="accion" value="favorito">
                            <input type="hidden" name="id_destino" value="<?= $id ?>">

                            <button class="boton boton-secundario" type="submit">
                                ♡ Favorito
                            </button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (!$esAdministrador && $modulo === 'hoteles'): ?>
                    <a
                        class="boton boton-principal"
                        href="?modulo=reservaciones&destino=<?= (int) $fila['id_destino'] ?>&hotel=<?= $id ?>#formulario-reservacion">
                        Reservar hotel
                    </a>
                <?php elseif (!$esAdministrador && $modulo === 'actividades'): ?>
                    <a
                        class="boton boton-principal"
                        href="?modulo=reservaciones&destino=<?= (int) $fila['id_destino'] ?>&actividad=<?= $id ?>#formulario-reservacion">
                        Agregar a reserva
                    </a>
                <?php endif; ?>

                <?php if ($esAdministrador): ?>
                    <a
                        class="boton"
                        href="?modulo=<?= e($modulo) ?>&editar=<?= $id ?>#formulario-mantenimiento">
                        Editar
                    </a>

                    <form
                        method="POST"
                        onsubmit="return confirm('¿Desea eliminar este registro?')">
                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= e($_SESSION['csrf_token']) ?>">

                        <input type="hidden" name="accion" value="eliminar">
                        <input type="hidden" name="entidad" value="<?= e($modulo) ?>">
                        <input type="hidden" name="id" value="<?= $id ?>">

                        <button class="boton boton-rojo" type="submit">
                            Eliminar
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </article>
    <?php endforeach; ?>

    <?php if ($lista === []): ?>
        <p class="estado-vacio">
            No se encontraron registros para los criterios indicados.
        </p>
    <?php endif; ?>
</section>
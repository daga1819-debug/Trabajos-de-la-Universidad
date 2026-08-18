<?php
$destinoSeleccionado = filter_input(INPUT_GET, 'destino', FILTER_VALIDATE_INT) ?: 0;
$hotelSeleccionado = filter_input(INPUT_GET, 'hotel', FILTER_VALIDATE_INT) ?: 0;
$actividadSeleccionada = filter_input(INPUT_GET, 'actividad', FILTER_VALIDATE_INT) ?: 0;
?>

<?php if (!$esAdministrador): ?>
    <form class="formulario-crud" method="POST" id="formulario-reservacion">
        <input
            type="hidden"
            name="csrf_token"
            value="<?= e($_SESSION['csrf_token']) ?>">

        <input type="hidden" name="accion" value="reservar">

        <label>
            Destino
            <select name="id_destino" id="reserva-destino" required>
                <option value="">Seleccione un destino</option>

                <?php foreach ($destinos as $destino): ?>
                    <?php if ($destino['estado'] === 'activo'): ?>
                        <option
                            value="<?= (int) $destino['id_destino'] ?>"
                            <?= (int) $destino['id_destino'] === $destinoSeleccionado
                                ? 'selected'
                                : '' ?>>
                            <?= e($destino['nombre']) ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Hotel
            <select name="id_hotel" id="reserva-hotel" required disabled>
                <option value="">Seleccione primero un destino</option>

                <?php foreach ($hoteles as $hotel): ?>
                    <?php if ($hotel['estado'] === 'activo'): ?>
                        <option
                            value="<?= (int) $hotel['id_hotel'] ?>"
                            data-destino="<?= (int) $hotel['id_destino'] ?>"
                            <?= (int) $hotel['id_hotel'] === $hotelSeleccionado
                                ? 'selected'
                                : '' ?>
                            hidden>
                            <?= e($hotel['nombre']) ?>
                            — ₡<?= number_format((float) $hotel['precio_noche']) ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Actividad opcional
            <select name="id_actividad" id="reserva-actividad" disabled>
                <option value="">Sin actividad</option>

                <?php foreach ($actividades as $actividad): ?>
                    <?php if ($actividad['estado'] === 'activo'): ?>
                        <option
                            value="<?= (int) $actividad['id_actividad'] ?>"
                            data-destino="<?= (int) $actividad['id_destino'] ?>"
                            <?= (int) $actividad['id_actividad'] === $actividadSeleccionada
                                ? 'selected'
                                : '' ?>
                            hidden>
                            <?= e($actividad['nombre']) ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Fecha de entrada
            <input
                type="date"
                name="fecha_inicio"
                min="<?= date('Y-m-d') ?>"
                required>
        </label>

        <label>
            Fecha de salida
            <input
                type="date"
                name="fecha_fin"
                min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                required>
        </label>

        <label>
            Cantidad de personas
            <input
                type="number"
                name="cantidad_personas"
                min="1"
                value="1"
                required>
        </label>

        <label>
            Cantidad de habitaciones
            <input
                type="number"
                name="cantidad_habitaciones"
                min="1"
                value="1"
                required>
        </label>

        <label class="ancho-completo">
            Observaciones
            <textarea name="observaciones"></textarea>
        </label>

        <button class="boton boton-principal" type="submit">
            Confirmar reservación
        </button>
    </form>
<?php endif; ?>

<div class="tabla-responsive">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Cliente</th>
                <th>Destino</th>
                <th>Fechas</th>
                <th>Personas</th>
                <th>Estado</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($reservaciones as $reservacion): ?>
                <tr>
                    <td><?= (int) $reservacion['id_reservacion'] ?></td>
                    <td><?= e($reservacion['usuario']) ?></td>
                    <td><?= e($reservacion['destino']) ?></td>
                    <td>
                        <?= e($reservacion['fecha_inicio']) ?>
                        –
                        <?= e($reservacion['fecha_fin']) ?>
                    </td>
                    <td><?= (int) $reservacion['cantidad_personas'] ?></td>
                    <td>
                        <?php if ($esAdministrador): ?>
                            <form method="POST" class="formulario-en-linea">
                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?= e($_SESSION['csrf_token']) ?>">

                                <input
                                    type="hidden"
                                    name="accion"
                                    value="estado_reserva">

                                <input
                                    type="hidden"
                                    name="id_reservacion"
                                    value="<?= (int) $reservacion['id_reservacion'] ?>">

                                <select name="estado">
                                    <?php
                                    $estados = [
                                        'pendiente',
                                        'confirmada',
                                        'cancelada',
                                        'completada',
                                    ];
                                    ?>

                                    <?php foreach ($estados as $estado): ?>
                                        <option
                                            value="<?= e($estado) ?>"
                                            <?= $reservacion['estado'] === $estado
                                                ? 'selected'
                                                : '' ?>>
                                            <?= e(ucfirst($estado)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <button class="boton" type="submit">
                                    Cambiar
                                </button>
                            </form>
                        <?php else: ?>
                            <?= e(ucfirst($reservacion['estado'])) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        ₡<?= number_format((float) $reservacion['total_reservacion'], 2) ?>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if ($reservaciones === []): ?>
                <tr>
                    <td colspan="7">No hay reservaciones registradas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
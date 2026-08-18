<?php
$titulosReportes = [
    'destinos' => 'Reservaciones por destino',
    'hoteles' => 'Hoteles más reservados',
    'actividades' => 'Actividades más solicitadas',
    'usuarios' => 'Usuarios registrados por fecha',
    'fechas' => 'Reservaciones por fecha',
    'ingresos' => 'Ingresos por fecha',
];
?>

<?php foreach ($reportes as $nombre => $filas): ?>
    <section class="panel-reporte">
        <h2><?= e($titulosReportes[$nombre] ?? ucfirst($nombre)) ?></h2>

        <?php
        $valores = array_column($filas, 'total');
        $maximo = $valores !== [] ? max($valores) : 1;
        ?>

        <?php if ($filas === []): ?>
            <p>No hay datos disponibles para este reporte.</p>
        <?php else: ?>
            <div class="barras-reporte">
                <?php foreach ($filas as $fila): ?>
                    <?php
                    $porcentaje = $maximo > 0
                        ? ((float) $fila['total'] / (float) $maximo) * 100
                        : 0;
                    $esIngreso = $nombre === 'ingresos';
                    ?>

                    <div>
                        <span><?= e($fila['etiqueta']) ?></span>
                        <i style="width: <?= $porcentaje ?>%"></i>
                        <strong>
                            <?= $esIngreso ? '₡' : '' ?><?= number_format((float) $fila['total'], $esIngreso ? 2 : 0) ?>
                        </strong>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
<?php endforeach; ?>
<form class="buscador-modulo" method="GET">
    <input type="hidden" name="modulo" value="usuarios">
    <input
        type="search"
        name="buscar"
        value="<?= e($buscar) ?>"
        placeholder="Buscar por nombre o correo">
    <button class="boton boton-principal" type="submit">Buscar</button>
</form>

<div class="tabla-responsive">
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($usuarios as $filaUsuario): ?>
                <?php $idFormulario = 'usuario-' . (int) $filaUsuario['id_usuario']; ?>

                <tr>
                    <td><?= e($filaUsuario['nombre']) ?></td>
                    <td><?= e($filaUsuario['correo']) ?></td>

                    <td>
                        <select name="rol" form="<?= $idFormulario ?>">
                            <option
                                value="cliente"
                                <?= $filaUsuario['rol'] === 'cliente'
                                    ? 'selected'
                                    : '' ?>>
                                Cliente
                            </option>

                            <option
                                value="administrador"
                                <?= $filaUsuario['rol'] === 'administrador'
                                    ? 'selected'
                                    : '' ?>>
                                Administrador
                            </option>
                        </select>
                    </td>

                    <td>
                        <select name="estado" form="<?= $idFormulario ?>">
                            <option
                                value="activo"
                                <?= $filaUsuario['estado'] === 'activo'
                                    ? 'selected'
                                    : '' ?>>
                                Activo
                            </option>

                            <option
                                value="inactivo"
                                <?= $filaUsuario['estado'] === 'inactivo'
                                    ? 'selected'
                                    : '' ?>>
                                Inactivo
                            </option>
                        </select>
                    </td>

                    <td>
                        <form id="<?= $idFormulario ?>" method="POST">
                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= e($_SESSION['csrf_token']) ?>">

                            <input type="hidden" name="accion" value="usuario">

                            <input
                                type="hidden"
                                name="id_usuario"
                                value="<?= (int) $filaUsuario['id_usuario'] ?>">

                            <button class="boton" type="submit">
                                Guardar
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
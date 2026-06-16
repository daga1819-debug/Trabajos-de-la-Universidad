# Sistema de Gestión de Biblioteca Digital

Nombre: Daryl Gabriel Jiménez Avalos

## Cómo ejecutar el sistema

- Si se utiliza XAMPP

1. Copiar la carpeta del proyecto dentro de `htdocs`.
2. Iniciar Apache.
3. Abrir el navegador y entrar a `http://localhost/EXAMEN_JIMENEZ_DARYL/index.php`.

- Con PHP nativo

1. Abrir una terminal dentro de la carpeta principal del proyecto.
2. Ejecutar el siguiente comando: `php -S localhost:80`.
3. Abrir el navegador web e ingresar la siguiente dirección: `http://localhost:80/index.php.`

## Características implementadas

- Dashboard principal con tabla de libros.
- Registro de libros con método POST.
- Validaciones de ISBN, título, autor, género, año, páginas y cantidad.
- Sanitización de datos con `htmlspecialchars()`.
- Almacenamiento de libros en `$_SESSION` usando arreglo multidimensional asociativo.
- Tres libros de ejemplo.
- Búsqueda por título con método GET.
- Filtro por género.
- Filtro por disponibilidad con radio buttons.
- Estadísticas del sistema.
- Cambio de disponibilidad con operador ternario.
- Uso de funciones personalizadas.

## Archivos principales

- `index.php`: página principal y tabla de libros.
- `registrar.php`: formulario y procesamiento de registro.
- `buscar.php`: formulario de búsqueda y resultados.
- `estadisticas.php`: cálculos estadísticos.
- `cambiar_disponibilidad.php`: cambia el estado de disponibilidad.
- `salir.php`: reinicia la sesión.
- `includes/funciones.php`: funciones reutilizables.
- `css/estilos.css`: diseño del sistema.

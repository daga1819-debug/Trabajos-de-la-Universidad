# Viajar es Pura Vida

Proyecto académico de Programación IV desarrollado con PHP, MySQL, PDO, HTML, CSS y JavaScript. El sistema permite consultar destinos turísticos de Costa Rica, administrar hoteles y actividades, registrar usuarios, crear reservaciones, guardar favoritos, publicar comentarios y generar reportes.

## Requisitos

- PHP 8.1 o superior.
- MySQL o MariaDB compatible con las restricciones utilizadas en el script SQL.
- Extensiones PHP `pdo_mysql` y `fileinfo` habilitadas.
- Apache/XAMPP para el entorno local recomendado.
- Conexión a Internet únicamente para el clima de Open-Meteo y los mapas de Leaflet/OpenStreetMap.

## Instalación

1. Copiar la carpeta `Viajar_es_Pura_Vida` dentro de `C:\xampp\htdocs`.
2. Iniciar Apache y MySQL desde XAMPP.
3. Abrir phpMyAdmin e importar `database/viajar_es_pura_vida.sql`.
4. Verificar que `config/database.php` coincida con la configuración local de MySQL. Por defecto se utiliza `127.0.0.1`, usuario `root`, contraseña vacía y el puerto predeterminado de MySQL.
5. Abrir `http://localhost/Viajar_es_Pura_Vida/public/`.


## Credenciales de prueba

### Administrador

- Correo: `admin@viajarespuravida.cr`
- Contraseña: `Admin123`

### Cliente

- Correo: `cliente@viajarespuravida.cr`
- Contraseña: `Cliente123`

Las contraseñas no se guardan en texto plano. El SQL contiene únicamente hashes compatibles con `password_verify()`.

## Funcionalidades principales

- Inicio y cierre de sesión con roles de administrador y cliente.
- Registro de clientes con validaciones del lado del servidor.
- Recuperación simulada de contraseña mediante token temporal.
- CRUD y búsqueda de destinos, hoteles y actividades.
- Carga de imágenes desde los formularios administrativos.
- Reservaciones de hotel con actividad opcional, cálculo de noches, habitaciones, personas y totales.
- Historial de reservaciones y cambio de estado por parte del administrador.
- Perfil con actualización de datos, fotografía y contraseña.
- Favoritos, comentarios y calificaciones.
- Administración del estado y rol de los usuarios.
- Seis reportes: reservaciones por destino, hoteles más reservados, actividades más solicitadas, usuarios registrados, reservaciones por fecha e ingresos.
- Bitácora de operaciones administrativas y reservaciones.
- Clima actual mediante Open-Meteo.
- Mapas mediante Leaflet y OpenStreetMap.
- Buscador del catálogo principal.

## Seguridad aplicada

- PDO con consultas preparadas reales.
- `password_hash()` y `password_verify()` para contraseñas.
- Tokens CSRF en formularios sensibles.
- Regeneración del identificador de sesión al iniciar sesión.
- Cookie de sesión `HttpOnly` y `SameSite=Lax`.
- Validación de tipo MIME y tamaño de imágenes.
- Escape de contenido HTML mediante la función auxiliar `e()`.
- Validaciones de permisos para acciones administrativas.
- Mensajes técnicos registrados en `storage/logs` sin mostrar detalles internos al usuario.

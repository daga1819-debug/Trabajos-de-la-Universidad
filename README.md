# Viajar es Pura Vida

Primer avance del proyecto programado del curso **Programación IV**.

## Tecnologías utilizadas

- HTML5
- PHP
- CSS3
- JavaScript básico
- Sesiones de PHP
- Estructura inicial inspirada en MVC

Aún no se implemento MySQL

## Funcionalidades incluidas

- Formulario de inicio de sesión.
- Envío de credenciales mediante `POST`.
- Validación de datos en PHP.
- Sanitización del correo electrónico.
- Token CSRF para proteger el formulario.
- Autenticación temporal con usuarios simulados.
- Creación, protección y cierre de sesión.
- Página principal con:
  - Carrusel de destinos.
  - Buscador visual.
  - Destinos destacados.
  - Hoteles recomendados.
  - Actividades turísticas.
  - Boceto de reservaciones.
  - Vista de módulos futuros.
- Formulario visual de registro.
- Formulario visual de recuperación de contraseña.
- Diseño adaptable a computadora, tableta y teléfono.

## Credenciales de prueba

### Administrador

- Correo: `admin@viajarespuravida.cr`
- Contraseña: `Admin123`

### Cliente

- Correo: `cliente@viajarespuravida.cr`
- Contraseña: `Cliente123`

## Cómo ejecutar el proyecto con XAMPP

1. Descomprima la carpeta `viajar-es-pura-vida`.
2. Copie la carpeta completa dentro de `C:\xampp\htdocs\`.
3. Inicie Apache desde el panel de XAMPP.
4. Abra el navegador.
5. Ingrese a:

   `http://localhost/viajar-es-pura-vida/public/`


## Mejoras previstas para siguientes entregas

- Conexión MySQL mediante PDO.
- Contraseñas con `password_hash()` y `password_verify()`.
- CRUD de usuarios, destinos, hoteles, actividades y reservaciones.
- Roles y autorización de administrador/cliente.
- Integración con API del clima.
- Integración con API de tipo de cambio o mapas.
- Reportes y estadísticas.
- Favoritos, calificaciones y comentarios.
- Validaciones completas en cliente y servidor.
- Manejo de archivos e imágenes.

# Viajar es Pura Vida

Primer avance del proyecto programado del curso **Programación IV**.

## Tecnologías utilizadas

- HTML5
- PHP
- CSS3
- JavaScript básico
- Sesiones de PHP
- Estructura inicial inspirada en MVC

En esta etapa el proyecto aun no contaba con mysql y la autenticación utilizaba usuarios simulados.

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

## ---------------------------------------------------------------------------------------------------------------------------------------- ##

Segundo avance del proyecto programado del curso **Programación IV**.

## Estado actual del proyecto

En este avance, la conexión con MySQL se utiliza para el módulo de autenticación de usuarios. La página principal conserva temporalmente datos estáticos con fines de prototipo visual.

La base de datos ya contiene los registros iniciales de destinos, hoteles y actividades que serán consultados dinámicamente en las próximas etapas, cuando se implementen los modelos, controladores y operaciones CRUD correspondientes.

## Funcionalidades implementadas

- Formulario de inicio de sesión.
- Recepción de credenciales mediante POST.
- Conexión a MySQL utilizando PDO.
- Consultas preparadas para prevenir inyección SQL.
- Contraseñas almacenadas mediante password_hash().
- Verificación de contraseñas mediante password_verify().
- Validaciones del lado del servidor.
- Protección CSRF.
- Manejo y protección de sesiones.
- Cierre de sesión.
- Página principal con el boceto visual del sistema completo.
- Modelo inicial de base de datos para usuarios, destinos, hoteles, actividades y reservaciones.

## Funcionalidades pendientes

- Registro funcional de nuevos usuarios.
- Recuperación y cambio de contraseña.
- CRUD de destinos.
- CRUD de hoteles.
- CRUD de actividades.
- Gestión de reservaciones.
- Reportes y estadísticas.
- Consumo de APIs.
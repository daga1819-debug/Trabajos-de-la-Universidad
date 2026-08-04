# El Archivo Fantasma

Proyecto desarrollado con HTML, CSS, JavaScript, PHP, MySQL y una API REST externa.

## Instalación en Windows con XAMPP

1. Copiar la carpeta `Archivo_Fantasma` dentro de `C:\xampp\htdocs\`.
2. Iniciar Apache y MySQL desde el panel de XAMPP.
3. Abrir phpMyAdmin en `http://127.0.0.1/phpmyadmin/`.
4. Importar el archivo `database/archivo_fantasma.sql`.
5. Abrir `http://localhost/Archivo_Fantasma/public/`.

## Instalación en macOS

1. Colocar la carpeta dentro del directorio público del servidor local.
2. Iniciar Apache y MySQL con MAMP, XAMPP o la instalación utilizada.
3. Importar `database/archivo_fantasma.sql`.
4. Revisar en `config/database.php` el usuario, la contraseña y el puerto de MySQL.

## Funcionalidades

- Panel administrativo responsivo.
- Escaneo simulado de archivos mediante PHP.
- Registro de escaneos y archivos en MySQL.
- Actualización dinámica de la tabla con JavaScript y `fetch`.
- Marcado y desmarcado de archivos peligrosos.
- Filtro para mostrar solamente archivos peligrosos.
- Consulta de reportes externos mediante JSONPlaceholder.
- Auto-detección cada 60 segundos.

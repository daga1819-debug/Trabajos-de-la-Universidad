<?php

/*
    Valida y almacena las imágenes cargadas desde los formularios administrativos
 */
class ImagenService
{
    private const TAMANO_MAXIMO = 2 * 1024 * 1024;

    private const EXTENSIONES_PERMITIDAS = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    /*
        Guarda una imagen en una subcarpeta administrada por la aplicación
        Devuelve null cuando el formulario no envió un archivo nuevo
     */
    public static function guardar(
        array $archivo,
        string $carpeta,
        string $prefijo
    ): ?string {
        $error = (int) ($archivo['error'] ?? UPLOAD_ERR_NO_FILE);

        if ($error === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($error !== UPLOAD_ERR_OK) {
            throw new RuntimeException('No fue posible recibir la imagen.');
        }

        if ((int) ($archivo['size'] ?? 0) > self::TAMANO_MAXIMO) {
            throw new RuntimeException('La imagen no puede superar 2 MB.');
        }

        $archivoTemporal = (string) ($archivo['tmp_name'] ?? '');

        if ($archivoTemporal === '' || !is_uploaded_file($archivoTemporal)) {
            throw new RuntimeException('El archivo recibido no es una carga válida.');
        }

        $tipo = (new finfo(FILEINFO_MIME_TYPE))->file($archivoTemporal);

        if (!isset(self::EXTENSIONES_PERMITIDAS[$tipo])) {
            throw new RuntimeException('La imagen debe ser JPG, PNG o WEBP.');
        }

        $extension = self::EXTENSIONES_PERMITIDAS[$tipo];
        $nombre = $prefijo . '_' . bin2hex(random_bytes(8)) . '.' . $extension;

        // La carpeta se crea automáticamente
        $rutaPublica = 'assets/img/' . trim($carpeta, '/') . '/' . $nombre;
        $rutaDestino = __DIR__ . '/../../public/' . $rutaPublica;
        $directorio = dirname($rutaDestino);

        if (!is_dir($directorio) && !mkdir($directorio, 0775, true)) {
            throw new RuntimeException('No fue posible crear la carpeta de imágenes.');
        }

        if (!move_uploaded_file($archivoTemporal, $rutaDestino)) {
            throw new RuntimeException('No fue posible guardar la imagen.');
        }

        return $rutaPublica;
    }
}

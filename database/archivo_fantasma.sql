-- Base de datos
CREATE DATABASE IF NOT EXISTS archivo_fantasma
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

-- Se selecciona la base de datos
USE archivo_fantasma;

-- Guarda la información general de cada escaneo realizado
CREATE TABLE IF NOT EXISTS escaneos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    cantidad_archivos INT NOT NULL,
    usuario VARCHAR(100) NOT NULL
);

-- Guarda cada archivo falso encontrado durante un escaneo
CREATE TABLE IF NOT EXISTS archivos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    tamano INT NOT NULL,
    fecha_detectado DATETIME NOT NULL,
    peligroso TINYINT(1) NOT NULL DEFAULT 0,
    escaneo_id INT NOT NULL,

    -- Relaciona cada archivo con el escaneo que lo detectó
    CONSTRAINT fk_archivos_escaneo
        FOREIGN KEY (escaneo_id)
        REFERENCES escaneos(id)
        ON DELETE CASCADE
);

-- Facilita las búsquedas por estado y por escaneo
CREATE INDEX idx_archivos_peligroso ON archivos (peligroso);
CREATE INDEX idx_archivos_escaneo ON archivos (escaneo_id);

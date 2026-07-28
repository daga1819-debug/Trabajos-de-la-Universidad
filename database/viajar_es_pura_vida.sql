/*
    Creamos la base de datos
  */
CREATE DATABASE viajar_es_pura_vida
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

-- Seleccionamos la base de datos
USE viajar_es_pura_vida;

-- Tabla de usuarios

CREATE TABLE usuarios (
    id_usuario INT UNSIGNED AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL,
    telefono VARCHAR(20) NULL,
    fotografia VARCHAR(255) NULL,

-- La contraseña se almacena de forma segura mediante hashing
    contrasena VARCHAR(255) NOT NULL,

-- Persmisos de los usuario
    rol ENUM('administrador', 'cliente') NOT NULL DEFAULT 'cliente',
-- Desactivar usuario sin elimiar usuario
    estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
-- Token para recuperación de contraseña
    token_recuperacion VARCHAR(255) NULL,
    token_expiracion DATETIME NULL,

    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id_usuario),
    UNIQUE KEY uk_usuarios_correo (correo)
)ENGINE=InnoDB;

-- Tabla de destinos

CREATE TABLE destinos (
    id_destino INT UNSIGNED AUTO_INCREMENT,
    nombre VARCHAR(120) NOT NULL,
    provincia ENUM(
        'San José',
        'Alajuela',
        'Cartago',
        'Heredia',
        'Guanacaste',
        'Puntarenas',
        'Limón'
    ) NOT NULL,

    descripcion TEXT NOT NULL,
    imagen_principal VARCHAR(255) NULL,
    latitud DECIMAL(10, 8) NULL,
    longitud DECIMAL(11, 8) NULL,
    ubicacion VARCHAR(255) NULL,

    estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',

    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id_destino),
    UNIQUE KEY uk_destinos_nombre (nombre),
    INDEX idx_destinos_provincia (provincia),
    INDEX idx_destinos_estado (estado)
)ENGINE=InnoDB;

-- Tabla de hoteles

CREATE TABLE hoteles (
    id_hotel INT UNSIGNED AUTO_INCREMENT,
    id_destino INT UNSIGNED NOT NULL,

    nombre  VARCHAR(150) NOT NULL,
    categoria TINYINT UNSIGNED NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    telefono VARCHAR(20) NULL,
    correo VARCHAR(150) NULL,

    precio_noche DECIMAL(10, 2) NOT NULL,
    cantidad_habitaciones INT UNSIGNED NOT NULL,
    descripcion TEXT NULL,
    imagen VARCHAR(255) NULL,

    estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',

    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id_hotel),

    CONSTRAINT fk_hoteles_destinos
        FOREIGN KEY (id_destino)
        REFERENCES destinos(id_destino)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT chk_hoteles_categoria
        CHECK (categoria BETWEEN 1 AND 5),

    CONSTRAINT chk_hoteles_precio
        CHECK (precio_noche >= 0),

    CONSTRAINT chk_hoteles_habitaciones
        CHECK (cantidad_habitaciones >= 0),
    
    INDEX idx_hoteles_destino (id_destino),
    INDEX idx_hoteles_estado (estado),
    INDEX idx_hoteles_nombre (nombre)

)ENGINE=InnoDB;

-- Tabla de actividades

CREATE TABLE actividades (
    id_actividad INT UNSIGNED AUTO_INCREMENT,
    id_destino INT UNSIGNED NOT NULL,

    nombre VARCHAR(150) NOT NULL,
    tipo ENUM(
        'Canopy',
        'Rafting',
        'Senderismo',
        'Buceo',
        'Tour',
        'Cabalgata',
        'Otro'
    ) NOT NULL,

    descripcion TEXT NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,

    -- La duración se guardará en minutos
    duracion_minutos INT UNSIGNED NOT NULL,

    cupo_maximo INT UNSIGNED NOT NULL,
    imagen VARCHAR(255) NULL,

    estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',

    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id_actividad),

    CONSTRAINT fk_actividades_destinos
        FOREIGN KEY (id_destino)
        REFERENCES destinos(id_destino)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    
    CONSTRAINT chk_actividades_precio
        CHECK (precio >= 0),

    CONSTRAINT chk_actividades_duracion
        CHECK (duracion_minutos > 0),

    CONSTRAINT chk_actividades_cupos
        CHECK (cupo_maximo > 0),

    INDEX idx_actividades_destino (id_destino),
    INDEX idx_actividades_tipo (tipo),
    INDEX idx_actividades_estado (estado)
)ENGINE=InnoDB;

-- Tabla de reservaciones

CREATE TABLE reservaciones (
    id_reservacion INT UNSIGNED AUTO_INCREMENT,
    id_usuario INT UNSIGNED NOT NULL,
    id_destino INT UNSIGNED NOT NULL,

    fecha_reservacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    cantidad_personas INT UNSIGNED NOT NULL,

    estado ENUM(
        'pendiente',
        'confirmada',
        'cancelada',
        'completada'
    ) NOT NULL DEFAULT 'pendiente',

    total_hoteles DECIMAL(12,2) NOT NULL DEFAULT 0,
    total_actividades DECIMAL(12,2) NOT NULL DEFAULT 0,
    total_reservacion DECIMAL(12,2) NOT NULL DEFAULT 0,

    observaciones TEXT NULL,

    fecha_actualizacion DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id_reservacion),

    CONSTRAINT fk_reservaciones_usuarios
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_reservaciones_destinos
        FOREIGN KEY (id_destino)
        REFERENCES destinos(id_destino)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT chk_reservaciones_personas
        CHECK (cantidad_personas > 0),

    CONSTRAINT chk_reservaciones_fechas
        CHECK (fecha_fin >= fecha_inicio),

    CONSTRAINT chk_reservaciones_totales
        CHECK (
            total_hoteles >= 0
            AND total_actividades >= 0
            AND total_reservacion >= 0
        ),

    INDEX idx_reservaciones_usuario (id_usuario),
    INDEX idx_reservaciones_destino (id_destino),
    INDEX idx_reservaciones_fecha (fecha_reservacion),
    INDEX idx_reservaciones_estado (estado)
) ENGINE=InnoDB;


-- Tabla de reservación de hoteles

CREATE TABLE reservacion_hoteles (
    id_reservacion_hotel INT UNSIGNED AUTO_INCREMENT,
    id_reservacion INT UNSIGNED NOT NULL,
    id_hotel INT UNSIGNED NOT NULL,

    cantidad_habitaciones INT UNSIGNED NOT NULL DEFAULT 1,
    cantidad_noches INT UNSIGNED NOT NULL,
    precio_noche DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(12, 2) NOT NULL,

    PRIMARY KEY (id_reservacion_hotel),

    CONSTRAINT fk_reservacion_hoteles_reservacion
        FOREIGN KEY (id_reservacion)
        REFERENCES reservaciones (id_reservacion)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_reservacion_hoteles_hotel
        FOREIGN KEY (id_hotel)
        REFERENCES hoteles (id_hotel)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT chk_reservacion_hoteles_cantidad
        CHECK (cantidad_habitaciones > 0),

    CONSTRAINT chk_reservacion_hoteles_noches
        CHECK (cantidad_noches > 0),

    CONSTRAINT chk_reservacion_hoteles_precio
        CHECK (precio_noche >= 0 AND subtotal >= 0),

    UNIQUE KEY uk_reservacion_hotel (
        id_reservacion,
        id_hotel
    )
) ENGINE = InnoDB;


-- Tabla de reservación de actividades


CREATE TABLE reservacion_actividades (
    id_reservacion_actividad INT UNSIGNED AUTO_INCREMENT,
    id_reservacion INT UNSIGNED NOT NULL,
    id_actividad INT UNSIGNED NOT NULL,

    fecha_actividad DATE NOT NULL,
    cantidad_personas INT UNSIGNED NOT NULL,
    precio_persona DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(12, 2) NOT NULL,

    PRIMARY KEY (id_reservacion_actividad),

    CONSTRAINT fk_reserva_actividades_reservacion
        FOREIGN KEY (id_reservacion)
        REFERENCES reservaciones (id_reservacion)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_reserva_actividades_actividad
        FOREIGN KEY (id_actividad)
        REFERENCES actividades (id_actividad)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT chk_reserva_actividades_personas
        CHECK (cantidad_personas > 0),

    CONSTRAINT chk_reserva_actividades_precio
        CHECK (precio_persona >= 0 AND subtotal >= 0),

    UNIQUE KEY uk_reservacion_actividad (
        id_reservacion,
        id_actividad,
        fecha_actividad
    )
) ENGINE = InnoDB;


-- Tabla de favoritos

CREATE TABLE favoritos (
    id_favorito INT UNSIGNED AUTO_INCREMENT,
    id_usuario INT UNSIGNED NOT NULL,
    id_destino INT UNSIGNED NOT NULL,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id_favorito),

    CONSTRAINT fk_favoritos_usuarios
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios (id_usuario)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_favoritos_destinos
        FOREIGN KEY (id_destino)
        REFERENCES destinos (id_destino)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    UNIQUE KEY uk_favorito_usuario_destino (
        id_usuario,
        id_destino
    )
) ENGINE = InnoDB;

-- Tabla de comentarios

CREATE TABLE comentarios (
    id_comentario INT UNSIGNED AUTO_INCREMENT,
    id_usuario INT UNSIGNED NOT NULL,
    id_destino INT UNSIGNED NOT NULL,

    comentario TEXT NOT NULL,
    calificacion TINYINT UNSIGNED NOT NULL,

    estado ENUM(
        'pendiente',
        'aprobado',
        'rechazado'
    ) NOT NULL DEFAULT 'pendiente',

    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id_comentario),

    CONSTRAINT fk_comentarios_usuarios
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios (id_usuario)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_comentarios_destinos
        FOREIGN KEY (id_destino)
        REFERENCES destinos (id_destino)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT chk_comentarios_calificacion
        CHECK (calificacion BETWEEN 1 AND 5),

    INDEX idx_comentarios_destino (id_destino),
    INDEX idx_comentarios_estado (estado)
) ENGINE = InnoDB;

-- Tabla de bitácora

CREATE TABLE bitacora (
    id_bitacora BIGINT UNSIGNED AUTO_INCREMENT,
    id_usuario INT UNSIGNED NULL,

    accion VARCHAR(100) NOT NULL,
    modulo VARCHAR(100) NOT NULL,
    descripcion TEXT NULL,
    direccion_ip VARCHAR(45) NULL,

    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id_bitacora),

    CONSTRAINT fk_bitacora_usuarios
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios (id_usuario)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    INDEX idx_bitacora_usuario (id_usuario),
    INDEX idx_bitacora_fecha (fecha_registro),
    INDEX idx_bitacora_modulo (modulo)
) ENGINE = InnoDB;

-- Datos iniciales de destinos

INSERT INTO destinos (
    nombre,
    provincia,
    descripcion,
    imagen_principal,
    latitud,
    longitud,
    ubicacion,
    estado
) VALUES
(
    'La Fortuna y Volcán Arenal',
    'Alajuela',
    'Destino reconocido por el Volcán Arenal, sus aguas termales, senderos y actividades de aventura.',
    'arenal.jpg',
    10.46780000,
    -84.64270000,
    'La Fortuna, San Carlos, Alajuela',
    'activo'
),
(
    'Monteverde',
    'Puntarenas',
    'Zona de bosque nuboso reconocida por su biodiversidad, senderos naturales y recorridos de canopy.',
    'monteverde.jpg',
    10.30000000,
    -84.81670000,
    'Monteverde, Puntarenas',
    'activo'
),
(
    'Manuel Antonio',
    'Puntarenas',
    'Destino de playas, bosque tropical y vida silvestre ubicado en el Pacífico Central.',
    'manuel-antonio.jpg',
    9.39230000,
    -84.13670000,
    'Quepos, Puntarenas',
    'activo'
),
(
    'Río Celeste',
    'Alajuela',
    'Atracción natural conocida por el color azul de sus aguas dentro del Parque Nacional Volcán Tenorio.',
    'rio-celeste.jpg',
    10.70100000,
    -85.01300000,
    'Guatuso, Alajuela',
    'activo'
),
(
    'Puerto Viejo',
    'Limón',
    'Destino caribeño reconocido por sus playas, cultura, gastronomía y riqueza natural.',
    'puerto-viejo.jpg',
    9.65400000,
    -82.75400000,
    'Talamanca, Limón',
    'activo'
);
-- Datos iniciales de hoteles

INSERT INTO hoteles (
    id_destino,
    nombre,
    categoria,
    direccion,
    telefono,
    correo,
    precio_noche,
    cantidad_habitaciones,
    descripcion,
    imagen,
    estado
) VALUES
(
    1,
    'Arenal Vista Lodge',
    4,
    'La Fortuna, San Carlos',
    '2479-0001',
    'reservas@arenalvistalodge.cr',
    55000.00,
    25,
    'Hotel con vista al Volcán Arenal, piscina y desayuno incluido.',
    'hotel-arenal.jpg',
    'activo'
),
(
    2,
    'Bosque Nuboso Hotel',
    4,
    'Santa Elena, Monteverde',
    '2645-0002',
    'reservas@bosquenuboso.cr',
    48000.00,
    18,
    'Hospedaje rodeado de naturaleza y cercano a las principales reservas.',
    'hotel-monteverde.jpg',
    'activo'
),
(
    3,
    'Pacífico Tropical Resort',
    5,
    'Manuel Antonio, Quepos',
    '2777-0003',
    'reservas@pacificotropical.cr',
    72000.00,
    35,
    'Resort cercano a la playa y al Parque Nacional Manuel Antonio.',
    'hotel-manuel-antonio.jpg',
    'activo'
);

-- Datos iniciales de actividades

INSERT INTO actividades (
    id_destino,
    nombre,
    tipo,
    descripcion,
    precio,
    duracion_minutos,
    cupo_maximo,
    imagen,
    estado
) VALUES
(
    1,
    'Senderismo en el Volcán Arenal',
    'Senderismo',
    'Recorrido guiado por senderos naturales cercanos al Volcán Arenal.',
    18000.00,
    180,
    20,
    'senderismo-arenal.jpg',
    'activo'
),
(
    1,
    'Rafting en el río Balsa',
    'Rafting',
    'Actividad de aventura guiada por rápidos aptos para principiantes.',
    35000.00,
    240,
    15,
    'rafting-balsa.jpg',
    'activo'
),
(
    2,
    'Canopy en Monteverde',
    'Canopy',
    'Recorrido de canopy entre las copas de los árboles del bosque nuboso.',
    32000.00,
    150,
    18,
    'canopy-monteverde.jpg',
    'activo'
),
(
    3,
    'Tour por Manuel Antonio',
    'Tour',
    'Recorrido guiado para observar senderos, playas y vida silvestre.',
    25000.00,
    180,
    15,
    'tour-manuel-antonio.jpg',
    'activo'
),
(
    5,
    'Buceo en el Caribe',
    'Buceo',
    'Experiencia de buceo guiada para conocer la biodiversidad marina del Caribe.',
    45000.00,
    240,
    10,
    'buceo-caribe.jpg',
    'activo'
);
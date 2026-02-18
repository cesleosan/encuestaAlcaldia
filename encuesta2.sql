-- 1. CREACIÓN DE LA BASE DE DATOS
CREATE DATABASE IF NOT EXISTS censo_tlalpan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE censo_tlalpan;

CREATE TABLE cat_colonias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_postal VARCHAR(5) NOT NULL,
    nombre_asentamiento VARCHAR(150) NOT NULL, -- Nombre de la colonia/pueblo
    tipo_asentamiento VARCHAR(50), -- Pueblo, Barrio, Colonia
    municipio VARCHAR(100) DEFAULT 'Tlalpan',
    estado VARCHAR(100) DEFAULT 'Ciudad de México',
    INDEX idx_cp (codigo_postal) -- Índice para búsqueda rápida
);

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE, -- El "User" para login
    password VARCHAR(255) NOT NULL,      -- Hash encriptado
    nombre_completo VARCHAR(150) NOT NULL, -- "NOMBRE DEL TECNICO" (Se toma de aquí)
    rol ENUM('root', 'supervisor', 'consulta', 'encuestador') NOT NULL DEFAULT 'encuestador',
    activo TINYINT(1) DEFAULT 1,
    ultimo_acceso DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE encuestas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- CONTROL INTERNO
    folio VARCHAR(20) NOT NULL UNIQUE,
    usuario_id INT NOT NULL,
    
    -- DATOS DEL PRODUCTOR
    curp VARCHAR(18) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    apellido_paterno VARCHAR(100) NOT NULL,
    apellido_materno VARCHAR(100),
    fecha_nacimiento DATE NOT NULL,
    sexo ENUM('HOMBRE', 'MUJER') NOT NULL,
    
    -- DATOS DEMOGRÁFICOS
    tiempo_residencia_tlalpan VARCHAR(50),
    tiempo_residencia_cdmx VARCHAR(50),
    grupo_etnico VARCHAR(50) DEFAULT NULL,
    escolaridad VARCHAR(50),
    
    -- UBICACIÓN DETALLADA
    calle VARCHAR(150),
    numero_exterior VARCHAR(20),
    numero_interior VARCHAR(20) DEFAULT NULL,
    colonia_id INT NULL,
    referencia TEXT,
    
    -- GEOLOCALIZACIÓN
    latitud DECIMAL(10, 8) NOT NULL,
    longitud DECIMAL(11, 8) NOT NULL,
    precision_gps DECIMAL(5, 2),
    
    -- DATOS PRODUCTIVOS GENERALES
    actividad_principal ENUM('AGRICOLA', 'PECUARIA', 'HUERTO', 'GRANJA', 'TRANSFORMACION', 'OTRO'),
    superficie_total DECIMAL(10, 2),
    
    -- CORRECCIÓN AQUÍ 👇
    -- Usamos LONGTEXT que soporta hasta 4GB de texto, ideal para JSONs grandes.
    -- El CHECK asegura que el texto guardado sea un formato JSON válido.
    respuestas_json LONGTEXT CHECK (json_valid(respuestas_json)),
    
    -- METADATA
    estatus ENUM('Borrador', 'Finalizada') DEFAULT 'Borrador',
    fecha_inicio DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_conclusion DATETIME NULL,
    
    -- LLAVES FORÁNEAS
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (colonia_id) REFERENCES cat_colonias(id)
);

-- Usuarios (Password default: 12345)
INSERT INTO usuarios (usuario, password, nombre_completo, rol) VALUES 
('admin', '12345', 'Admin Tlalpan', 'root'),
('supervisor', '12345', 'Lic. Supervisor', 'supervisor'),
('tec01', '12345', 'Juan Pérez Técnico', 'encuestador'),
('tec02', '12345', 'Ana López Técnica', 'encuestador'),
('aGuillen, 'aGuillen','Adan Guillen', 'encuestador');
 
-- INSERT SQL de colonias/asentamientos de TLALPAN CDMX
INSERT INTO cat_colonias (codigo_postal, nombre_asentamiento, tipo_asentamiento) VALUES
('14000', 'Tlalpan', 'Colonia'),
('14000', 'Tlalpan Centro', 'Colonia'),
('14010', 'Parque del Pedregal', 'Colonia'),
('14020', 'Villa Olímpica', 'Colonia'),
('14030', 'Isidro Fabela', 'Colonia'),
('14039', 'Ampliación Isidro Fabela', 'Colonia'),
('14040', 'Cantera Puente de Piedra', 'Colonia'),
('14040', 'Pueblo Quieto', 'Colonia'),
('14049', 'Comuneros de Santa Úrsula', 'Colonia'),
('14050', 'Toriello Guerra', 'Colonia'),
('14060', 'Peña Pobre', 'Colonia'),
('14070', 'Rómulo Sánchez Mireles', 'Colonia'),
('14070', 'San Pedro Apóstol', 'Colonia'),
('14080', 'Belisario Domínguez Sección XVI', 'Colonia'),
('14080', 'Del Niño Jesús', 'Barrio'),
('14090', 'La Joya', 'Colonia'),
('14108', 'Chichicaspatl', 'Colonia'),
('14110', 'Ampliación Fuentes del Pedregal', 'Colonia'),
('14140', 'Fuentes del Pedregal', 'Colonia'),
('14200', 'Héroes de Padierna', 'Colonia'),
('14208', 'Colinas del Ajusco', 'Colonia'),
('14209', 'Torres de Padierna', 'Colonia'),
('14210', 'Jardines en la Montaña', 'Colonia'),
('14219', 'Parque Nacional Bosque del Pedregal', 'Equipamiento'),
('14220', 'Cuchilla de Padierna', 'Colonia'),
('14230', 'Cultura Maya', 'Colonia'),
('14248', 'Cruz del Farol', 'Colonia'),
('14250', 'Miguel Hidalgo 1A Sección', 'Colonia'),
('14250', 'Miguel Hidalgo 2A Sección', 'Colonia'),
('14250', 'Miguel Hidalgo 3A Sección', 'Colonia'),
('14250', 'Miguel Hidalgo 4A Sección', 'Colonia'),
('14260', 'El Capulín', 'Barrio'),
('14266', 'Zacayucan Peña Pobre', 'Colonia'),
('14267', 'De Caramagüey', 'Barrio'),
('14268', 'La Lonja', 'Barrio'),
('14269', 'La Fama', 'Barrio'),
('14270', 'Primavera', 'Colonia'),
('14270', 'Verano', 'Colonia'),
('14300', 'Nueva Oriental Coapa', 'Colonia'),
('14300', 'Residencial Acoxpa', 'Colonia'),
('14300', 'Residencial Miramontes', 'Colonia'),
('14308', 'Ex Hacienda Coapa', 'Colonia'),
('14310', 'Belisario Domínguez', 'Colonia'),
('14320', 'Vergel Coapa', 'Colonia'),
('14330', 'Granjas Coapa', 'Colonia'),
('14340', 'Vergel de Coyoacán', 'Colonia'),
('14340', 'Vergel del Sur', 'Colonia'),
('14350', 'Prado Coapa 1A Sección', 'Colonia'),
('14357', 'Prado Coapa 2A Sección', 'Colonia'),
('14357', 'Prado Coapa 3A Sección', 'Colonia'),
('14360', 'Magisterial Coapa', 'Colonia'),
('14370', 'San Lorenzo Huipulco', 'Pueblo'),
('14370', 'Residencial Chimali', 'Colonia'),
('14370', 'Villa Lázaro Cárdenas', 'Colonia'),
('14376', 'Arboledas del Sur', 'Colonia'),
('14380', 'A.M.S.A', 'Colonia'),
('14386', 'Rancho los Colorines', 'Colonia'),
('14387', 'Ex Hacienda San Juan de Dios', 'Colonia'),
('14388', 'Guadalupe', 'Colonia'),
('14389', 'Arenal de Guadalupe', 'Colonia'),
('14390', 'Villa Coapa', 'Colonia'),
('14406', 'Divisadero', 'Colonia'),
('14408', 'Nuevo Renacimiento de Axalco', 'Colonia'),
('14409', 'Tecorral', 'Colonia'),
('14410', 'Bosques de Tepeximilpa', 'Colonia'),
('14410', 'Fuentes Brotantes', 'Colonia'),
('14420', 'Cumbres de Tepetongo', 'Colonia'),
('14420', 'Santa Úrsula Xitla', 'Colonia'),
('14426', 'Tlaxcaltenco la Mesa', 'Colonia'),
('14427', 'San Juan Tepeximilpa', 'Colonia'),
('14429', 'Santísima Trinidad', 'Colonia'),
('14430', 'El Truenito', 'Barrio'),
('14438', 'Pedregal de Santa Úrsula Xitla', 'Colonia'),
('14439', 'Pedregal de las Águilas', 'Colonia'),
('14449', 'El Mirador 1A Sección', 'Colonia'),
('14449', 'El Mirador 2A Sección', 'Colonia'),
('14449', 'El Mirador 3A Sección', 'Colonia'),
('14456', 'Actopa', 'Colonia'),
('14456', 'Actopa Sur', 'Colonia'),
('14460', 'Tlalpuente', 'Colonia'),
('14600', 'Valle Escondido', 'Colonia'),
('14608', 'Colinas del Bosque', 'Colonia'),
('14610', 'Arenal Tepepan', 'Colonia'),
('14620', 'Club de Golf México', 'Colonia'),
('14629', 'San Buenaventura', 'Colonia'),
('14630', 'Chimalcoyoc', 'Pueblo'),
('14630', 'Villa Tlalpan', 'Colonia'),
('14640', 'Ejidos de San Pedro Mártir', 'Colonia'),
('14643', 'Fuentes de Tepepan', 'Colonia'),
('14646', 'Valle de Tepepan', 'Colonia'),
('14647', 'Juventud Unida', 'Colonia'),
('14647', 'Rinconada El Mirador', 'Colonia'),
('14653', 'Heróico Colegio Militar', 'Colonia'),
('14654', 'Dolores Tlalli', 'Colonia'),
('14655', 'Valle Verde', 'Colonia'),
('14657', 'Tlalmille', 'Colonia'),
('14658', 'Mirador del Valle', 'Colonia'),
('14700', 'San Miguel Ajusco', 'Pueblo'),
('14710', 'Santo Tomas Ajusco', 'Pueblo'),
('14720', 'Belvedere Ajusco', 'Colonia'),
('14734', 'El Zacatón', 'Colonia'),
('14737', 'Vistas del Pedregal', 'Colonia'),
('14738', 'Bosques del Pedregal', 'Colonia'),
('14748', 'Mirador I', 'Colonia'),
('14748', 'Mirador II', 'Colonia'),
('14749', 'Chimilli', 'Colonia'),
('14760', 'Héroes de 1910', 'Colonia'),
('14900', 'Parres El Guarda', 'Pueblo');

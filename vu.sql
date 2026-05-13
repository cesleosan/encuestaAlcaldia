-- 1. Asegurar el uso de la base de datos correcta
USE censo_tlalpan;

-- 2. Tabla Principal de Solicitudes (Con el ENUM corregido)
CREATE TABLE IF NOT EXISTS solicitudes (
    id_solicitud INT AUTO_INCREMENT PRIMARY KEY,
    folio VARCHAR(25) UNIQUE NOT NULL,
    materia VARCHAR(100) NOT NULL,
    nombre_tramite VARCHAR(255) NOT NULL,
    tipo_persona ENUM('fisica', 'moral') NOT NULL,
    -- Agregamos 'representante' para evitar el error de truncado
    tipo_representante ENUM('propietario', 'legal', 'autorizada', 'representante') NOT NULL,
    fecha_ingreso TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estatus ENUM('captura', 'finalizado', 'cancelado') DEFAULT 'captura'
) ENGINE=InnoDB;

-- 3. Tabla de Datos del Interesado (Física y Moral)
CREATE TABLE IF NOT EXISTS datos_interesado (
    id_interesado INT AUTO_INCREMENT PRIMARY KEY,
    id_solicitud INT NOT NULL,
    nombres_o_razon_social VARCHAR(255),
    apellido_paterno VARCHAR(100),
    apellido_materno VARCHAR(100),
    rfc VARCHAR(15),
    telefono VARCHAR(20),
    email VARCHAR(150),
    alcaldia VARCHAR(100),
    colonia VARCHAR(150),
    calle VARCHAR(255),
    num_exterior VARCHAR(20),
    cp VARCHAR(10),
    -- Datos exclusivos de Moral
    no_escritura VARCHAR(50),
    no_notario VARCHAR(50),
    nombre_notario VARCHAR(255),
    FOREIGN KEY (id_solicitud) REFERENCES solicitudes(id_solicitud) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. Tabla de Datos de Representantes (Legal o Autorizada)
CREATE TABLE IF NOT EXISTS datos_representantes (
    id_rep INT AUTO_INCREMENT PRIMARY KEY,
    id_solicitud INT NOT NULL,
    tipo_rep ENUM('legal', 'autorizada'),
    nombres VARCHAR(150),
    apellido_paterno VARCHAR(100),
    apellido_materno VARCHAR(100),
    rfc VARCHAR(15),
    telefono VARCHAR(20),
    email VARCHAR(150),
    doc_acredita_personalidad TEXT,
    alcaldia VARCHAR(100),
    colonia VARCHAR(150),
    calle VARCHAR(255),
    num_exterior VARCHAR(20),
    cp VARCHAR(10),
    FOREIGN KEY (id_solicitud) REFERENCES solicitudes(id_solicitud) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. Tabla de Requisitos Validados (ESTA ES LA QUE FALTABA)
CREATE TABLE IF NOT EXISTS requisitos_presentados (
    id_requisito_solicitud INT AUTO_INCREMENT PRIMARY KEY,
    id_solicitud INT NOT NULL,
    nombre_documento VARCHAR(255) NOT NULL,
    validado BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_solicitud) REFERENCES solicitudes(id_solicitud) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. Tabla para Datos Dinámicos (EAV: Predio, Mercado, Recibos)[cite: 3]
CREATE TABLE IF NOT EXISTS detalles_tramite_especifico (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_solicitud INT NOT NULL,
    campo_nombre VARCHAR(100) NOT NULL,
    campo_valor TEXT,
    FOREIGN KEY (id_solicitud) REFERENCES solicitudes(id_solicitud) ON DELETE CASCADE
) ENGINE=INNODB;

SELECT 
    s.folio,
    s.nombre_tramite,
    s.tipo_persona,
    i.nombres_o_razon_social AS interesado_nombre,
    i.rfc AS interesado_rfc,
    r.nombre_documento AS requisito_entregado,
    d.campo_nombre AS dato_especifico,
    d.campo_valor AS valor_especifico
FROM solicitudes s
LEFT JOIN datos_interesado i ON s.id_solicitud = i.id_solicitud
LEFT JOIN requisitos_presentados r ON s.id_solicitud = r.id_solicitud
LEFT JOIN detalles_tramite_especifico d ON s.id_solicitud = d.id_solicitud
ORDER BY s.id_solicitud DESC;

SELECT 
    s.folio, 
    s.nombre_tramite, 
    d.campo_nombre, 
    d.campo_valor 
FROM detalles_tramite_especifico d
JOIN solicitudes s ON d.id_solicitud = s.id_solicitud
WHERE s.id_solicitud = (SELECT MAX(id_solicitud) FROM solicitudes);
SELECT 
    s.folio,
    rep.tipo_rep,
    rep.nombres,
    rep.doc_acredita_personalidad,
    rep.calle,
    rep.colonia,
    rep.cp
FROM datos_representantes rep
JOIN solicitudes s ON rep.id_solicitud = s.id_solicitud;
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
    rep.apellido_paterno,
    rep.doc_acredita_personalidad,
    rep.calle,
    rep.colonia,
    rep.cp
FROM datos_representantes rep
JOIN solicitudes s ON rep.id_solicitud = s.id_solicitud
ORDER BY s.id_solicitud DESC;

ALTER TABLE solicitudes ADD COLUMN payload LONGTEXT AFTER tipo_representante;

USE censo_tlalpan;

-- 1. Asegurar payload en solicitudes
ALTER TABLE solicitudes
    ADD COLUMN IF NOT EXISTS payload LONGTEXT NULL AFTER tipo_representante;

-- 2. Agregar columnas consultables de bifurcación/modalidad
ALTER TABLE solicitudes
    ADD COLUMN IF NOT EXISTS bifurcacion_clave VARCHAR(100) NULL AFTER payload,
    ADD COLUMN IF NOT EXISTS modalidad VARCHAR(100) NULL AFTER bifurcacion_clave,
    ADD COLUMN IF NOT EXISTS modalidad_texto VARCHAR(255) NULL AFTER modalidad,
    ADD COLUMN IF NOT EXISTS detalle VARCHAR(100) NULL AFTER modalidad_texto,
    ADD COLUMN IF NOT EXISTS detalle_texto VARCHAR(255) NULL AFTER detalle;

-- 3. Fechas de control
ALTER TABLE solicitudes
    ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER estatus;

-- 4. Tabla específica para propietario del predio
CREATE TABLE IF NOT EXISTS datos_propietario (
    id_propietario INT AUTO_INCREMENT PRIMARY KEY,
    id_solicitud INT NOT NULL,
    nombres VARCHAR(150),
    apellido_paterno VARCHAR(100),
    apellido_materno VARCHAR(100),
    rfc VARCHAR(15),
    telefono VARCHAR(25),
    email VARCHAR(150),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_solicitud) REFERENCES solicitudes(id_solicitud) ON DELETE CASCADE,
    INDEX idx_propietario_solicitud (id_solicitud)
) ENGINE=InnoDB;

-- 5. Tabla específica para recibos/pagos
CREATE TABLE IF NOT EXISTS recibos_pago (
    id_recibo INT AUTO_INCREMENT PRIMARY KEY,
    id_solicitud INT NOT NULL,
    numero_recibo TINYINT NULL,
    folio_recibo VARCHAR(100),
    monto DECIMAL(12,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_solicitud) REFERENCES solicitudes(id_solicitud) ON DELETE CASCADE,
    INDEX idx_recibo_solicitud (id_solicitud),
    INDEX idx_recibo_folio (folio_recibo)
) ENGINE=InnoDB;

-- 6. Mejorar detalles dinámicos para conservar llave técnica
ALTER TABLE detalles_tramite_especifico
    ADD COLUMN IF NOT EXISTS campo_key VARCHAR(150) NULL AFTER id_solicitud,
    ADD COLUMN IF NOT EXISTS grupo VARCHAR(80) NULL AFTER campo_valor,
    ADD COLUMN IF NOT EXISTS orden INT NULL AFTER grupo;

CREATE INDEX IF NOT EXISTS idx_detalles_solicitud 
ON detalles_tramite_especifico(id_solicitud);

CREATE INDEX IF NOT EXISTS idx_detalles_key 
ON detalles_tramite_especifico(campo_key);

-- 7. Índices útiles
CREATE INDEX IF NOT EXISTS idx_solicitudes_folio 
ON solicitudes(folio);

CREATE INDEX IF NOT EXISTS idx_solicitudes_fecha 
ON solicitudes(fecha_ingreso);

CREATE INDEX IF NOT EXISTS idx_solicitudes_tramite 
ON solicitudes(nombre_tramite);

CREATE INDEX IF NOT EXISTS idx_solicitudes_modalidad 
ON solicitudes(modalidad);

CREATE INDEX IF NOT EXISTS idx_requisitos_solicitud 
ON requisitos_presentados(id_solicitud);

CREATE INDEX IF NOT EXISTS idx_representantes_solicitud 
ON datos_representantes(id_solicitud);

CREATE INDEX IF NOT EXISTS idx_interesado_solicitud 
ON datos_interesado(id_solicitud);

ALTER TABLE solicitudes
    ADD COLUMN IF NOT EXISTS estado_proceso VARCHAR(40) NOT NULL DEFAULT 'NUEVO' AFTER estatus,
    ADD COLUMN IF NOT EXISTS etapa_actual VARCHAR(60) NULL AFTER estado_proceso,
    ADD COLUMN IF NOT EXISTS prioridad VARCHAR(20) NULL DEFAULT 'NORMAL' AFTER etapa_actual,
    ADD COLUMN IF NOT EXISTS asignado_a INT NULL AFTER prioridad,
    ADD COLUMN IF NOT EXISTS estado_observaciones TEXT NULL AFTER asignado_a,
    ADD COLUMN IF NOT EXISTS motivo_rechazo TEXT NULL AFTER estado_observaciones,
    ADD COLUMN IF NOT EXISTS fecha_estado TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER motivo_rechazo,
    ADD COLUMN IF NOT EXISTS fecha_resolucion TIMESTAMP NULL DEFAULT NULL AFTER fecha_estado;
    
    CREATE TABLE IF NOT EXISTS historial_solicitud_estados (
    id_historial INT AUTO_INCREMENT PRIMARY KEY,
    id_solicitud INT NOT NULL,
    estado_anterior VARCHAR(40) NULL,
    estado_nuevo VARCHAR(40) NOT NULL,
    observaciones TEXT NULL,
    usuario_id INT NULL,
    usuario_nombre VARCHAR(150) NULL,
    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_solicitud) REFERENCES solicitudes(id_solicitud) ON DELETE CASCADE,
    INDEX idx_historial_solicitud (id_solicitud),
    INDEX idx_historial_estado (estado_nuevo)
) ENGINE=INNODB;

CREATE INDEX idx_solicitudes_estado_proceso ON solicitudes(estado_proceso);
CREATE INDEX idx_solicitudes_materia ON solicitudes(materia);
CREATE INDEX idx_solicitudes_tramite ON solicitudes(nombre_tramite);
CREATE INDEX idx_solicitudes_fecha_estado ON solicitudes(fecha_estado);
    
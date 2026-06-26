-- Tierra con Corazón
-- Fix/migración para activar "Formatos técnicos" y estatus "COMITE".
-- Ejecutar en la base: censo_tlalpan

USE censo_tlalpan;

-- 1) Checklist: columna que usa Captura.php al guardar expediente.
ALTER TABLE encuestas
    ADD COLUMN IF NOT EXISTS check_formatos_tecnicos TINYINT(1) NOT NULL DEFAULT 0
    AFTER check_siniiga_doc;

-- 2) Fase COMITE: necesaria para que Captura pueda activar el estatus
-- y para que el perfil consulta vea solo esos registros.
ALTER TABLE encuestas
    MODIFY COLUMN fase_proceso ENUM(
        'EMPADRONADO',
        'SOLICITUD_INGRESADA',
        'VALIDACION_DOCS',
        'EN_REVISION',
        'COMITE',
        'APROBADO',
        'RECHAZADO'
    ) NOT NULL DEFAULT 'EMPADRONADO';

-- 3) Evidencias separadas por tipo:
-- VERIFICACION_CAMPO = fotos normales de verificación
-- FORMATOS_TECNICOS = las 3 imágenes del nuevo documento
ALTER TABLE encuesta_evidencias
    ADD COLUMN IF NOT EXISTS tipo_evidencia ENUM('VERIFICACION_CAMPO', 'FORMATOS_TECNICOS')
    NOT NULL DEFAULT 'VERIFICACION_CAMPO'
    AFTER ruta_archivo;

ALTER TABLE encuesta_evidencias
    MODIFY COLUMN tipo_evidencia ENUM('VERIFICACION_CAMPO', 'FORMATOS_TECNICOS')
    NOT NULL DEFAULT 'VERIFICACION_CAMPO';

-- 4) Índices para filtros del dashboard/consulta.
CREATE INDEX IF NOT EXISTS idx_encuestas_fase_proceso ON encuestas (fase_proceso);
CREATE INDEX IF NOT EXISTS idx_evidencias_tipo ON encuesta_evidencias (tipo_evidencia);

-- 5) Validación rápida.
DESCRIBE encuestas;
DESCRIBE encuesta_evidencias;

-- Estados logicos de usuarios para Tierra con Corazon.
-- Activo: puede iniciar sesion.
-- Pausado: acceso suspendido temporalmente, sin eliminar usuario.
-- Inactivo: acceso deshabilitado, sin eliminar usuario.

ALTER TABLE usuarios
  ADD COLUMN IF NOT EXISTS estado_acceso ENUM('activo', 'pausado', 'inactivo') NOT NULL DEFAULT 'activo'
  AFTER activo;

UPDATE usuarios
SET estado_acceso = 'inactivo'
WHERE activo = 0
  AND estado_acceso = 'activo';

UPDATE usuarios
SET activo = CASE
    WHEN estado_acceso = 'activo' THEN 1
    ELSE 0
END;

-- Usuario solicitado por Adan: se inactiva sin eliminarlo.
UPDATE usuarios
SET activo = 0,
    estado_acceso = 'inactivo'
WHERE id = 18
   OR usuario = 'maricela.martinez';

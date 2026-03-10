<?php
// Configuración de la base de datos censo_tlalpan
$db = new PDO('mysql:host=localhost;dbname=censo_tlalpan', 'admin_encuesta', 'root');

$nombre = "EDGAR ZAVALA FLORES";
$telefono = "5579209177";
$usuario = "edgar.zavala";
$passwordPlana = "Edga9177!"; // Patrón: Edga + 9177 + !

// Generamos el Hash seguro
$passwordHash = password_hash($passwordPlana, PASSWORD_BCRYPT);

$sql = "INSERT INTO usuarios (usuario, password, nombre_completo, telefono, rol, activo) 
        VALUES (:u, :p, :n, :t, 'encuestador', 1)";

$stmt = $db->prepare($sql);
$exito = $stmt->execute([
    ':u' => $usuario,
    ':p' => $passwordHash,
    ':n' => $nombre,
    ':t' => $telefono
]);

if ($exito) {
    echo "✅ Usuario '{$usuario}' registrado con éxito para el operativo.";
} else {
    echo "❌ Error al registrar al usuario.";
}
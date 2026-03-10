<?php
// Configuración de conexión (Ajusta si es necesario)
$db = new PDO('mysql:host=localhost;dbname=censo_tlalpan', 'admin_encuesta', 'root');

$principales = [
    ['u' => 'admin',      'p' => 'Tlalpan2026!', 'n' => 'Admin Tlalpan'],
    ['u' => 'supervisor', 'p' => 'Super2026!',   'n' => 'Lic. Supervisor'],
    ['u' => 'aGuillen',   'p' => 'Adan2026!',    'n' => 'Adan Guillen']
];

foreach ($principales as $user) {
    // Generamos el Hash seguro para la nueva lógica del Auth.php
    $hash = password_hash($user['p'], PASSWORD_BCRYPT);
    
    $sql = "UPDATE usuarios SET password = :p, nombre_completo = :n WHERE usuario = :u";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':p' => $hash,
        ':n' => $user['n'],
        ':u' => $user['u']
    ]);
    echo "✅ Usuario '{$user['u']}' actualizado y hasheado correctamente.\n";
}
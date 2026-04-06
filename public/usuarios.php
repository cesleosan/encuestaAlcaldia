<?php
// Configuración de la base de datos censo_tlalpan
$db = new PDO('mysql:host=localhost;dbname=censo_tlalpan', 'admin_encuesta', 'root');

// Listado de nuevos capturistas con credenciales ÚNICAS y SEGURAS
$nuevosUsuarios = [
    ['nombre' => 'MARICELA MARTÍNEZ NAVA', 'user' => 'maricela.martinez', 'pass' => 'M4r1#Nava.22'],
    ['nombre' => 'MELINA SANDRA CRUZ AYALA', 'user' => 'melina.cruz', 'pass' => 'Cruz*Meli!85'],
    ['nombre' => 'ANDREA LORELEY ORTIZ CASTILLO', 'user' => 'andrea.ortiz', 'pass' => 'Andr34_Ortiz?'],
    ['nombre' => 'DULCE MELISSA REZA PARRA', 'user' => 'dulce.reza', 'pass' => 'Reza-Dulce.94'],
    ['nombre' => 'JESÚS TENAHUATZIN GÓMEZ OSNAYA', 'user' => 'jesus.gomez', 'pass' => 'Jesu$Osnaya.7'],
    ['nombre' => 'DIANA SOFIA ANDRADE CHACÓN', 'user' => 'diana.andrade', 'pass' => 'Dian4&Chacon.'],
    ['nombre' => 'ALFREDO VALDÉS OSORIO', 'user' => 'alfredo.valdes', 'pass' => 'Valdes+Alfr_0'],
    ['nombre' => 'ADRIANA MONSERRAT MARTÍNEZ LÓPEZ', 'user' => 'adriana.martinez_l', 'pass' => 'Adria*Mart!9']
];

$sql = "INSERT INTO usuarios (usuario, password, nombre_completo, telefono, rol, activo) 
        VALUES (:u, :p, :n, :t, 'capturista', 1)";

$stmt = $db->prepare($sql);

echo "<h2>🛡️ Registro de Seguridad: Nuevos Capturistas</h2>";
echo "<p>Copia esta tabla en un lugar seguro antes de cerrar esta ventana.</p>";
echo "<table border='1' style='border-collapse: collapse; width: 100%; font-family: sans-serif;'>
        <tr style='background-color: #773357; color: white;'>
            <th style='padding: 12px;'>Nombre del Personal</th>
            <th style='padding: 12px;'>Usuario (Login)</th>
            <th style='padding: 12px;'>Contraseña Única</th>
            <th style='padding: 12px;'>Resultado</th>
        </tr>";

foreach ($nuevosUsuarios as $u) {
    // Generamos el Hash de alta seguridad
    $passwordHash = password_hash($u['pass'], PASSWORD_BCRYPT);
    
    // Inserción
    $exito = $stmt->execute([
        ':u' => $u['user'],
        ':p' => $passwordHash,
        ':n' => $u['nombre'],
        ':t' => '5500000000' // Teléfono genérico para evitar errores de campo vacío
    ]);

    $color = $exito ? "#e6ffed" : "#ffeef0";
    $label = $exito ? "<b style='color:green;'>REGISTRADO</b>" : "<b style='color:red;'>ERROR</b>";

    echo "<tr style='background-color: {$color};'>
            <td style='padding: 10px;'>{$u['nombre']}</td>
            <td style='padding: 10px;'><code>{$u['user']}</code></td>
            <td style='padding: 10px;'><mark style='background:#fcf8e3;'>{$u['pass']}</mark></td>
            <td style='padding: 10px; text-align:center;'>{$label}</td>
          </tr>";
}

echo "</table>";
?>
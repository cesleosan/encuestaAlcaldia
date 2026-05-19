<?php
// Crear usuario administrador/root para Tierra con Corazón
// IMPORTANTE: Ejecutar una sola vez y borrar este archivo del servidor.

// Configuración de la base de datos
$db = new PDO(
    'mysql:host=localhost;dbname=censo_tlalpan;charset=utf8mb4',
    'admin_encuesta',
    'root',
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);

// Refuerzo de codificación para evitar nombres tipo MARTÃ�NEZ
$db->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");

// Usuario administrador a crear
$usuarioNuevo = [
    'nombre' => 'FERNANDO ROMERO ROMERO',
    'user'   => 'fernando.romero',
    'pass'   => 'F3rnando*Romero.26',
    'tel'    => '5500000000',

    // En tu sistema el admin real usa rol ROOT.
    // Si de verdad quieres guardarlo como "admin", cambia root por admin,
    // pero root es lo más compatible con tu BD actual.
    'rol'    => 'root'
];

echo "<h2>🛡️ Registro de Seguridad: Nuevo Administrador</h2>";
echo "<p><b>Importante:</b> guarda estas credenciales y elimina este archivo después de ejecutarlo.</p>";

try {
    echo "<table border='1' style='border-collapse: collapse; width: 100%; font-family: sans-serif;'>
            <tr style='background-color: #773357; color: white;'>
                <th style='padding: 12px;'>Nombre</th>
                <th style='padding: 12px;'>Usuario</th>
                <th style='padding: 12px;'>Contraseña</th>
                <th style='padding: 12px;'>Rol</th>
                <th style='padding: 12px;'>Resultado</th>
            </tr>";

    // Verificar si ya existe el usuario
    $check = $db->prepare("SELECT id, rol FROM usuarios WHERE usuario = :usuario LIMIT 1");
    $check->execute([
        ':usuario' => $usuarioNuevo['user']
    ]);

    $existe = $check->fetch();

    if ($existe) {
        // Si ya existe, lo actualizamos a administrador/root y activo
        $passwordHash = password_hash($usuarioNuevo['pass'], PASSWORD_BCRYPT);

        $update = $db->prepare("
            UPDATE usuarios
            SET 
                password = :password,
                nombre_completo = :nombre,
                telefono = :telefono,
                rol = :rol,
                activo = 1
            WHERE usuario = :usuario
        ");

        $update->execute([
            ':usuario'  => $usuarioNuevo['user'],
            ':password' => $passwordHash,
            ':nombre'   => $usuarioNuevo['nombre'],
            ':telefono' => $usuarioNuevo['tel'],
            ':rol'      => $usuarioNuevo['rol']
        ]);

        echo "<tr style='background-color:#fff3cd;'>
                <td style='padding:10px;'>{$usuarioNuevo['nombre']}</td>
                <td style='padding:10px;'><code>{$usuarioNuevo['user']}</code></td>
                <td style='padding:10px;'><mark>{$usuarioNuevo['pass']}</mark></td>
                <td style='padding:10px;'><b>{$usuarioNuevo['rol']}</b></td>
                <td style='padding:10px; text-align:center;'>
                    <b style='color:#856404;'>YA EXISTÍA, SE ACTUALIZÓ A ADMIN/ROOT</b>
                </td>
              </tr>";
    } else {
        // Crear nuevo usuario
        $passwordHash = password_hash($usuarioNuevo['pass'], PASSWORD_BCRYPT);

        $insert = $db->prepare("
            INSERT INTO usuarios 
                (usuario, password, nombre_completo, telefono, rol, activo)
            VALUES 
                (:usuario, :password, :nombre, :telefono, :rol, 1)
        ");

        $insert->execute([
            ':usuario'  => $usuarioNuevo['user'],
            ':password' => $passwordHash,
            ':nombre'   => $usuarioNuevo['nombre'],
            ':telefono' => $usuarioNuevo['tel'],
            ':rol'      => $usuarioNuevo['rol']
        ]);

        echo "<tr style='background-color:#e6ffed;'>
                <td style='padding:10px;'>{$usuarioNuevo['nombre']}</td>
                <td style='padding:10px;'><code>{$usuarioNuevo['user']}</code></td>
                <td style='padding:10px;'><mark>{$usuarioNuevo['pass']}</mark></td>
                <td style='padding:10px;'><b>{$usuarioNuevo['rol']}</b></td>
                <td style='padding:10px; text-align:center;'>
                    <b style='color:green;'>ADMINISTRADOR REGISTRADO</b>
                </td>
              </tr>";
    }

    echo "</table>";

    echo "<br><div style='font-family:sans-serif; padding:15px; background:#f8f9fa; border-left:5px solid #773357;'>
            <b>Credenciales:</b><br>
            Usuario: <code>{$usuarioNuevo['user']}</code><br>
            Contraseña: <code>{$usuarioNuevo['pass']}</code><br>
            Rol en sistema: <code>{$usuarioNuevo['rol']}</code>
          </div>";

} catch (PDOException $e) {
    echo "<h3 style='color:red;'>Error al crear/actualizar usuario</h3>";
    echo "<pre>" . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</pre>";
}
?>
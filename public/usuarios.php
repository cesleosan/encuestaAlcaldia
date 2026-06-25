<?php
// Crea o actualiza usuarios de demostración para Tierra con Corazón.
// IMPORTANTE: ejecutar una sola vez y eliminar este archivo del servidor.

$db = new PDO(
    'mysql:host=localhost;dbname=censo_tlalpan;charset=utf8mb4',
    'admin_encuesta',
    'root',
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);

$db->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");

$usuarios = [
    [
        'nombre' => 'FERNANDO ROMERO ROMERO',
        'user'   => 'fernando.romero',
        'pass'   => 'F3rnando*Romero.26',
        'tel'    => '5500000000',
        'rol'    => 'root'
    ],
    [
        'nombre' => 'USUARIO DEMO CONSULTA',
        'user'   => 'consulta.demo',
        'pass'   => 'Consulta*Demo.26',
        'tel'    => '5500000001',
        'rol'    => 'consulta'
    ]
];

function escapar($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

echo "<h2 style='font-family:sans-serif;'>Usuarios de Tierra con Corazón</h2>";
echo "<p style='font-family:sans-serif;'><b>Importante:</b> elimina este archivo después de ejecutarlo.</p>";

try {
    $check = $db->prepare("SELECT id FROM usuarios WHERE usuario = :usuario LIMIT 1");
    $update = $db->prepare("
        UPDATE usuarios
        SET password = :password,
            nombre_completo = :nombre,
            telefono = :telefono,
            rol = :rol,
            modulo = 'TIERRA',
            activo = 1
        WHERE usuario = :usuario
    ");
    $insert = $db->prepare("
        INSERT INTO usuarios
            (usuario, password, nombre_completo, telefono, rol, modulo, activo)
        VALUES
            (:usuario, :password, :nombre, :telefono, :rol, 'TIERRA', 1)
    ");

    echo "<table border='1' style='border-collapse:collapse;width:100%;font-family:sans-serif;'>
            <tr style='background:#773357;color:white;'>
                <th style='padding:12px;'>Nombre</th>
                <th style='padding:12px;'>Usuario</th>
                <th style='padding:12px;'>Contraseña</th>
                <th style='padding:12px;'>Rol</th>
                <th style='padding:12px;'>Resultado</th>
            </tr>";

    foreach ($usuarios as $usuario) {
        $check->execute([':usuario' => $usuario['user']]);
        $existe = (bool)$check->fetch();

        $parametros = [
            ':usuario'  => $usuario['user'],
            ':password' => password_hash($usuario['pass'], PASSWORD_BCRYPT),
            ':nombre'   => $usuario['nombre'],
            ':telefono' => $usuario['tel'],
            ':rol'      => $usuario['rol']
        ];

        if ($existe) {
            $update->execute($parametros);
            $resultado = 'ACTUALIZADO';
            $color = '#fff3cd';
        } else {
            $insert->execute($parametros);
            $resultado = 'CREADO';
            $color = '#e6ffed';
        }

        echo "<tr style='background:{$color};'>
                <td style='padding:10px;'>" . escapar($usuario['nombre']) . "</td>
                <td style='padding:10px;'><code>" . escapar($usuario['user']) . "</code></td>
                <td style='padding:10px;'><mark>" . escapar($usuario['pass']) . "</mark></td>
                <td style='padding:10px;'><b>" . escapar($usuario['rol']) . "</b></td>
                <td style='padding:10px;text-align:center;'><b>{$resultado}</b></td>
              </tr>";
    }

    echo "</table>";
    echo "<div style='font-family:sans-serif;margin-top:20px;padding:15px;background:#f8f9fa;border-left:5px solid #773357;'>
            <b>Acceso demo de consulta</b><br>
            Usuario: <code>consulta.demo</code><br>
            Contraseña: <code>Consulta*Demo.26</code><br>
            Este perfil solamente verá expedientes en estatus <code>COMITE</code>.
          </div>";
} catch (PDOException $e) {
    echo "<h3 style='color:red;'>Error al crear o actualizar usuarios</h3>";
    echo "<pre>" . escapar($e->getMessage()) . "</pre>";
}
?>

<?php
// Crea o actualiza usuarios para Tierra con Corazon.
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
        'rol'    => 'root',
        'grupo'  => 'Administrador'
    ],
    [
        'nombre' => 'LIC. FRANCISCO HERN&Aacute;NDEZ GONZ&Aacute;LEZ',
        'user'   => 'francisco.hernandez',
        'pass'   => 'Francisco*Hernandez.26',
        'tel'    => null,
        'rol'    => 'consulta',
        'grupo'  => 'Comite'
    ],
    [
        'nombre' => 'LIC. EDGAR AD&Aacute;N ZAVALA FLORES',
        'user'   => 'edgar.zavala',
        'pass'   => 'Edgar*Zavala.26',
        'tel'    => null,
        'rol'    => 'consulta',
        'grupo'  => 'Comite'
    ],
    [
        'nombre' => 'MTRA. CLAUDIA ISLAS LAGOS',
        'user'   => 'claudia.islas',
        'pass'   => 'Claudia*Islas.26',
        'tel'    => null,
        'rol'    => 'consulta',
        'grupo'  => 'Comite'
    ],
    [
        'nombre' => 'LIC. ANDR&Eacute;S DE JES&Uacute;S HERN&Aacute;NDEZ FLORES',
        'user'   => 'andres.hernandez',
        'pass'   => 'Andres*Hernandez.26',
        'tel'    => null,
        'rol'    => 'consulta',
        'grupo'  => 'Comite'
    ],
    [
        'nombre' => 'LIC. AMMY BETHSUA BA&Ntilde;UELOS D&Iacute;AZ',
        'user'   => 'ammy.banuelos',
        'pass'   => 'Ammy*Banuelos.26',
        'tel'    => null,
        'rol'    => 'consulta',
        'grupo'  => 'Comite'
    ],
    [
        'nombre' => 'C. DALIA PATRICIA HERRERA &Aacute;LVAREZ',
        'user'   => 'dalia.herrera',
        'pass'   => 'Dalia*Herrera.26',
        'tel'    => null,
        'rol'    => 'consulta',
        'grupo'  => 'Comite'
    ],
    [
        'nombre' => 'MTRA. DULCE JANETH RAM&Iacute;REZ LUGO',
        'user'   => 'dulce.ramirez',
        'pass'   => 'Dulce*Ramirez.26',
        'tel'    => null,
        'rol'    => 'consulta',
        'grupo'  => 'Comite'
    ],
    [
        'nombre' => 'DRA. JUANA AMALIA SALGADO L&Oacute;PEZ',
        'user'   => 'juana.salgado',
        'pass'   => 'Juana*Salgado.26',
        'tel'    => null,
        'rol'    => 'consulta',
        'grupo'  => 'Comite'
    ],
    [
        'nombre' => 'LIC. ALDO JOVANI SALDA&Ntilde;A MART&Iacute;NEZ',
        'user'   => 'aldo.saldana',
        'pass'   => 'Aldo*Saldana.26',
        'tel'    => null,
        'rol'    => 'consulta',
        'grupo'  => 'Comite'
    ],
    [
        'nombre' => 'C. DAN AK&Eacute; DE LA LUZ',
        'user'   => 'dan.ake',
        'pass'   => 'Dan*Ake.26',
        'tel'    => null,
        'rol'    => 'consulta',
        'grupo'  => 'Comite'
    ],
    [
        'nombre' => 'C. FERNANDO ROMERO ROMERO',
        'user'   => 'fernando.romero.comite',
        'pass'   => 'Fernando*Comite.26',
        'tel'    => null,
        'rol'    => 'consulta',
        'grupo'  => 'Comite'
    ]
];

function escapar($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

function texto($valor) {
    return html_entity_decode((string)$valor, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

echo "<h2 style='font-family:sans-serif;'>Usuarios de Tierra con Corazon</h2>";
echo "<p style='font-family:sans-serif;'><b>Importante:</b> elimina este archivo despues de ejecutarlo.</p>";
echo "<p style='font-family:sans-serif;'>Usuarios finales. Los usuarios de Comite tienen rol <code>consulta</code>: solo ven expedientes en <code>COMITE</code> y no editan ni cambian estatus.</p>";

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
    $desactivarDemo = $db->prepare("
        UPDATE usuarios
        SET activo = 0
        WHERE usuario = 'consulta.demo'
    ");

    echo "<table border='1' style='border-collapse:collapse;width:100%;font-family:sans-serif;'>
            <tr style='background:#773357;color:white;'>
                <th style='padding:12px;'>Grupo</th>
                <th style='padding:12px;'>Nombre</th>
                <th style='padding:12px;'>Usuario</th>
                <th style='padding:12px;'>Contrasena</th>
                <th style='padding:12px;'>Rol</th>
                <th style='padding:12px;'>Resultado</th>
            </tr>";

    foreach ($usuarios as $usuario) {
        $check->execute([':usuario' => $usuario['user']]);
        $existe = (bool)$check->fetch();

        $parametros = [
            ':usuario'  => $usuario['user'],
            ':password' => password_hash($usuario['pass'], PASSWORD_BCRYPT),
            ':nombre'   => texto($usuario['nombre']),
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
                <td style='padding:10px;'>" . escapar($usuario['grupo']) . "</td>
                <td style='padding:10px;'>" . escapar(texto($usuario['nombre'])) . "</td>
                <td style='padding:10px;'><code>" . escapar($usuario['user']) . "</code></td>
                <td style='padding:10px;'><mark>" . escapar($usuario['pass']) . "</mark></td>
                <td style='padding:10px;'><b>" . escapar($usuario['rol']) . "</b></td>
                <td style='padding:10px;text-align:center;'><b>{$resultado}</b></td>
              </tr>";
    }

    $desactivarDemo->execute();

    echo "</table>";
    echo "<div style='font-family:sans-serif;margin-top:20px;padding:15px;background:#f8f9fa;border-left:5px solid #773357;'>
            <b>Accesos finales de Comite</b><br>
            Cada usuario tiene contrasena individual, visible en la tabla superior.<br>
            Estos perfiles solamente consultan expedientes en estatus <code>COMITE</code>; no editan datos ni cambian estatus.
          </div>";
} catch (PDOException $e) {
    echo "<h3 style='color:red;'>Error al crear o actualizar usuarios</h3>";
    echo "<pre>" . escapar($e->getMessage()) . "</pre>";
}
?>

<?php
// Configuración de la base de datos de la Alcaldía
$db = new PDO('mysql:host=localhost;dbname=censo_tlalpan', 'admin_encuesta', 'root');

$tecnicos = [
    ['u'=>'gloria.flores', 'p'=>'Glor1971!', 'n'=>'GLORIA FLORES PEREZ', 't'=>'5526741971'],
    ['u'=>'melina.sandra', 'p'=>'Meli5234!', 'n'=>'MELINA SANDRA CRUZ AYALA', 't'=>'5541745234'],
    ['u'=>'arnulfo.maldonado', 'p'=>'Arnu8425!', 'n'=>'ARNULFO MALDONADO ROBLES', 't'=>'5591998425'],
    ['u'=>'fidel.camacho', 'p'=>'Fide1974!', 'n'=>'FIDEL CAMACHO GALLEGO', 't'=>'5620461974'],
    ['u'=>'andrea.lorelei', 'p'=>'Andr0789!', 'n'=>'ANDREA LORELEI ORTIZ CASTILLO', 't'=>'5626350789'],
    ['u'=>'dulce.malissa', 'p'=>'Dulc0248!', 'n'=>'DULCE MALISSA REZA PARRA', 't'=>'5513650248'],
    ['u'=>'diana.sofia', 'p'=>'Dian7314!', 'n'=>'DIANA SOFIA ANDRADE CHACON', 't'=>'5543727314'],
    ['u'=>'jared.torres', 'p'=>'Jare7039!', 'n'=>'JARED TORRES OLMOS', 't'=>'5648107039'],
    ['u'=>'alfredo.valades', 'p'=>'Alfr9613!', 'n'=>'ALFREDO VALADES OSORIO', 't'=>'5569559613'],
    ['u'=>'adriana.monserrath', 'p'=>'Adri5594!', 'n'=>'ADRIANA MONSERRATH MARTINEZ LOPEZ', 't'=>'553135594'],
    ['u'=>'jesus.tenahutzin', 'p'=>'Jesu1207!', 'n'=>'JESUS TENAHUTZIN GOMEZ OSNAYA', 't'=>'5631931207']
];

$sql = "INSERT INTO usuarios (usuario, password, nombre_completo, telefono, rol, activo) 
        VALUES (:u, :p, :n, :t, 'encuestador', 1)";
$stmt = $db->prepare($sql);

foreach ($tecnicos as $t) {
    $stmt->execute([
        ':u' => $t['u'],
        ':p' => password_hash($t['p'], PASSWORD_BCRYPT),
        ':n' => $t['n'],
        ':t' => $t['t']
    ]);
    echo "✅ Técnico {$t['u']} registrado correctamente.<br>";
}
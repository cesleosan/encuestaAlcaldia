<?php
class MockUser {

    // ESTA ES TU "BASE DE DATOS" FALSA
    private $db_falsa = [
        // 1. EL JEFE SUPREMO (Acceso Total)
        [
            'id' => 1,
            'usuario' => 'admin',
            'password' => '12345', // En real usaríamos password_hash
            'rol' => 'root',
            'nombre' => 'Admin General'
        ],
        // 2. EL SUPERVISOR (Ve datos y exporta, no edita usuarios)
        [
            'id' => 2,
            'usuario' => 'supervisor',
            'password' => '12345',
            'rol' => 'supervisor',
            'nombre' => 'Jefe de Campo'
        ],
        // 3. CONSULTA (Solo ve gráficas)
        [
            'id' => 3,
            'usuario' => 'invitado',
            'password' => '12345',
            'rol' => 'consulta',
            'nombre' => 'Auditor Externo'
        ],
        // 4. ENCUESTADOR (Solo ve la encuesta bonita en el celular)
        [
            'id' => 4,
            'usuario' => 'encuestador',
            'password' => '12345',
            'rol' => 'encuestador',
            'nombre' => 'Juan Pérez'
        ]
    ];

    // Simula la consulta "SELECT * FROM usuarios WHERE usuario = ?"
    public function obtenerUsuario($userBusqueda) {
        foreach ($this->db_falsa as $user) {
            if ($user['usuario'] === $userBusqueda) {
                // Convertimos el array a objeto para que tu Controller no note la diferencia
                return (object) $user;
            }
        }
        return null; // No existe
    }
}
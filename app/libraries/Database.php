<?php
/*
 * Clase Database
 * - Se conecta a la base de datos usando PDO
 * - Prepara sentencias SQL (Statements)
 * - Vincula valores (Bind values)
 * - Retorna filas y resultados
 */
class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;

    public $dbh;   // Database Handler
    private $stmt; // Statement
    private $error;

    public function __construct() {
        // Configurar DSN (Data Source Name)
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        
        $options = array(
            PDO::ATTR_PERSISTENT => true, // Conexión persistente para mayor velocidad
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION // Que nos avise si hay error
        );

        // Instanciar PDO
        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
            // Asegurar caracteres latinos (ñ, tildes)
            $this->dbh->exec("set names utf8");
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            echo "Error de Conexión: " . $this->error;
        }
    }

    // Preparar la consulta SQL
    public function query($sql) {
        $this->stmt = $this->dbh->prepare($sql);
    }

    // Vincular parámetros (Bind)
    // Ejemplo: bind(':id', 1)
    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    // Ejecutar la consulta preparada
    public function execute() {
        return $this->stmt->execute();
    }

    // Obtener un conjunto de registros (Array de objetos)
    public function resultSet() {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Obtener un solo registro
    public function single() {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    // Obtener cantidad de filas afectadas
    public function rowCount() {
        return $this->stmt->rowCount();
    }
}
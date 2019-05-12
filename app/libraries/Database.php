<?php
    // PDO Database Class
    // Connects App to the Database
    // Create Prepared Statements
    // Bind Values
    // Return Rows or Results

    class Database {
        private $db_host = DB_HOST;
        private $db_user = DB_USER;
        private $db_pass = DB_PASS;
        private $db_name = DB_NAME;

        private $db_handle;
        private $db_stmt;
        private $db_error;

        public function __construct() {
            //Set DSN
            $dsn = "mysql:host=" . $this->db_host . ';dbname=' . $this->db_name;
            $options = array (
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            );

            //Create PDO Instance
            try {
                $this->db_handle = new PDO($dsn, $this->db_user, $this->db_pass, $options);
            }
            catch(PDOException $ex) {
                $this->db_error = $ex->getMessage();
                echo $this->db_error;
            }
        }

        //Prepare the SQL Statement
        public function query($sql) {
            $this->db_stmt = $this->db_handle->prepare($sql);
        }

        public function bind($param, $value, $type = null) {
            if ( is_null($type) ) {
                switch( true ) {
                    case is_int($value) : 
                        $type = PDO::PARAM_INT;
                        break;
                    case is_bool($value) : 
                        $type = PDO::PARAM_BOOL;
                        break;
                    case is_null($value) : 
                        $type = PDO::PARAM_NULL;
                        break;
                    default : 
                        $type = PDO::PARAM_STR;
                }
            }

            //bind the value after determining the data type
            $this->db_stmt->bindValue($param, $value, $type);
        }

        public function execute() {
            return $this->db_stmt->execute();
        }

        //Get All Records
        public function getResultSet() {
            $this->execute();
            return $this->db_stmt->fetchAll(PDO::FETCH_OBJ);
        }

        //Get Single Record
        public function getResultRow() {
            $this->execute();
            return $this->db_stmt->fetch(PDO::FETCH_OBJ);
        }

        //Get Row Count
        public function getRowCount() {
            return $this->db_stmt->rowCount();
        }

        public function getLastInsertId() {
            return $this->db_handle->lastInsertId();
        }

        public function getError() {
            return $this->db_error;
        }
    }

?>
<?php 
    class Database {
        // DB parameters
        //for mysql
        private $host = '';
        private $db_name = '';
        private $username = '';
        private $password = '';

        public function __construct(){
            $this->host = $_ENV['DB_HOST'];
            $this->db_name = $_ENV['DB_DATABASE'];
            $this->username = $_ENV['DB_USERNAME'];
            $this->password = $_ENV['DB_PASSWORD'];
        }

        public function connect(){
            $this->conn = null;

            try {
                $this->conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $e){
                http_response_code(500);
                print_r(json_encode(
                    array(
                        'success'=>false,
                        'message' => $e->getMessage(),
                    )
                ));
                exit();
            }
            return $this->conn;
        }
    }
?>
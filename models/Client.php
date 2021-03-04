<?php
// 'user' object
require "../../../vendor/autoload.php";
use \Firebase\JWT\JWT;
class Client{
 
    // database connection and table name
    private $conn;
    private $table_name = "client";
 
    // object properties
    public $id;
    public $app_name;
    public $client_id;
    public $token;
 
    // constructor
    public function __construct($db){
        $this->conn = $db;
    }
    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    public function generate_client_id_password($password){
        $this->client_id = uniqid("CLI",false) . $this->generateRandomString(5);
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        return print_r(json_encode(
            array(
                'success'=>true,
                'message' => "Client Created.",
                'password' => $password_hash,
                'credentials' => array(
                    'client_id' => $this->client_id,
                    'password' => $password
                )
            )
        ));
    }
    // check if given email exist in the database
    public function check_app_exists(){
        // query to check if email exists
        $query = "SELECT app_name, password
                FROM " . $this->table_name . "
                WHERE client_id = ?
                LIMIT 0,1";
        // prepare the query
        $stmt = $this->conn->prepare( $query );
        // sanitize
        $this->client_id=htmlspecialchars(strip_tags($this->client_id));
        // bind given email value
        $stmt->bindParam(1, $this->client_id); 
        // execute the query
        $stmt->execute();
        // get number of rows
        $num = $stmt->rowCount();
        // if email exists, assign values to object properties for easy access and use for php sessions
        if($num>0){
            // get record details / values
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // assign values to object properties
            $this->app_name = $row['app_name'];
            $this->password = $row['password'];
            // return true because email exists in the database
            return true;
        }
        // return false if email does not exist in the database
        return false;
    }

    //login user
    public function get_secret_key($client_id, $password){
        // set product property values
        $this->client_id = $client_id;
        $app_exists = $this->check_app_exists();

        // check if email exists and if password is correct
        if($app_exists && password_verify($password, $this->password)){
        
            $token = array(
            "iat" => time(),
            "iss" =>  $_ENV['JWT_ISSUER'],
            "data" => array(
                    "client_id" => $this->client_id,
                    "app_name" => $this->app_name
                )
            );
        
            // generate jwt
            $jwt = JWT::encode($token, $_ENV['JWT_KEY']);
            return print_r(json_encode(
                array(
                    'success'=>true,
                    'message' => "Authorized.",
                    'token' => $jwt
                )
            ));
        }else{
            return print_r(json_encode(
                array(
                    'success'=>false,
                    'message' => "Incorrect Id or password."
                )
            ));
        }
    }

    //delete user
    public function validate_token($jwt_token){
        // if decode succeed, show user details
        try {
            // decode jwt
            $decoded = JWT::decode($jwt_token, $_ENV['JWT_KEY'], array('HS256'));

            //check the client id
            if(!strcmp($decoded->data->client_id, $_ENV['CLIENT_ID'])){
                // compare 
                return true;
            }else{
                return false;
            }
        }catch (Exception $e){
            // set response code
            http_response_code(401);
            return false;
        }
    }
    
}
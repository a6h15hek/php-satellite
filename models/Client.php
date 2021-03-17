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
    private $session_token;
    public $access_origin;
 
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
    public function createClient(){
        try{
            // insert query
            $query = "INSERT INTO " . $this->table_name . "
                    SET
                        app_name = :app_name,
                        client_id = :client_id,
                        session_token = :session_token ";

            // prepare the query
            $stmt = $this->conn->prepare($query);

            //creating unique user id 
            $this->client_id = uniqid("CLI",false) . $this->generateRandomString(5);
            $stmt->bindParam(':client_id', $this->client_id);

            // bind the values
            $stmt->bindParam(':app_name', $this->app_name);

            //login token for security
            $this->session_token = htmlspecialchars(strip_tags($this->generateRandomString(7)));
            $stmt->bindParam(':session_token', $this->session_token);

            // execute the query, also check if query was successful
            if($stmt->execute()){
                http_response_code(200);
                return json_encode(
                    array(
                        'success'=>true,
                        'message' => "Client Created."
                    )
                );
            }

            http_response_code(500);
            return json_encode(
                array(
                    'success'=>false,
                    'message' => "Unable to create client."
                )
            );
        }catch (PDOException $e){
            http_response_code(500);
            return json_encode(
                array(
                    'success'=>false,
                    'message' => $e->getMessage()
                )
            );
        }
    }
    
    // check if given email exist in the database
    public function check_app_exists(){
        // query to check if email exists
        $query = "SELECT app_name
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
            // return true because email exists in the database
            return true;
        }
        // return false if email does not exist in the database
        return false;
    }

    //login user
    public function get_secret_key($client_id){
        // set product property values
        $this->client_id = $client_id;
        $app_exists = $this->check_app_exists();

        // check if email exists and if password is correct
        if($app_exists){

            $token = array(
            "iat" => time(),
            "iss" =>  $_ENV['JWT_ISSUER'],
            "data" => array(
                    "user_id" => $this->client_id,
                    "role" => "client"
                )
            );
        
            // generate jwt
            $jwt = JWT::encode($token, $_ENV['JWT_KEY']);
            http_response_code(200);
            return json_encode(
                array(
                    'success'=>true,
                    'message' => "Copy this token & use as SECRET_KEY in client application.",
                    'token' => $jwt
                )
            );
        }else{
            http_response_code(401);
            return json_encode(
                array(
                    'success'=>false,
                    'message' => "App not exists."
                )
            );
        }
    }

    //delete client
    public function delete(){
        // Create query
        $query = "DELETE FROM " . $this->table_name . "
                  WHERE client_id=:client_id ";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind data
        $stmt->bindParam(':client_id', $this->client_id);
        try{
            if($stmt->execute()) {
                http_response_code(200);
                return json_encode(
                    array(
                        'success'=>true,
                        'message' => 'Client deleted.'
                    )
                );
            }else{
                http_response_code(500);
                return json_encode(
                    array(
                        'success'=>false,
                        'message' => $stmt->error
                    )
                );
            }
        }catch (Exception $e){
            http_response_code(500);
            return json_encode(
                array(
                    'success'=>false,
                    'message' => $e->getMessage()
                )
            );
        }            
    }
    public function getClients(){
        try{
           // Creating query
            $query ='SELECT  app_name, client_id
                    FROM '. $this->table_name ;
            
            //preparing statement
            $stmt = $this->conn->prepare($query);
            // executing and checking
        
            if(!$stmt->execute()){
                http_response_code(500);
                return json_encode(
                    array(
                        'success'=>false,
                        'message' => $stmt->error
                    )
                );
            }
        }catch (Exception $e){
            http_response_code(500);
            return json_encode(
                array(
                    'success'=>false,
                    'message' => $e->getMessage()
                )
            );
        }
        
        $row_count = $stmt->rowCount();

        if($row_count > 0){
            $clients_array = array();

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $client = array(
                    'app_name' => $app_name,
                    'client_id' => $client_id          
                );
        
                // Push to "data"
                array_push($clients_array, $client);
            }
            // return to json
            http_response_code(200);
            return json_encode(
                array(
                    'success'=>true,
                    'message' => "Client Fetched.",
                    'data'=>$clients_array
                )
            );
        }else{
            // No document
            http_response_code(404);
            return json_encode(
                array(
                    'success'=>false,
                    'message' => 'No Clients. Create one'
                )
            );
        }
    }
}
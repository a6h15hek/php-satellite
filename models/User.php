<?php
// 'user' object
require "../../../vendor/autoload.php";
use \Firebase\JWT\JWT;
class User{
 
    // database connection and table name
    private $conn;
    private $table_name = "users";
 
    // object properties
    public $id;
    public $firstname;
    public $lastname;
    public $email;
    public $role;
    public $password;
    public $user_id;
    private $login_token;

    public $access_role;

    //admin usage 
    private $total_users;
 
    // constructor
    public function __construct($db){
        $this->conn = $db;
    }
     // Add document using collection_name returns document_name
    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
 
    // create() method will be here
    // create new user record
    public function create(){
        try{
            // insert query
            $query = "INSERT INTO " . $this->table_name . "
                    SET
                        user_id = :user_id,
                        firstname = :firstname,
                        lastname = :lastname,
                        email = :email,
                        password = :password,
                        login_token = :login_token";

            // prepare the query
            $stmt = $this->conn->prepare($query);

            // sanitize
            $this->firstname=htmlspecialchars(strip_tags($this->firstname));
            $this->lastname=htmlspecialchars(strip_tags($this->lastname));
            $this->email=htmlspecialchars(strip_tags($this->email));
            $this->password=htmlspecialchars(strip_tags($this->password));

            //creating unique user id 
            $this->user_id = uniqid("U",false) . $this->generateRandomString(5);

            // bind the values
            $stmt->bindParam(':user_id', $this->user_id);
            $stmt->bindParam(':firstname', $this->firstname);
            $stmt->bindParam(':lastname', $this->lastname);
            $stmt->bindParam(':email', $this->email);

            //login token for security
            $this->login_token = $this->generateRandomString(7);
            $stmt->bindParam(':login_token', $this->login_token);

            // hash the password before saving to database
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $password_hash);

            // execute the query, also check if query was successful
            if($stmt->execute()){
                http_response_code(200);
                return json_encode(
                    array(
                        'success'=>true,
                        'message' => "User Created."
                    )
                );
            }

            http_response_code(500);
            return json_encode(
                array(
                    'success'=>false,
                    'message' => "Unable to create User."
                )
            );
        }catch (PDOException $e){
            $this->conn->rollBack();
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
    public function emailExists(){
        // query to check if email exists
        $query = "SELECT user_id, role, login_token, password
                FROM " . $this->table_name . "
                WHERE email = ?
                LIMIT 0,1";
        // prepare the query
        $stmt = $this->conn->prepare( $query );
        // sanitize
        $this->email=htmlspecialchars(strip_tags($this->email));
        // bind given email value
        $stmt->bindParam(1, $this->email); 
        // execute the query
        $stmt->execute();
        // get number of rows
        $num = $stmt->rowCount();
        // if email exists, assign values to object properties for easy access and use for php sessions
        if($num>0){
            // get record details / values
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // assign values to object properties
            $this->user_id = $row['user_id'];
            $this->role = $row['role'];
            $this->login_token = $row['login_token'];
            $this->password = $row['password'];
            // return true because email exists in the database
            return true;
        }
        // return false if email does not exist in the database
        return false;
    }

    //login user
    public function login($email, $password){
        // set product property values
        $this->email = $email;
        $email_exists = $this->emailExists();

        // check if email exists and if password is correct
        if($email_exists && password_verify($password, $this->password)){
        
            $token = array(
            "iat" => time(),
            "iss" =>  $_ENV['JWT_ISSUER'],
            "login_token" => $this->login_token,
            "data" => array(
                    "user_id" => $this->user_id,
                    "role" => $this->role
                )
            );
        
            // generate jwt
            $jwt = JWT::encode($token, $_ENV['JWT_KEY']);
            http_response_code(200);
            return json_encode(
                array(
                    'success'=>true,
                    'message' => "Login successfully.",
                    'token' => $jwt
                )
            );
        }else{
            http_response_code(401);
            return json_encode(
                array(
                    'success'=>false,
                    'message' => "email or password incorrect."
                )
            );
        }
    }

    private function get_login_token(){
        $query = "SELECT login_token
                FROM " . $this->table_name . "
                WHERE user_id = ?
                LIMIT 0,1";
        // prepare the query
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $this->user_id); 
        $stmt->execute();

        $this->login_token = $stmt->fetchColumn();
        return $this->login_token ;
    }


    //delete user
    public function validate_token($jwt_token){
        // if decode succeed, show user details
        try {
            // decode jwt
            $decoded = JWT::decode($jwt_token, $_ENV['JWT_KEY'], array('HS256'));
            $this->user_id = $decoded->data->user_id;

            if(!strcmp($decoded->login_token, $this->get_login_token())){
                return $decoded->data;
            }else{
                return false;
            }
        }catch (Exception $e){
            // set response code
            http_response_code(401);
            return false;
        }
    }  

    public function logout(){
        try{
            $query = 'UPDATE users
                      SET login_token = :login_token 
                      WHERE user_id = :user_id ';
            // prepare the query
            $stmt = $this->conn->prepare( $query );

            $this->login_token = htmlspecialchars(strip_tags($this->generateRandomString(7)));
            $stmt->bindParam(':login_token', $this->login_token); 
            $stmt->bindParam(':user_id', $this->user_id); 

            if($stmt->execute()){
                return json_encode(
                    array(
                        'success'=>true,
                        'message' => "logout successful."
                    )
                );
            }

            http_response_code(500);
            return json_encode(
                array(
                    'success'=>false,
                    'message' => "Unable to logout."
                )
            );
        }catch(PDOException $e){
            http_response_code(500);
            return json_encode(
                array(
                    'success'=>false,
                    'message' => $e->getMessage()
                )
            );
        }
    }

    public function getUser(){
         try{
            $query = "SELECT email, firstname, lastname, role
            FROM " . $this->table_name . "
            WHERE user_id = ?
            LIMIT 0,1";
            // prepare the query
            $stmt = $this->conn->prepare( $query );
            $this->user_id=htmlspecialchars(strip_tags($this->user_id));
            // bind given user Id value
            $stmt->bindParam(1, $this->user_id); 
            $stmt->execute();
            $num = $stmt->rowCount();

            if($num>0){
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                // assign values to object properties
                $this->role = $row['role'];
                $this->firstname = $row['firstname'];
                $this->lastname = $row['lastname'];
                $this->email = $row['email'];

                return json_encode(
                    array(
                        'success'=>true,
                        'data' => array(
                            'user_id' => $this->user_id,
                            'firstname' => $this->firstname,
                            'lastname' => $this->lastname,
                            'role' => $this->role,
                            'email' => $this->email
                        )
                    )
                );
            }
         }catch(PDOException $e){
            http_response_code(500);
            return json_encode(
                array(
                    'success'=>false,
                    'message' => $e->getMessage()
                )
            );
         }
    }

    public function getuserslist($start = 0, $end = 12){
        try {
            $this->conn->beginTransaction();
            $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE role='user' ";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            //getting total users
            $this->total_users =$stmt->fetchColumn();
            $stmt->closeCursor();

            $query = "SELECT  user_id, firstname, lastname, email 
            FROM " . $this->table_name . " WHERE role='user'
            LIMIT :start , :end ";
            // prepare the query
            $stmt = $this->conn->prepare( $query );
            // bind given user Id value
            $stmt->bindValue(':start', (int) trim($start), PDO::PARAM_INT);
            $stmt->bindValue(':end', (int) trim($end), PDO::PARAM_INT);
            $stmt->execute();
            $num = $stmt->rowCount();
            
            if($num>0){
                $users_array = array();

                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                   
                    $user = array(
                        'user_id' => $user_id,
                        'firstname' => $firstname,
                        'lastname' => $lastname,
                        'email' => $email              
                    );
                    // Push to "data"
                    array_push($users_array, $user);
                }   
                $stmt->closeCursor();
                $this->conn->commit();

                http_response_code(200);
                return json_encode(
                    array(
                        'success'=>true,
                        'total_user'=>(int)$this->total_users,
                        'users'=>$users_array
                    )
                );
            }
            
            http_response_code(404);
            return json_encode(
                array(
                    'success'=>false,
                    'message' => "Users not found, try changing limits."
                )
            );
        } catch (PDOException $e){
            $this->conn->rollBack();
            http_response_code(500);
            return json_encode(
                array(
                    'success'=>false,
                    'message' => $e->getMessage()
                )
            );
        }
    }

    //delete user
    public function delete(){
        // Create query
        $query = "DELETE FROM " . $this->table_name . "
                  WHERE user_id=:user_id ";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind data
        $stmt->bindParam(':user_id', $this->user_id);
        try{
            if($stmt->execute()) {
                http_response_code(200);
                return json_encode(
                    array(
                        'success'=>true,
                        'message' => 'User deleted.'
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
}
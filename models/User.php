<?php
// 'user' object
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
                        password = :password";

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

            // hash the password before saving to database
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $password_hash);

            // execute the query, also check if query was successful
            if($stmt->execute()){
                return print_r(json_encode(
                    array(
                        'success'=>true,
                        'message' => "User Created."
                    )
                ));
            }

            return print_r(json_encode(
                array(
                    'success'=>false,
                    'message' => "Unable to create User."
                )
            ));
        }catch (PDOException $e){
            $this->conn->rollBack();
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
        $query = "SELECT user_id, role ,password
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
            // $this->firstname = $row['firstname'];
            // $this->lastname = $row['lastname'];
            $this->password = $row['password'];
            // return true because email exists in the database
            return true;
        }
        // return false if email does not exist in the database
        return false;
    }
}
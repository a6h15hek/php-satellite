<?php 
    /**
     * documents
     * ******************
     * id (primary key)
     * collection_id (foreign Key)
     * document_name
     * data_object
     * updated_at
     * created_at
     */
    class Document {
         // database variables
         private $conn = null;
         private $table = 'documents';

         //user properties
         public $user_id;
         public $role;
         public $created_by;
         private $read_per;
         private $write_per;

         // collection Properties
         public $id;
         public $collection_id;
         public $collection_name;
         public $document_id;
         public $document_name;
         public $data_object;
         public $updated_at;
         public $created_at;
         public $start;
         public $end;
 
         public function __construct($db){
             $this->conn = $db;
         }

        // Get object using collection_name & document_name
        public function get($collection_name,$document_name){
            if(!strcmp($this->role, "admin")){
                $query = 'SELECT doc.updated_at, doc.created_at , doc.data_object
                      FROM ' . $this->table . ' doc LEFT JOIN collections col ON doc.collection_id = col.id 
                      WHERE col.collection_name = :collection_name AND doc.document_name = :document_name 
                      LIMIT 0,1 ';

                 //preparing statement
                $stmt = $this->conn->prepare($query);
                // Bind ID
                $stmt->bindParam(':collection_name', $collection_name);
                $stmt->bindParam(':document_name', $document_name);
            }else{
                $query = 'SELECT doc.updated_at, doc.created_at , doc.data_object
                      FROM ' . $this->table . ' doc LEFT JOIN collections col ON doc.collection_id = col.id 
                      WHERE col.collection_name = :collection_name AND doc.document_name = :document_name 
                      AND (col.read_per = "public" OR doc.created_by = :user_id )
                      LIMIT 0,1 ';
                 //preparing statement
                 $stmt = $this->conn->prepare($query);
                 // Bind ID
                 $stmt->bindParam(':collection_name', $collection_name);
                 $stmt->bindParam(':document_name', $document_name);
                 $stmt->bindParam(':user_id', $this->user_id);
            }

             // executing and checking
             try{
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
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                // Set properties
                $document_obj = array(
                    'dataObject' => json_decode($row['data_object']),
                    'updatedAt' => $row['updated_at'],
                    'createdAt' => $row['created_at']
                );
                http_response_code(200);
                return json_encode(
                    array(
                        'success'=>true,
                        'data'=>$document_obj
                    )
                );
            } else{
                http_response_code(401);
                return json_encode(
                    array(
                        'success'=>false,
                        'message'=>'Document not found or permission denied.'
                    )
                );
            }

        }
        // Get object or objects array collection_name & conditions
        // Get object using orderby and limit 

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

        private function isJson($string) {
           $string = json_encode($string);
           return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
        }

        public function add($collection_name,$data_object){
            if(!$this->isJson($data_object)){
                http_response_code(400);
                return json_encode(
                    array(
                        'success'=>false,
                        'message' => "data_object not valid."
                    )
                );
            }
            try {
                $this->conn->beginTransaction();
                $query1 = 'SELECT id, write_per FROM collections WHERE collection_name=:collection_name';
                 //preparing statement
                 $stmt = $this->conn->prepare($query1);
                 // Bind ID
                 $stmt->bindParam(':collection_name', $collection_name);
                 $stmt->execute();

                //get column id 
                // $this->collection_id =$stmt->fetchColumn();
                if($stmt->rowCount() > 0){
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $this->collection_id = $row['id'];
                    $this->write_per = $row['write_per'];
                }else{
                    $this->collection_id = false;
                }
                 $stmt->closeCursor();

                if(!$this->collection_id){
                    $query2 = 'INSERT INTO collections (collection_name) 
                               VALUES (:collectionname) ';
                    //preparing statement
                    $stmt = $this->conn->prepare($query2);
                    // Bind ID
                    $stmt->bindParam(':collection_name', $collection_name);
                    $this->collection_name=htmlspecialchars(strip_tags($collection_name));
                    $stmt->execute(array(':collectionname' => $this->collection_name));
                    $this->collection_id =  $this->conn->lastInsertId();
                    $stmt->closeCursor();
                }


                $query3 = 'INSERT INTO documents (collection_id, document_name, data_object, created_by)
                           VALUES (:collection_id,:document_name,:data_object,:created_by) ';
                 //preparing statement
                 $stmt = $this->conn->prepare($query3);
                 // Bind ID
                 $stmt->bindParam(':collection_id', $this->collection_id);
                 $this->data_object = json_encode($data_object);
                 $stmt->bindParam(':data_object', $this->data_object);
                 $this->document_name = uniqid("DOC",false) . $this->generateRandomString(5);
                 $stmt->bindParam(':document_name', $this->document_name);
                 $stmt->bindParam(':created_by', $this->user_id);
                 $stmt->execute();
                 $stmt->closeCursor();

                 //commit changes
                 $this->conn->commit();

                http_response_code(200);
                return json_encode(
                    array(
                        'success'=>true,
                        'message' => "document added.",
                        'document_name' => $this->document_name
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
        
        // Write document using collection_name & document_name
        public function set($collection_name, $document_name, $data_object, $merge = false){
            if(!$this->isJson($data_object)){
                http_response_code(400);
                return json_encode(
                    array(
                        'success'=> false,
                        'message' => "data_object not valid."
                    )
                );
            }
            try {
                $this->conn->beginTransaction();
                $query1 = 'SELECT id, write_per FROM collections WHERE collection_name=:collection_name';
                 //preparing statement
                $stmt = $this->conn->prepare($query1);
                 // Bind ID
                $stmt->bindParam(':collection_name', $collection_name);
                $stmt->execute();

                 //get column id 
                //$this->collection_id =$stmt->fetchColumn();
                if($stmt->rowCount() > 0){
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $this->collection_id = $row['id'];
                    $this->write_per = $row['write_per'];
                }else{
                    $this->collection_id = false;
                }
                $stmt->closeCursor();

                if(!$this->collection_id){
                    $query2 = 'INSERT INTO collections (collection_name) 
                               VALUES (:collectionname) ';
                    //preparing statement
                    $stmt = $this->conn->prepare($query2);
                    // Bind ID
                    $stmt->bindParam(':collection_name', $collection_name);
                    $this->collection_name=htmlspecialchars(strip_tags($collection_name));
                    $stmt->execute(array(':collectionname' => $this->collection_name));
                    $this->collection_id =  $this->conn->lastInsertId();
                    $stmt->closeCursor();
                 }

                $query3 = 'SELECT id, created_by FROM documents WHERE document_name=:document_name';
                 //preparing statement
                $stmt = $this->conn->prepare($query3);
                 // Bind ID
                $stmt->bindParam(':document_name', $document_name);
                $stmt->execute();

                 //get column id 
                // $this->document_id =$stmt->fetchColumn();
                if($stmt->rowCount() > 0){
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $this->document_id = $row['id'];
                    $this->created_by = $row['created_by'];
                }else{
                    $this->document_id = false;
                }
                
                $stmt->closeCursor();


                if($this->document_id){
                    //document exists
                    //check for user permission to change document
                    if(!strcmp($this->write_per,"public") || !strcmp($this->role, "admin") || !strcmp($this->user_id, $this->created_by)){
                         //updating document
                        if($merge){
                            $query4 = 'UPDATE documents
                                    SET data_object = JSON_MERGE_PATCH(data_object, :data_object) 
                                    WHERE id = :document_id ';
                        }else{
                            $query4 = 'UPDATE documents
                                    SET data_object = :data_object
                                    WHERE id = :document_id ';
                        }
                        //preparing statement
                        $stmt = $this->conn->prepare($query4);
                        // Bind ID
                        $stmt->bindParam(':document_id', $this->document_id);
                        $this->data_object = json_encode($data_object);
                        $stmt->bindParam(':data_object', $this->data_object);
                        $stmt->execute();
                        $stmt->closeCursor();
                    }else{
                        http_response_code(401);
                        return json_encode(
                            array(
                                'success'=>false,
                                'message' => "access denied. user_id not match."
                            )
                        );   
                    }
                    
                }else{
                    //document not exist
                    $query5 = 'INSERT INTO documents (collection_id, document_name, data_object, created_by)
                           VALUES (:collection_id,:document_name,:data_object,:created_by) ';
                    //preparing statement
                    $stmt = $this->conn->prepare($query5);
                    // Bind ID
                    $stmt->bindParam(':collection_id', $this->collection_id);
                    $this->data_object = json_encode($data_object);
                    $stmt->bindParam(':data_object', $this->data_object);
                    $this->document_name = $document_name;
                    $stmt->bindParam(':document_name', $this->document_name);
                    $stmt->bindParam(':created_by', $this->user_id);
                    $stmt->execute();
                    $stmt->closeCursor();
                }
                
                 //commit changes
                 $this->conn->commit();
                http_response_code(200);
                return json_encode(
                    array(
                        'success'=>true,
                        'message' => "Document successfully written!"
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
        
        // Update the document using collection_name & document_name
        // Update Element in array using collection_name $ document_name & field_name & data (add and delete)
        public function updatearray($collection_name, $document_name, $arrayfield, $arrayelement, $action="add"){
            try {
                $this->conn->beginTransaction();
                $query1 = 'SELECT id,write_per FROM collections WHERE collection_name=:collection_name';
                 //preparing statement
                $stmt = $this->conn->prepare($query1);
                 // Bind ID
                $stmt->bindParam(':collection_name', $collection_name);
                $stmt->execute();

                 //get column id 
                // $this->collection_id =$stmt->fetchColumn();
                if($stmt->rowCount() > 0){
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $this->collection_id = $row['id'];
                    $this->write_per = $row['write_per'];
                }else{
                    $this->collection_id = false;
                }
                $stmt->closeCursor();

                if(!$this->collection_id){
                    $query2 = 'INSERT INTO collections (collection_name) 
                               VALUES (:collectionname) ';
                    //preparing statement
                    $stmt = $this->conn->prepare($query2);
                    // Bind ID
                    $stmt->bindParam(':collection_name', $collection_name);
                    $this->collection_name=htmlspecialchars(strip_tags($collection_name));
                    $stmt->execute(array(':collectionname' => $this->collection_name));
                    $this->collection_id =  $this->conn->lastInsertId();
                    $stmt->closeCursor();
                 }

                $query3 = 'SELECT id, data_object, created_by FROM documents WHERE document_name=:document_name';
                 //preparing statement
                $stmt = $this->conn->prepare($query3);
                 // Bind ID
                $stmt->bindParam(':document_name', $document_name);
                $stmt->execute();

                if($stmt->rowCount() > 0){
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $this->document_id = $row['id'];
                    $this->data_object = $row['data_object'];
                    $this->created_by = $row['created_by'];
                }else{
                    $this->document_id = false;
                }
                $stmt->closeCursor();

                if(!strcmp($this->write_per,"public") || !strcmp($this->role, "admin") || !strcmp($this->user_id, $this->created_by)){
                    if($action == "remove"){
                        //document not exist to remove element
                        if(!$this->document_id){
                            http_response_code(404);
                            return json_encode(
                                array(
                                    'success'=>false,
                                    'message' => "document not exist."
                                )
                            );
                        }
                            $query4 = 'UPDATE documents
                                        SET data_object = JSON_REMOVE(data_object, JSON_UNQUOTE(JSON_SEARCH(data_object, "one", :arrayelement)))
                                        WHERE id = :document_id  AND JSON_SEARCH(data_object, "one", :arrayelement) IS NOT NULL';
                            //preparing statement
                            $stmt = $this->conn->prepare($query4);
                            // Bind ID
                            $stmt->bindParam(':document_id', $this->document_id);
                            // $arrayfield = "$." . $arrayfield;
                            // $stmt->bindParam(':arrayfield', $arrayfield);
                            $stmt->bindParam(':arrayelement', $arrayelement);
                            $stmt->execute();
                            $stmt->closeCursor();
    
                    }else if($action == "add"){
                        if($this->document_id){
                            //document exists
                            $this->data_object = json_decode($this->data_object,true);
                            if(array_key_exists($arrayfield, $this->data_object)){
                                //updating document
                                $query4 = 'UPDATE documents
                                    SET data_object = JSON_ARRAY_APPEND(data_object, :arrayfield, :arrayelement)
                                    WHERE id = :document_id AND JSON_SEARCH(data_object, "one", :arrayelement) IS NULL';
                                $arrayfield = "$." . $arrayfield;
                            }else{
                                //updating document
                                $query4 = 'UPDATE documents
                                    SET data_object = JSON_MERGE_PATCH(data_object, JSON_OBJECT(:arrayfield, JSON_ARRAY(:arrayelement)))
                                    WHERE id = :document_id AND JSON_SEARCH(data_object, "one", :arrayelement) IS NULL';
                            }
                            
                            //preparing statement
                            $stmt = $this->conn->prepare($query4);
                            // Bind ID
                            $stmt->bindParam(':document_id', $this->document_id);
                            $stmt->bindParam(':arrayfield', $arrayfield);
                            $stmt->bindParam(':arrayelement', $arrayelement);
                            $stmt->execute();
                            $stmt->closeCursor();
                            
                        }else{
                            //document not exist
                            $query5 = 'INSERT INTO documents (collection_id, document_name, data_object)
                                   VALUES (:collection_id,:document_name, JSON_OBJECT(:arrayfield, JSON_ARRAY(:arrayelement))) ';
                            //preparing statement
                            $stmt = $this->conn->prepare($query5);
                            // Bind ID
                            $stmt->bindParam(':collection_id', $this->collection_id);
                            $stmt->bindParam(':arrayfield', $arrayfield);
                            $stmt->bindParam(':arrayelement', $arrayelement);
                            $this->document_name = $document_name;
                            $stmt->bindParam(':document_name', $this->document_name);
                            $stmt->execute();
                            $stmt->closeCursor();
                        }
                    }
                }else{
                    http_response_code(401);
                        return json_encode(
                            array(
                                'success'=>false,
                                'message' => "access denied. user_id not match."
                            )
                        );      
                }
                
                 //commit changes
                 $this->conn->commit();

                http_response_code(200);
                if($action == "add"){
                    return json_encode(
                        array(
                            'success'=>true,
                            'message' => "Element added successfully.",
                        )
                    );   
                }else if($action == "remove"){
                    return json_encode(
                        array(
                            'success'=>true,
                            'message' => "Element removed successfully.",
                        )
                    );
                }
            
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

        //admins function
        public function getdocuments($collection_name,$start=0,$end=10){
            if(!strcmp($this->role, "admin")){
                $query = 'SELECT doc.updated_at, doc.created_at, doc.document_name, col.collection_name, doc.data_object
                      FROM ' . $this->table . ' doc LEFT JOIN collections col ON doc.collection_id = col.id 
                      WHERE col.collection_name = :collection_name LIMIT :start , :end ';
                      //preparing statement
                $stmt = $this->conn->prepare($query);
                // Bind ID
                $stmt->bindParam(':collection_name', $collection_name);
                $stmt->bindValue(':start', (int) trim($start), PDO::PARAM_INT);
                $stmt->bindValue(':end', (int) trim($end-$start), PDO::PARAM_INT);
            }else{
                $query = 'SELECT doc.updated_at, doc.created_at, doc.document_name, col.collection_name, doc.data_object
                      FROM ' . $this->table . ' doc LEFT JOIN collections col ON doc.collection_id = col.id 
                      WHERE col.collection_name = :collection_name 
                      AND (col.read_per = "public" OR doc.created_by = :user_id )
                      LIMIT :start , :end ';

                //preparing statement
                $stmt = $this->conn->prepare($query);
                // Bind ID
                $stmt->bindParam(':collection_name', $collection_name);
                $stmt->bindParam(':user_id', $this->user_id);
                $stmt->bindValue(':start', (int) trim($start), PDO::PARAM_INT);
                $stmt->bindValue(':end', (int) trim($end-$start), PDO::PARAM_INT);
            }

            

             // executing and checking
            try{
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
                $document_array = array();

                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $document_item = array(
                        'dataObject' => json_decode($data_object),
                        'document_name' => $document_name,
                        'updatedAt' => $updated_at,
                        'createdAt' => $created_at              
                    );
            
                    // Push to "data"
                    array_push($document_array, $document_item);
                }
                // return to json
                http_response_code(200);
                return json_encode(
                    array(
                        'success'=>true,
                        'documents'=>$document_array
                    )
                );
            }else{
                // No document
                http_response_code(404);
                return json_encode(
                    array(
                        'success'=>false,
                        'message' => 'Collection not found.'
                    )
                );
            }
        }

        public function deletedocument($collection_name,$document_name){
            if(!strcmp($this->role, "admin")){
                $query = 'DELETE FROM ' . $this->table . ' 
                    WHERE document_name = :document_name 
                    AND 
                    collection_id IN (SELECT id FROM collections WHERE collection_name = :collection_name )';

                    //preparing statement
                    $stmt = $this->conn->prepare($query);
                    // Bind ID
                    $stmt->bindParam(':collection_name', $collection_name);
                    $stmt->bindParam(':document_name', $document_name);
            }else{
                $query = 'DELETE FROM ' . $this->table . ' 
                    WHERE document_name = :document_name
                    AND
                    created_by = :created_by 
                    AND 
                    collection_id IN (SELECT id FROM collections WHERE collection_name = :collection_name AND write_per = "private" )';

                //preparing statement
                $stmt = $this->conn->prepare($query);
                // Bind ID
                $stmt->bindParam(':collection_name', $collection_name);
                $stmt->bindParam(':document_name', $document_name);
                $stmt->bindParam(':created_by', $this->user_id);
            }

            // executing and checking
            try{
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
                http_response_code(200);
                return json_encode(
                    array(
                        'success'=>true,
                        'data'=>"document deleted."
                    )
                );
            }else{
                http_response_code(400);
                return json_encode(
                    array(
                        'success'=>false,
                        'data'=>"document not exists or permission denied."
                    )
                );
            }
        }

        public function getDocNames($collection_name,$start=0,$end=10){
            $query = 'SELECT doc.document_name
                      FROM ' . $this->table . ' doc LEFT JOIN collections col ON doc.collection_id = col.id 
                      WHERE col.collection_name = :collection_name LIMIT :start , :end ';
                      //preparing statement
                $stmt = $this->conn->prepare($query);
                // Bind ID
                $stmt->bindParam(':collection_name', $collection_name);
                $stmt->bindValue(':start', (int) trim($start), PDO::PARAM_INT);
                $stmt->bindValue(':end', (int) trim($end-$start), PDO::PARAM_INT);

             // executing and checking
            try{
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
                $document_array = array();

                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    
                    // Push to "data"
                    array_push($document_array, $document_name);
                }
                // return to json
                http_response_code(200);
                return json_encode(
                    array(
                        'success'=>true,
                        'documents'=>$document_array
                    )
                );
            }else{
                // No document
                http_response_code(404);
                return json_encode(
                    array(
                        'success'=>false,
                        'message' => 'Collection not found.'
                    )
                );
            }
        }
    }
?>
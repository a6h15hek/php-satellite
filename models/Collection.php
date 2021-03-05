<?php 
    /**
     * collections
     * ******************
     * id (primary key)
     * collection_name
     * created_at
     * access {
     *  read : false,
     *  write : false,
     *  delete : false,
     *  update : false
     * }
     */
    class Collection {
        // database variables
        private $conn = null;
        private $table = 'collections';

        //collection Properties
        private $id;
        private $collection_name;
        private $new_collection_name;
        private $created_at;

        public function __construct($db){
            $this->conn = $db;
        }

        //Get all collections
        public function get_collections(){
            // Creating query
            $query ='SELECT 
                        collection_name,
                        created_at
                    FROM '. $this->table ;
            
            //preparing statement
            $stmt = $this->conn->prepare($query);
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
                $collection_array = array();

                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $collection_item = array(
                        'collectionName' => $collection_name,
                        'createdAt' => $created_at              
                    );
            
                    // Push to "data"
                    array_push($collection_array, $collection_item);
                }
                // return to json
                http_response_code(200);
                return json_encode(
                    array(
                        'success'=>true,
                        'data'=>$collection_array
                    )
                );
            }else{
                // No document
                http_response_code(404);
                return json_encode(
                    array(
                        'success'=>false,
                        'message' => 'No collections.'
                    )
                );
            }
        }

        //get single collection
        public function get_single_collections($collection_name){
            // Creating query
            $query ='SELECT 
                        id,
                        collection_name
                    FROM ' . $this->table . '
                    WHERE 
                        collection_name = ?
                    LIMIT 0,1' ;
            
            //preparing statement
            $stmt = $this->conn->prepare($query);

            //Bind Collection Name
            $stmt->bindParam(1, $collection_name);
            
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
                $collection_arr = array(
                    'collection_id' => $row['id'],
                    'collection_name' => $row['collection_name']
                );
                http_response_code(200);
                return json_encode(
                    array(
                        'success'=>true,
                        'data'=>$collection_arr
                    )
                );
            } else{
                http_response_code(404);
                return json_encode(
                    array(
                        'success'=>false,
                        'message'=>'collection not found.'
                    )
                );
            }
        }

        //create Collection
        public function create($collection_name,$read="private",$write="private"){
            // Create query
            $query = 'INSERT INTO ' . $this->table . ' (collection_name, read_per, write_per) 
                     VALUES (:collection_name, :read_per, :write_per)';
            
            // Prepare statement
            $stmt = $this->conn->prepare($query);

            // Clean data
            $this->collection_name = htmlspecialchars(strip_tags($collection_name));

            // Bind data
            $stmt->bindParam(':collection_name', $this->collection_name);
            $stmt->bindParam(':read_per', $read);
            $stmt->bindParam(':write_per', $write);

            try{
                if($stmt->execute()) {
                    http_response_code(200);
                    return json_encode(
                        array(
                            'success'=>true,
                            'message' => 'Collection Created.'
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

        public function update($collection_name, $new_collection_name){
            // Create query
            $query = 'UPDATE ' . $this->table . '
                      SET collection_name = :new_collection_name 
                      WHERE collection_name = :collection_name ';
            
            // Prepare statement
            $stmt = $this->conn->prepare($query);

            // Clean data
            $this->collection_name = htmlspecialchars(strip_tags($collection_name));
            $this->new_collection_name = htmlspecialchars(strip_tags($new_collection_name));

            // Bind data
            $stmt->bindParam(':collection_name', $this->collection_name);
            $stmt->bindParam(':new_collection_name',$new_collection_name);

            try{
                if($stmt->execute()) {
                    http_response_code(200);
                    return json_encode(
                        array(
                            'success'=>true,
                            'message' => 'Collection name Updated'
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

        public function updatepermissions($collection_name, $read=NULL, $write=NULL){

            if(isset($read) && isset($write)){
                // Create query
                $query = 'UPDATE ' . $this->table . '
                    SET read_per = :read_per , write_per = :write_per
                    WHERE collection_name = :collection_name ';

                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':read_per',$read);
                $stmt->bindParam(':write_per',$write);
            } else if(isset($read)){
                 // Create query
                 $query = 'UPDATE ' . $this->table . '
                    SET read_per = :read_per 
                    WHERE collection_name = :collection_name ';

                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':read_per',$read);

            } else if(isset($write)){
                 // Create query
                 $query = 'UPDATE ' . $this->table . '
                    SET write_per = :write_per
                    WHERE collection_name = :collection_name ';

                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':write_per',$write);
            }
            
            // Bind data
            $stmt->bindParam(':collection_name', $collection_name);

            try{
                if($stmt->execute()) {
                    http_response_code(200);
                    return json_encode(
                        array(
                            'success'=>true,
                            'message' => 'Collection permission Updated'
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

        //delete collection ;
        public function delete($collection_name){
            // Create query
            // $query = 'DELETE FROM ' . $this->table . '
            //           WHERE collection_name = :collection_name ';
            $query = 'DELETE documents, collections FROM documents 
                    INNER JOIN collections ON documents.collection_id = collections.id 
                    WHERE collections.collection_name = :collection_name ';
            
            // Prepare statement
            $stmt = $this->conn->prepare($query);

            // Clean data
            // $this->collection_name = htmlspecialchars(strip_tags($collection_name));

            // Bind data
            $stmt->bindParam(':collection_name', $collection_name);

            try{
                if($stmt->execute()) {
                    http_response_code(200);
                    return json_encode(
                        array(
                            'success'=>true,
                            'message' => 'Collection deleted and related documents deleted.'
                        )
                    );
                }else{
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
?>
<?php

include '../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Database
{
    private $database  = "apijwtdb";
    private $server  = "localhost";
    private $password  = "";
    private $username  = "root";
    private $mysqli = "";
    private $result = array();
    private $connection = false;


   /*
    * connect using constructed method
   */
    public function __construct()
    {
       if(! $this->connection  ){
           $this->mysqli = new mysqli($this->server, $this->username, $this->password, $this->database);
           $this->connection = true;

           if ($this->mysqli->connect_error){
               array_push($this->result,
                   $this->mysqli_connection_error);
               return false;
           }
       }
       else{
           return true;
       }
    }


    /*
     * check authorization credential
     * */
    public function checkCredential(){
        try {
            $headers = getallheaders();
            $authorization = isset($headers["Authorization"]) !== null && isset($headers["Authorization"]);
            if($authorization){
                $jwt =  $headers["Authorization"];
                $secret_key = "RTG1234";
                $token =str_replace("Bearer ", "", $jwt);
                $decoded = JWT::decode($token , new Key($secret_key, 'HS256'));
                return true;
            }
            else{
                echo json_encode(
                    [
                        'status'=> 'failed',
                        'message'=>"token undefined"
                    ]
                );
            }

        }
        catch (Exception $e){
            echo json_encode(
                [
                    'status'=> 'failed',
                    'message'=>"Invalid token"
                ]
            );
            return false;
        }
    }


    /*
     * check existing table
    */
    public function tableExist($table){
       $sql = "SHOW TABLES FROM $this->database LIKE '{$table}'";
       $tableInDb = $this->mysqli->query($sql);
       if($tableInDb){
           if($tableInDb->num_rows == 1){
               return true;
           }
           else{
               array_push($this->result, $table. "Doesn't exist");
           }
       }
       else{
           return false;
        }
    }

    /*
     * INSERT statement
     * */
    public function insert($table, $params = array()){
        if($this->tableExist($table)){
           $columns = implode(',', array_keys($params));
           $values = implode("','", array_values($params));

           $sql = "INSERT INTO $table ($columns) VALUES('$values')";
           if($this->mysqli->query($sql)){
               array_push($this->result, true);
               return true;
           }
           else{
               array_push($this->result, false);
               return false;
           }
        }
        else{
            return false;
        }

    }


    /*
     * UPDATE statement
     * */
    public function update($table, $params=array(), $where=null){
        if ($this->tableExist($table)){
            foreach ($params as $key => $val){
                $args[] = "$key = '{$val}'";
            }
            $sql = "UPDTE $table SET ".implode(' ', args);
            if($this->mysqli->query($sql)){
                array_push($this->result , true);
                return true;
            }
            else{
                array_push($this->result, false);
                return false;
            }
        }
        else{
            return false;
        }
    }

    /*
     * DELETE statement
     * */
    public function delete($table, $where=null){
        if($this->tableExist($table)){
            $sql = "DELETE FROM $table";
            if($where !=null){
                $sql .= " WHERE $where";
            }
            if($this->mysqli->query($sql)){
                array_push($this->result, true);
                return true;
            }
            else{
                array_push($this->result, false);
                return false;
            }
        }
        else{
            return false;
        }
    }


    /*Get data select statement*/
    public function select($table, $row="*", $join =null, $where=null, $order= null, $limit=null){
        if($this->tableExist($table)){
            $sql = "SELECT $row FROM $table";
            if($join !=null){
                $sql .= " JOIN $join";
            }
            if($where != null){
                $sql .= " WHERE $where";
            }
            if($order != null){
                $sql .= " ORDER BY $order";
            }

            if ($limit != null){
                $sql .=" LIMIT $limit";
            }

            $query = $this->mysqli->query($sql);
            if($query->num_rows > 0){
                $this->result = $query->fetch_all(MYSQLI_ASSOC);
                return true;
            }
            else {
                return false;
            }
        }
    }
    public function getResult(){
        $val = $this->result;
        $this->result = array();
        return $val;
    }

    public function __destruct()
    {
       if($this->connection){
           if($this->mysqli->close()){
               $this->connection = false;
               return true;
           }
       }
    }

}


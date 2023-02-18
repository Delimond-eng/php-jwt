<?php

    header("Access-Control-Allow-Origin:*");
    header("Access-Control-Allow-Method:POST");
    header("Content-Type:application/json");

    include '../database/Database.php';

    $db = new Database();

   if($_SERVER['REQUEST_METHOD'] == "POST"){
        $data =json_decode(file_get_contents('php://input', true));
        $username =htmlentities( $data->user_name);
        $useremail =htmlentities( $data->user_email);
        $userpass =htmlentities(password_hash($data->user_pass, PASSWORD_DEFAULT));

        $db->select("users", "user_email", null, "user_email='{$useremail}'", null, null );
        $is_email = $db->getResult();

        if(isset($is_email[0]['user_email']) == $useremail){
            echo json_encode(
                ['status'=> 'failed', 'message'=>'User email already exist !' ]
            );
            return false;
        }
        else{
            if($db->checkCredential()){
                $params = [
                    "user_name" => $username,
                    "user_email" => $useremail,
                    "user_pass" => $userpass,
                    "user_create_At"=>date("Y-m-d")
                ];
                $db->insert("users", $params);
                $result = $db->getResult();
                if($result[0] == 1){
                    echo json_encode(
                        ['status'=> 'success', 'message'=>'User created succefuly' ]
                    );
                }
                else{
                    echo json_encode(
                        ['status'=> 'failed', 'message'=>'User not created' ]
                    );
                }
            }
        }
   }
   else{
       echo json_encode(
           ['status'=> 'failed', 'message'=>'Access Denied' ]
       );
   }
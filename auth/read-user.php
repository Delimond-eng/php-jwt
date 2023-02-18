<?php

    header("Access-Control-Allow-Origin:*");
    header("Access-Control-Allow-Method:POST");
    header("Content-Type:application/json");
    include '../database/Database.php';
    include '../vendor/autoload.php';
    use \Firebase\JWT\JWT;
    use Firebase\JWT\Key;


    if($_SERVER["REQUEST_METHOD"]== "GET"){
        try {
            $headers = getallheaders();
            $jwt =  $headers["Authorization"];
            $secret_key = "RTG1234";
            $decoded = JWT::decode(str_replace("Bearer ", "", $jwt) , new Key($secret_key, 'HS256'));
            echo json_encode(
                [
                    'status'=> 'success',
                    'user'=> $decoded->data
                ]
            );
        }
        catch (Exception $e){
            echo json_encode(
                [
                    'status'=> 'failed',
                    'message'=>$e->getMessage()
                ]
            );
        }

    }
    else{
        echo json_encode(
            ['status'=> 'failed', 'message'=>'Access Denied' ]
        );
    }
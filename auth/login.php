<?php

    header("Access-Control-Allow-Origin:*");
    header("Access-Control-Allow-Method:POST");
    header("Content-Type:application/json");
    include '../database/Database.php';
    include '../vendor/autoload.php';
    use \Firebase\JWT\JWT;

    /*Database instance*/
    $db = new Database();

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $data =json_decode(file_get_contents('php://input', true));
        $user_email = htmlentities($data->user_email);
        $user_pass = htmlentities($data->user_pass);

        $db->select("users", "*", null, "user_email='{$user_email}'", null, null);
        $rows= $db->getResult();

        if(empty($rows)){
            echo json_encode(
                ['status'=> 'failed', 'message'=>'Invalid user credential ' ]
            );
            return false;
        }

        foreach ($rows as $row){
            $id = $row['id'];
            $email = $row['user_email'];
            $name = $row['user_name'];
            if(!password_verify($user_pass, $row['user_pass'])){
                echo json_encode(
                    ['status'=> 'failed', 'message'=>'Invalid user credential ' ]
                );
                return false;
            }
            else{
                $payload = [
                    "iss"=>"localhost",
                    "aud"=>"localhost",
                    "exp"=>time() + 10000,

                    "data"=>[
                        "id"=>$id,
                        "user_name"=>$name,
                        "user_email"=>$email,

                    ]
                ];
                $secret_key = "RTG1234";
                $jwt = JWT::encode($payload, $secret_key, "HS256" );
                echo json_encode(
                    [
                        'status'=> 'success',
                        'message'=>'logged in successfully',
                        'token'=>$jwt
                    ]
                );
            }

        }
    }
    else{
        echo json_encode(
            ['status'=> 'failed', 'message'=>'Endpoint not found' ]
        );
    }


<?php

    header("Access-Control-Allow-Origin:*");
    header("Access-Control-Allow-Method:POST");
    header("Content-Type:application/json");

    include '../database/Database.php';

    $db = new Database();

if($_SERVER['REQUEST_METHOD']== "GET") {


    $db->select("users", "*", null, null, null, null);
    $data = $db->getResult();
    echo json_encode([
        "status"=>"success",
        "data" =>$data
    ]);
}
else{
    echo json_encode([
        "status"=>"failed",
        "message" =>"NOT FOUND endpoint"
    ]);
}
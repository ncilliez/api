<?php

    require_once("db_connect.php");
    $request_method = $_SERVER["REQUEST_METHOD"];

    switch ($request_method) {
        case 'GET':
            if (!empty($_GET["id"])) 
            {
                $id = intval($_GET["id"]); 
                getProducts($id);
            }else{
                getProducts();
            }
            break;
        
        default:
            header("HTTP/1.0 405 Method Not Allowed");
            break;
    }

    function getProducts($id = null){
        global $conn;
        if ($id === null) {
            $query = "SELECT * FROM produit";
        } else {
            $query = "SELECT * FROM produit WHERE id = " . $id;
        }
        $response = array();
        $result = mysqli_query($conn, $query);
        while($row = mysqli_fetch_array($result)){
            $response[] =  $row; 
        }
        header('Content-Type: application/json');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }

?>

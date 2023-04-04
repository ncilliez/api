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

        case 'POST':
            AddProduct();
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

    function AddProduct(){
        global $conn;
        $name = $_POST["name"];
        $description = $_POST["description"];
        $price = $_POST["price"];
        $category = $_POST["category"];
        $created = date('Y-m-d H:i:s');
        $modified = date('Y-m-d H:i:s');
    
        $query = "INSERT INTO produit(name, description, price, category_id, created, modified) 
        VALUES('".$name."', '".$description."', '".$price."', '".$category."', '".$created."', '".$modified."')";

        if(mysqli_query($conn, $query)) {   
            $response=array(
                'status'=> 1,
                'status_message'=>'Produit ajouté avec succès.'
            );
        } else {
            $response=array(
                'status'=> 0,
                'status_message'=>'ERREUR! '
            );
        }
    
        // Retournez la réponse en tant que JSON
        header('Content-Type: application/json');
        echo json_encode($response);
    }
    

?>

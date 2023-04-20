<?php

// header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
// header('Access-Control-Allow-Headers: Content-Type');

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

        case 'PUT':
            // Modifier un produit
            $id = intval($_GET["id"]);
            updateProduct($id);
        break;

        case 'DELETE':
            // Supprimer un produit
            $id = intval($_GET["id"]);
            deleteProduct($id);
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

    function updateProduct($id)
    {
        global $conn;
        $_PUT = array(); //tableau qui va contenir les données reçues
        parse_str(file_get_contents('php://input'), $_PUT);
        $name = $_PUT["name"];
        $description = $_PUT["description"];
        $price = $_PUT["price"];
        $category = $_PUT["category"];
        $modified = date('Y-m-d H:i:s');
        //construire la requête SQL
        $query="UPDATE produit SET name='".$name."', description='".$description."', price='".$price."', category_id='".$category."', modified='".$modified."' WHERE id=".$id;
        
        if(mysqli_query($conn, $query))
        {
            $response=array(
            'status' => 1,
            'status_message' =>'Produit mis a jour avec succes.'
            );
        }
        else
        {
            $response=array(
            'status' => 0,
            'status_message' =>'Echec de la mise a jour de produit. '. mysqli_error($conn)
            );
            
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function deleteProduct($id)
    {
        global $conn;
        $query = "DELETE FROM produit WHERE id=".$id;
        if(mysqli_query($conn, $query))
        {
            $response=array(
            'status' => 1,
            'status_message' =>'Produit supprime avec succes.'
            );
        }
        else
        {
            $response=array(
            'status' => 0,
            'status_message' =>'La suppression du produit a echoue. '. mysqli_error($conn)
            );
        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }
    
?>

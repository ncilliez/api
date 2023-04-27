<?php

    require_once("db_connect.php");
    $request_method = $_SERVER["REQUEST_METHOD"];

    switch ($request_method) {
        case 'GET':
            if (!empty($_GET["id"])) {
                $id = intval($_GET["id"]); 
                getCategories($id);
            } else {
                getCategories();
            }
              
        break;

        case 'POST':
            AddCategorie();
        break;

        // case 'PUT':
        //     // Modifier un produit
        //     $id = intval($_GET["id"]);
        //     updateProduct($id);
        // break;

        // case 'DELETE':
        //     // Supprimer un produit
        //     $id = intval($_GET["id"]);
        //     deleteProduct($id);
        // break;
        
        default:
            header("HTTP/1.0 405 Method Not Allowed");
        break;
    }

    function getCategories($id = null){
        $conn = getConnexion();
        if ($id === null) {
            $query = "SELECT * FROM categorie";
        } else {   
            $query = "SELECT * FROM categorie WHERE id = " . $id;
        }
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        sendJSON($response);
    }

    function addCategorie() {
        $conn = getConnexion();
        $libelle_categorie = $_POST["libelle_categorie"];
        
        $query = "INSERT INTO categorie(libelle_categorie) 
        VALUES('".$libelle_categorie."')";
        $stmt = $conn->prepare($query);
        
        $result = $stmt->execute();
        
        if($result) {   
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
        sendJSON($response);
    }
    
    // function updateProduct($id)
    // {
    //     $conn = getConnexion();
    //     $_PUT = array(); //tableau qui va contenir les données reçues
    //     parse_str(file_get_contents('php://input'), $_PUT);
    //     $name = $_PUT["name"];
    //     $description = $_PUT["description"];
    //     $price = $_PUT["price"];
    //     $image_produit = $_PUT["image_produit"];
    //     $category = $_PUT["category"];
    //     $modified = date('Y-m-d H:i:s');
    //     //construire la requête SQL
    //     $query="UPDATE produit SET name='".$name."', description='".$description."', price='".$price."',image_produit='".$image_produit."', category_id='".$category."', modified='".$modified."' WHERE id=".$id;
    //     $stmt = $conn->prepare($query);
        
    //     $result = $stmt->execute();
    //     if($result){
    //         $response=array(
    //             'status'=> 1,
    //             'status_message'=>'Produit mis a jour avec succès.'
    //         );
    //     } else {
    //         $response=array(
    //             'status'=> 0,
    //             'status_message'=>'ERREUR! '
    //         );
    //     }
        
    //     header('Content-Type: application/json');
    //     sendJSON($response);
    // }

    // function deleteProduct($id)
    // {
    //     $conn = getConnexion();
    //     $query = "DELETE FROM produit WHERE id=".$id;
    //     $stmt = $conn->prepare($query);
        
    //     $result = $stmt->execute();
    //     if($result){
    //         $response=array(
    //             'status'=> 1,
    //             'status_message'=>'Produit supprimé.'
    //         );
    //     } else {
    //         $response=array(
    //             'status'=> 0,
    //             'status_message'=>'ERREUR! '
    //         );
    //     }

    //     header('Content-Type: application/json');
    //     sendJSON($response);
    // }

    function sendJSON($result){
        header("Access-Control-Allow-origin: *");
        header("Content-Type: application/json; charset= UTF-8");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, X-Token-Auth, Authorization");
        header("Access-Control-Allow-Credentials: true");
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
         // echo json_encode($result, JSON_PRETTY_PRINT);
    }
    
?>
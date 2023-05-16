<?php

    require_once("db_connect.php");
    $request_method = $_SERVER["REQUEST_METHOD"];

    switch ($request_method) {
        case 'GET':
            if (!empty($_GET["id"])) {
                if (!is_numeric($_GET["id"])) {
                    $categ = $_GET["id"];
                    getProducts($categ);
                } else {
                    $id = intval($_GET["id"]); 
                    getProducts($id);
                }
            } else {
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

    function getProducts($id = null) {
        $conn = getConnexion();
        if(isset($_GET['categorie']) && isset($_GET['items']) && isset($_GET['page'])){
            $categorie = $_GET['categorie'];
            $items = $_GET['items'];
            $page = ($_GET['page']-1)*$items;
            $query = "SELECT p.id, p.name, p.description, p.price, p.image_produit, c.libelle_categorie as 'categorie' 
                      FROM produit p INNER JOIN categorie c ON p.category_id = c.id 
                      WHERE c.libelle_categorie = :categorie LIMIT :items OFFSET :page";
        } else if(isset($_GET['categorie'])){
            $categorie = $_GET['categorie'];
            $query = "SELECT p.id, p.name, p.description, p.price, p.image_produit, c.libelle_categorie as 'categorie' 
                      FROM produit p INNER JOIN categorie c ON p.category_id = c.id 
                      WHERE c.libelle_categorie = :categorie";
        } else if(isset($_GET['items']) && isset($_GET['page'])){
            $items = $_GET['items'];
            $page = ($_GET['page']-1)*$items;
            $query = "SELECT * FROM produit LIMIT :items OFFSET :page";
        } else if ($id === null) {
            $query = "SELECT * FROM produit";
        } else {
            $query = "SELECT * FROM produit WHERE id = :id";
        }
        // Utilisation de prepared statements pour éviter les attaques SQL Injection
        $stmt = $conn->prepare($query);
        if (isset($id)) {
            $stmt->bindParam(':id', $id);
        }
        if (isset($categorie)) {
            $stmt->bindParam(':categorie', $categorie);
        }
        if (isset($items)) {
            $stmt->bindParam(':items', $items, PDO::PARAM_INT);
        }
        if (isset($page)) {
            $stmt->bindParam(':page', $page, PDO::PARAM_INT);
        }
        $stmt->execute();
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        sendJSON($response);
    }
    
    function addProduct() {
        $conn = getConnexion();
    
        // Récupération des données
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $category = $_POST['category'];
    
        // Récupération de l'image
        $extension = pathinfo($_FILES['image_produit']['name'], PATHINFO_EXTENSION);
        $image_path = 'images/' . uniqid() . '.' .$extension; // Génère un nom de fichier unique
        $image_produit = 'http://192.168.1.10/api/' . $image_path;
        move_uploaded_file($_FILES['image_produit']['tmp_name'], $image_path);
    
        $query = "INSERT INTO produit (name, description, price, image_produit, category_id, created, modified) 
                  VALUES (:name, :description, :price, :image_produit, :category_id, :created, :modified)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':image_produit', $image_produit);
        $stmt->bindParam(':category_id', $category);
        $stmt->bindValue(':created', date('Y-m-d H:i:s'));
        $stmt->bindValue(':modified', date('Y-m-d H:i:s'));
    
        $result = $stmt->execute();
    
        if ($result) {   
            $response = array(
                'status' => 1,
                'status_message' => 'Produit ajouté avec succès.'
            );
        } else {
            $response = array(
                'status' => 0,
                'status_message' => 'ERREUR!'
            );
        }
    
        // Retournez la réponse en tant que JSON
        sendJSON($response);
    }
    
    
    function updateProduct($id)
    {
        $conn = getConnexion();
        $_PUT = array(); //tableau qui va contenir les données reçues
        parse_str(file_get_contents('php://input'), $_PUT);
        $name = $_PUT["name"];
        $description = $_PUT["description"];
        $price = $_PUT["price"];
        $image_produit = $_PUT["image_produit"];
        $category = $_PUT["category"];
        $modified = date('Y-m-d H:i:s');
        //construire la requête SQL
        $query="UPDATE produit SET name='".$name."', description='".$description."', price='".$price."',image_produit='".$image_produit."', category_id='".$category."', modified='".$modified."' WHERE id=".$id;
        $stmt = $conn->prepare($query);
        
        $result = $stmt->execute();
        if($result){
            $response=array(
                'status'=> 1,
                'status_message'=>'Produit mis a jour avec succès.'
            );
        } else {
            $response=array(
                'status'=> 0,
                'status_message'=>'ERREUR! '
            );
        }
        
        header('Content-Type: application/json');
        sendJSON($response);
    }

    function deleteProduct($id)
    {
        $conn = getConnexion();
        $query = "DELETE FROM produit WHERE id=".$id;
        $stmt = $conn->prepare($query);
        
        $result = $stmt->execute();
        if($result){
            $response=array(
                'status'=> 1,
                'status_message'=>'Produit supprimé.'
            );
        } else {
            $response=array(
                'status'=> 0,
                'status_message'=>'ERREUR! '
            );
        }

        header('Content-Type: application/json');
        sendJSON($response);
    }

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

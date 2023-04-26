<?php
    $url = "http://127.0.0.1/api/produits/5"; // modifier le produit 1
    $data = array('name' => 'PEC', 'description' => 'Pencil 2H', 'price' => 2.25,'image_produit' => 'http://localhost/api/images/cat.jpg', 'category' => '1');

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));

    $response = curl_exec($ch);

    var_dump($response);

    if (!$response) 
    {
        return false;
    }
?>
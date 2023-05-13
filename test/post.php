<?php

    $url = 'http://localhost/api/produits';

    // Récupération de l'image
    $image_path = 'cat.jpg';
    $image_data = file_get_contents($image_path);
    $encoded_image = base64_encode($image_data);

    // Paramètres de la requête
    $data = array(
        'name' => 'PEC',
        'description' => 'Pencil 2H',
        'price' => 2.25,
        'image_produit' => $encoded_image,
        'category' => '3'
    );

    // Options de la requête
    $options = array(
        'http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        )
    );

    // Envoi de la requête
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    var_dump($result);
?>
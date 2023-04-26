<?php

    $url = 'http://localhost/api/produits';
    $data = array('name' => 'PEC', 'description' => 'Pencil 2H', 'price' => 2.25,'image_produit' => 'http://localhost/api/images/cat.jpg', 'category' => '3');

    $options = array(
        'http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        )
    );

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    var_dump($result);

?>
<?php

    function getConnexion(){
        return new PDO("mysql:host=localhost;dbname=stock;charset=utf8","root","");
    }

?>

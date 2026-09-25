<?php

try {
    session_start();
    $bdd = new PDO('mysql:host=localhost;dbname=gestion_centre_de_formation;charset=utf8;', 'root', '1234');
} catch (Exception $e) {
    die('Une erreur a été trouvée : ' . $e->getMessage());
}

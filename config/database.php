<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    // Remplace 'ton_mot_de_passe_admin' par le mot de passe de ton utilisateur admin de BDD
    $bdd = new PDO('mysql:host=localhost; dbname=gestion_centre_de_formation; charset=utf8', 'admin', '1234' );
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Une erreur a été trouvée : ' . $e->getMessage());
}
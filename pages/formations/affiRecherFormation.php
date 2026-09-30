<?php

require('config/database.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['validate'])) {

    if (!empty($_POST['nom']) && !empty($_POST['description']) && !empty($_POST['duree']) && !empty($_POST['prix']) && !empty($_POST['statut'])) {

        $nom = htmlspecialchars($_POST['nom']);
        $description = htmlspecialchars($_POST['description']);
        $duree = htmlspecialchars($_POST['duree']);
        $prix = htmlspecialchars($_POST['prix']);
        
        // Valeurs d'audit par défaut
        $status = 1; // 1 = Actif
        $created_by = isset($_SESSION['id']) ? $_SESSION['id'] : 1; // ID de l'utilisateur connecté (ou 1 par défaut)
        $date_created = date('Y-m-d H:i');

        $insertFormation = $bdd->prepare('INSERT INTO formation(nom, description, duree, prix, status, date_created, created_by) VALUES(?, ?, ?, ?, ?, ?, ?)');
        $insertFormation->execute(array($nom, $description,$duree ,$prix, $status, $date_created , $created_by));
        $successMsg = "La formation a bien été ajoutée !";

    } else {
        $errorMsg = "Veuillez remplir tous les champs obligatoires (*)...";
    }
}

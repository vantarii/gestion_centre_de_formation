<?php

// Inclusion de la connexion BDD avec __DIR__
require_once __DIR__ . '/../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['validate'])) {

    if (!empty($_POST['titre']) && !empty($_POST['prix'])) {

        $nom = htmlspecialchars($_POST['titre']);
        $description = !empty($_POST['description']) ? htmlspecialchars($_POST['description']) : null;
        $duree = !empty($_POST['duree']) ? htmlspecialchars($_POST['duree']) : null;
        $prix = floatval($_POST['prix']);
        
        // Audit
        $statut = 1;
        $created_by = $_SESSION['id'] ?? 1;
        $date_created = date('Y-m-d H:i:s');

        try {
            $insertFormation = $bdd->prepare('INSERT INTO formation(nom, description, duree, prix, statut, date_created, created_by) VALUES(?, ?, ?, ?, ?, ?, ?)');
            $insertFormation->execute(array($nom, $description, $duree, $prix, $statut, $date_created, $created_by));

            $successMsg = "La formation a bien été ajoutée !";
        } catch (PDOException $e) {
            $errorMsg = "Erreur de base de données : " . $e->getMessage();
        }

    } else {
        $errorMsg = "Veuillez remplir les champs obligatoires (*)...";
    }
}

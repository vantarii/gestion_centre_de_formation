<?php
// Inclure la connexion BDD
require_once __DIR__ . '/../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si le formulaire a été soumis en POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupération et nettoyage des données
    $nom = !empty($_POST['nom']) ? trim(htmlspecialchars($_POST['nom'])) : null;
    $prenom = !empty($_POST['prenom']) ? trim(htmlspecialchars($_POST['prenom'])) : null;
    $email = !empty($_POST['email']) ? trim(htmlspecialchars($_POST['email'])) : null;
    $telephone = !empty($_POST['telephone']) ? trim(htmlspecialchars($_POST['telephone'])) : null;
    $specialite = !empty($_POST['specialite']) ? trim(htmlspecialchars($_POST['specialite'])) : null;

    // Vérification des champs obligatoires
    if ($nom && $prenom && $email && $telephone) {

        $status = 1;
        $created_by = $_SESSION['id'] ?? 1; // ID de l'utilisateur connecté ou 1 par défaut
        $date_created = date('Y-m-d H:i:s');

        try {
            // S'assurer que PDO lève des exceptions en cas d'erreur
            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Requête d'insertion dans la table formateur
            $insertFormateur = $bdd->prepare('
                INSERT INTO formateur (nom, prenom, email, telephone, specialite, status, date_created, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ');

            $insertFormateur->execute([
                $nom,
                $prenom,
                $email,
                $telephone,
                $specialite,
                $status,
                $date_created,
                $created_by
            ]);

            $successMsg = "La formatrice a bien été ajoutée !";

        } catch (PDOException $e) {
            $errorMsg = "Erreur SQL lors de l'insertion : " . $e->getMessage();
        }

    } else {
        $errorMsg = "Veuillez remplir tous les champs obligatoires (*).";
    }
}
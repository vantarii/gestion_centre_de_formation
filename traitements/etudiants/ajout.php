<?php

require_once __DIR__ . '/../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['validate'])) {

    if (!empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['telephone']) && !empty($_POST['email'])) {

        $nom = htmlspecialchars($_POST['nom']);
        $prenom = htmlspecialchars($_POST['prenom']);
        $telephone = htmlspecialchars($_POST['telephone']);
        $email = htmlspecialchars($_POST['email']);
        $date_naissance = !empty($_POST['date_naissance']) ? $_POST['date_naissance'] : null;
        $adresse = !empty($_POST['adresse']) ? htmlspecialchars($_POST['adresse']) : null;
        
        $status = 1;
        $created_by = $_SESSION['id'] ?? 1;
        $date_created = date('Y-m-d H:i:s');

        $insertEtudiant = $bdd->prepare('INSERT INTO etudiant(nom, prenom, telephone, email, date_naissance, adresse, status, date_created, created_by) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $insertEtudiant->execute(array($nom, $prenom, $telephone, $email, $date_naissance, $adresse, $status, $date_created, $created_by));

        $successMsg = "L'étudiante a bien été ajoutée !";

    } else {
        $errorMsg = "Veuillez remplir tous les champs obligatoires (*)...";
    }
}

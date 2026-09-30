<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Remonter de 2 niveaux depuis traitements/users/ vers config/database.php
require_once __DIR__ . '/../../config/database.php';

if (isset($_POST['validate'])) {

    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        
        $user_email = htmlspecialchars($_POST['email']);
        $user_password = $_POST['password'];

        $checkIfUserExists = $bdd->prepare('SELECT * FROM users WHERE email = ?');
        $checkIfUserExists->execute(array($user_email));

        if ($checkIfUserExists->rowCount() > 0) {
            
            $userInfos = $checkIfUserExists->fetch();

            if (password_verify($user_password, $userInfos['mdp'])) {

                $_SESSION['auth'] = true;
                $_SESSION['id'] = $userInfos['id'];
                $_SESSION['lastname'] = $userInfos['nom'];
                $_SESSION['firstname'] = $userInfos['prenom'];
                $_SESSION['email'] = $userInfos['email'];

                // Redirection vers la page d'ajout d'étudiant une fois connecté
                header('Location: /pages/etudiants/ajoutEtudiant.php');
                exit();

            } else {
                $errorMsg = "Votre mot de passe est incorrect...";
            }

        } else {
            $errorMsg = "Aucun compte trouvé avec cette adresse email...";
        }

    } else {
        $errorMsg = "Veuillez compléter tous les champs...";
    }
}

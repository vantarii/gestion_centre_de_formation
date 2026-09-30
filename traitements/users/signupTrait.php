<?php

require_once __DIR__ . '/../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['validate'])) {

    // 1. Vérification des champs reçus
    if (!empty($_POST['lastname']) && !empty($_POST['firstname']) && !empty($_POST['email']) && !empty($_POST['password'])) {

        $nom = htmlspecialchars(trim($_POST['lastname']));
        $prenom = htmlspecialchars(trim($_POST['firstname']));
        $email = htmlspecialchars(trim($_POST['email']));
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        try {
            // 2. Vérifier si l'email existe déjà
            $checkUser = $bdd->prepare('SELECT id FROM users WHERE email = ?');
            $checkUser->execute(array($email));

            if ($checkUser->rowCount() === 0) {

                // 3. Insertion du nouveau gérant
                $insertUser = $bdd->prepare('INSERT INTO users(nom, prenom, email, mdp) VALUES(?, ?, ?, ?)');
                $insertUser->execute(array($nom, $prenom, $email, $password));

                // 4. Confirmation et redirection
                $_SESSION['success_signup'] = "Votre compte gérant a bien été créé ! Connectez-vous.";
                header('Location: login.php');
                exit();

            } else {
                $errorMsg = "Un compte existe déjà avec cette adresse email.";
            }

        } catch (PDOException $e) {
            // Afficher l'erreur SQL exacte pour comprendre le blocage
            $errorMsg = "Erreur de base de données : " . $e->getMessage();
        }

    } else {
        $errorMsg = "Veuillez remplir tous les champs du formulaire.";
    }
}
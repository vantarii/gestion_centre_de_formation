<?php

require('config/database.php');

// Validation du formulaire
if (isset($_POST['validate'])) {

    // Vérifier si les champs obligatoires sont remplis
    if (!empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['telephone']) && !empty($_POST['email'])) {

        // Nettoyage des données saisies
        $nom = htmlspecialchars($_POST['nom']);
        $prenom = htmlspecialchars($_POST['prenom']);
        $telephone = htmlspecialchars($_POST['telephone']);
        $email = htmlspecialchars($_POST['email']);
        $date_naissance = !empty($_POST['date_naissance']) ? $_POST['date_naissance'] : null;
        $adresse = !empty($_POST['adresse']) ? htmlspecialchars($_POST['adresse']) : null;

        // Préparation de la requête d'insertion
        $insertEtudiant = $bdd->prepare('INSERT INTO etudiant(nom, prenom, telephone, email, date_naissance, adresse) VALUES(?, ?, ?, ?, ?, ?)');
        $insertEtudiant->execute(array($nom, $prenom, $telephone, $email, $date_naissance, $adresse));

        // Message de succès ou redirection vers la liste des étudiants
        $successMsg = "L'étudiant a bien été ajouté !";
        // header('Location: index.php'); // Optionnel : redirection

    } else {
        $errorMsg = "Veuillez remplir tous les champs obligatoires (*)...";
    }
}

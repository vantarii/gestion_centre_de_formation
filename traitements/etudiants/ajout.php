<?php
require_once __DIR__ . '/../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Génère une chaîne aléatoire sécurisée pour etudiant_uuid
 */
function generateRandomString($length = 6)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }

    return $randomString;
}

// Traitement lors de la soumission du formulaire d'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validate'])) {

    $nom = !empty($_POST['nom']) ? trim(htmlspecialchars($_POST['nom'])) : null;
    $prenom = !empty($_POST['prenom']) ? trim(htmlspecialchars($_POST['prenom'])) : null;
    $email = !empty($_POST['email']) ? trim(htmlspecialchars($_POST['email'])) : null;
    $dateNaissance = !empty($_POST['date_naissance']) ? $_POST['date_naissance'] : null;
    $telephone = !empty($_POST['telephone']) ? trim(htmlspecialchars($_POST['telephone'])) : null;
    $adresse = !empty($_POST['adresse']) ? trim(htmlspecialchars($_POST['adresse'])) : null;

    if ($nom && $prenom && $email && $telephone) {

        // 1. Vérification si l'email existe déjà parmi les étudiants actifs
        $checkEmail = $bdd->prepare('SELECT id FROM etudiant WHERE email = ? AND status = 1');
        $checkEmail->execute([$email]);

        if ($checkEmail->rowCount() > 0) {
            $errorMsg = "Un étudiant actif existe déjà avec cette adresse email.";
        } else {
            // 2. Génération d'un etudiant_uuid unique
            do {
                $etudiantUuid = generateRandomString(6);
                $checkUuid = $bdd->prepare('SELECT id FROM etudiant WHERE etudiant_uuid = ?');
                $checkUuid->execute([$etudiantUuid]);
            } while ($checkUuid->rowCount() > 0); // S'assure qu'il n'y a pas de collision

            $created_by = $_SESSION['id'] ?? 1;

            try {
                // 3. Insertion en base de données
                $insertEtudiant = $bdd->prepare('
                    INSERT INTO etudiant (etudiant_uuid, nom, prenom, email, date_naissance, telephone, adresse, status, date_created, created_by)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW(), ?)
                ');

                $insertEtudiant->execute([
                    $etudiantUuid,
                    $nom,
                    $prenom,
                    $email,
                    $dateNaissance,
                    $telephone,
                    $adresse,
                    $created_by
                ]);

                $successMsg = "L'étudiant " . htmlspecialchars($prenom . ' ' . $nom) . " a été ajouté avec succès !";

            } catch (PDOException $e) {
                $errorMsg = "Erreur lors de l'enregistrement : " . $e->getMessage();
            }
        }

    } else {
        $errorMsg = "Veuillez remplir tous les champs obligatoires (*).";
    }
}
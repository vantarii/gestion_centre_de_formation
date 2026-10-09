<?php
require_once __DIR__ . '/../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function generateRandomString($length = 6) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, strlen($characters) - 1)];
    }
    return $randomString;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validate'])) {
    $nom = !empty($_POST['nom']) ? trim(htmlspecialchars($_POST['nom'])) : null;
    $prenom = !empty($_POST['prenom']) ? trim(htmlspecialchars($_POST['prenom'])) : null;
    $email = !empty($_POST['email']) ? trim(htmlspecialchars($_POST['email'])) : null;
    $telephone = !empty($_POST['telephone']) ? trim(htmlspecialchars($_POST['telephone'])) : null;
    $specialite = !empty($_POST['specialite']) ? trim(htmlspecialchars($_POST['specialite'])) : null;

    if ($nom && $prenom && $email) {
        do {
            $formateurUuid = generateRandomString(6);
            $checkUuid = $bdd->prepare('SELECT id FROM formateur WHERE formateur_uuid = ?');
            $checkUuid->execute([$formateurUuid]);
        } while ($checkUuid->rowCount() > 0);

        try {
            $insert = $bdd->prepare('
                INSERT INTO formateur (formateur_uuid, nom, prenom, email, telephone, specialite, status)
                VALUES (?, ?, ?, ?, ?, ?, 1)
            ');
            $insert->execute([$formateurUuid, $nom, $prenom, $email, $telephone, $specialite]);

            $_SESSION['success_msg'] = "Formatrice ajoutée avec succès !";
            header('Location: ../../pages/formateur/index.php');
            exit();
        } catch (PDOException $e) {
            $_SESSION['error_msg'] = "Erreur : " . $e->getMessage();
        }
    }
}
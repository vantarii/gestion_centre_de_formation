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
    $titre = !empty($_POST['titre']) ? trim(htmlspecialchars($_POST['titre'])) : null;
    $description = !empty($_POST['description']) ? trim(htmlspecialchars($_POST['description'])) : null;
    $prix = !empty($_POST['prix']) ? floatval($_POST['prix']) : null;
    $duree = !empty($_POST['duree']) ? trim(htmlspecialchars($_POST['duree'])) : null;

    if ($titre && $prix) {
        do {
            $formationUuid = generateRandomString(6);
            $checkUuid = $bdd->prepare('SELECT id FROM formation WHERE formation_uuid = ?');
            $checkUuid->execute([$formationUuid]);
        } while ($checkUuid->rowCount() > 0);

        try {
            $insert = $bdd->prepare('
                INSERT INTO formation (formation_uuid, titre, description, prix, duree, status)
                VALUES (?, ?, ?, ?, ?, 1)
            ');
            $insert->execute([$formationUuid, $titre, $description, $prix, $duree]);

            $_SESSION['success_msg'] = "La formation a été ajoutée avec succès !";
            header('Location: ../../pages/formations/index.php');
            exit();
        } catch (PDOException $e) {
            $_SESSION['error_msg'] = "Erreur lors de la création : " . $e->getMessage();
        }
    } else {
        $_SESSION['error_msg'] = "Veuillez remplir les champs obligatoires.";
    }
}
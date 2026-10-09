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
    $etudiantId = !empty($_POST['etudiant_id']) ? intval($_POST['etudiant_id']) : null;
    $formationId = !empty($_POST['formation_id']) ? intval($_POST['formation_id']) : null;

    if ($etudiantId && $formationId) {
        // Génération UUID
        do {
            $inscriptionUuid = generateRandomString(6);
            $checkUuid = $bdd->prepare('SELECT id FROM inscription WHERE inscription_uuid = ?');
            $checkUuid->execute([$inscriptionUuid]);
        } while ($checkUuid->rowCount() > 0);

        try {
            $insert = $bdd->prepare('
                INSERT INTO inscription (inscription_uuid, etudiant_id, formation_id, date_inscription, status)
                VALUES (?, ?, ?, NOW(), 1)
            ');
            $insert->execute([$inscriptionUuid, $etudiantId, $formationId]);

            $_SESSION['success_msg'] = "Inscription validée avec succès !";
            header('Location: ../../pages/inscriptions/index.php');
            exit();
        } catch (PDOException $e) {
            $_SESSION['error_msg'] = "Erreur SQL : " . $e->getMessage();
        }
    }
}
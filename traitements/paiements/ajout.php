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
    $inscriptionId = !empty($_POST['inscription_id']) ? intval($_POST['inscription_id']) : null;
    $montant = !empty($_POST['montant']) ? floatval($_POST['montant']) : null;
    $modePaiement = !empty($_POST['mode_paiement']) ? trim(htmlspecialchars($_POST['mode_paiement'])) : null;
    $reference = !empty($_POST['reference']) ? trim(htmlspecialchars($_POST['reference'])) : null;
    $datePaiement = !empty($_POST['date_paiement']) ? $_POST['date_paiement'] : date('Y-m-d H:i:s');
    $statut = isset($_POST['statut']) ? intval($_POST['statut']) : 1;

    if ($inscriptionId && $montant && $reference) {
        do {
            $paiementUuid = generateRandomString(6);
            $checkUuid = $bdd->prepare('SELECT id FROM paiement WHERE paiement_uuid = ?');
            $checkUuid->execute([$paiementUuid]);
        } while ($checkUuid->rowCount() > 0);

        try {
            $insert = $bdd->prepare('
                INSERT INTO paiement (paiement_uuid, inscription_id, montant, date_paiement, mode_paiement, reference, statut)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ');
            $insert->execute([$paiementUuid, $inscriptionId, $montant, $datePaiement, $modePaiement, $reference, $statut]);

            $_SESSION['success_msg'] = "Paiement enregistré avec succès.";
            header('Location: ../../pages/paiements/index.php');
            exit();
        } catch (PDOException $e) {
            $_SESSION['error_msg'] = "Erreur SQL : " . $e->getMessage();
        }
    }
}
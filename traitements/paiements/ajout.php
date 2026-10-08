<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../users/security.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validate'])) {

    $inscriptionId = !empty($_POST['inscription_id']) ? intval($_POST['inscription_id']) : null;
    $montant = !empty($_POST['montant']) ? floatval($_POST['montant']) : null;
    $modePaiement = !empty($_POST['mode_paiement']) ? trim(htmlspecialchars($_POST['mode_paiement'])) : null;
    $reference = !empty($_POST['reference']) ? trim(htmlspecialchars($_POST['reference'])) : null;
    $datePaiement = !empty($_POST['date_paiement']) ? $_POST['date_paiement'] : date('Y-m-d H:i:s');
    $statut = isset($_POST['statut']) ? intval($_POST['statut']) : 1;

    if ($inscriptionId && $montant && $modePaiement && $reference) {
        try {
            $insertPaiement = $bdd->prepare('
                INSERT INTO paiement (inscription_id, montant, date_paiement, mode_paiement, reference, statut)
                VALUES (?, ?, ?, ?, ?, ?)
            ');
            $insertPaiement->execute([
                $inscriptionId,
                $montant,
                $datePaiement,
                $modePaiement,
                $reference,
                $statut
            ]);

            $_SESSION['success_msg'] = "Paiement de " . number_format($montant, 0, ',', ' ') . " FCFA enregistré avec succès !";
            header('Location: ../../pages/paiements/index.php');
            exit();

        } catch (PDOException $e) {
            $_SESSION['error_msg'] = "Erreur SQL lors de l'enregistrement : " . $e->getMessage();
            header('Location: ../../pages/paiements/ajoutPaiement.php');
            exit();
        }
    } else {
        $_SESSION['error_msg'] = "Veuillez remplir tous les champs obligatoires.";
        header('Location: ../../pages/paiements/ajoutPaiement.php');
        exit();
    }
} else {
    header('Location: ../../pages/paiements/index.php');
    exit();
}
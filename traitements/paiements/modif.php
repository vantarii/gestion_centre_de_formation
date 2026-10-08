<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../users/security.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validate'])) {

    $idPaiement = !empty($_POST['id_paiement']) ? intval($_POST['id_paiement']) : null;
    $inscriptionId = !empty($_POST['inscription_id']) ? intval($_POST['inscription_id']) : null;
    $montant = !empty($_POST['montant']) ? floatval($_POST['montant']) : null;
    $modePaiement = !empty($_POST['mode_paiement']) ? trim(htmlspecialchars($_POST['mode_paiement'])) : null;
    $reference = !empty($_POST['reference']) ? trim(htmlspecialchars($_POST['reference'])) : null;
    $datePaiement = !empty($_POST['date_paiement']) ? $_POST['date_paiement'] : date('Y-m-d H:i:s');
    $statut = !empty($_POST['statut']) ? trim(htmlspecialchars($_POST['statut'])) : 'Validé';

    if ($idPaiement && $inscriptionId && $montant && $modePaiement && $reference) {
        try {
            $updatePaiement = $bdd->prepare('
                UPDATE paiement 
                SET inscription_id = ?, montant = ?, date_paiement = ?, mode_paiement = ?, reference = ?, statut = ?
                WHERE id = ?
            ');
            $updatePaiement->execute([
                $inscriptionId,
                $montant,
                $datePaiement,
                $modePaiement,
                $reference,
                $statut,
                $idPaiement
            ]);

            $_SESSION['success_msg'] = "Le paiement a été mis à jour avec succès.";
            header('Location: ../../pages/paiement/index.php');
            exit();

        } catch (PDOException $e) {
            $_SESSION['error_msg'] = "Erreur SQL lors de la modification : " . $e->getMessage();
            header('Location: ../../pages/paiement/modifPaiement.php?id=' . $idPaiement);
            exit();
        }
    } else {
        $_SESSION['error_msg'] = "Tous les champs sont requis.";
        header('Location: ../../pages/paiement/modifPaiement.php?id=' . $idPaiement);
        exit();
    }
} else {
    header('Location: ../../pages/paiement/index.php');
    exit();
}
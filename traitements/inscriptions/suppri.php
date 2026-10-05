<?php
require_once __DIR__ . '/../../traitements/users/security.php';
require_once __DIR__ . '/../../config/database.php';

// Vérifier si l'ID est bien présent dans l'URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $idInscription = intval($_GET['id']);

    try {
        // 1. Vérifier si l'inscription existe et est encore active (status = 1)
        $checkInscription = $bdd->prepare('SELECT id FROM inscription WHERE id = ? AND status = 1');
        $checkInscription->execute([$idInscription]);

        if ($checkInscription->rowCount() > 0) {
            // 2. Annulation : mise à jour du statut à 0
            $cancelInscription = $bdd->prepare('UPDATE inscription SET status = 0 WHERE id = ?');
            $cancelInscription->execute([$idInscription]);

            $_SESSION['success_msg'] = "L'inscription a été annulée avec succès.";
        } else {
            $_SESSION['error_msg'] = "L'inscription demandée n'existe pas ou a déjà été annulée.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_msg'] = "Erreur lors de l'annulation : " . $e->getMessage();
    }
} else {
    $_SESSION['error_msg'] = "Identifiant d'inscription non spécifié.";
}

// Redirection vers la liste des inscriptions
header('Location: ../../pages/inscription/index.php');
exit();
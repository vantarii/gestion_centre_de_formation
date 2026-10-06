<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../traitements/users/security.php';
require_once __DIR__ . '/../../config/database.php';

// Vérifier si l'ID est bien présent dans l'URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $idInscription = intval($_GET['id']);

    try {
        // 1. Vérifier si l'inscription existe et est active
        $checkInscription = $bdd->prepare('SELECT id FROM inscription WHERE id = ? AND status = 1');
        $checkInscription->execute([$idInscription]);

        if ($checkInscription->rowCount() > 0) {
            // 2. Soft Delete : passage du statut à 0
            $softDelete = $bdd->prepare('UPDATE inscription SET status = 0 WHERE id = ?');
            $softDelete->execute([$idInscription]);

            $_SESSION['success_msg'] = "L'inscription a été annulée avec succès.";
        } else {
            $_SESSION['error_msg'] = "L'inscription demandée n'existe pas ou est déjà annulée.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_msg'] = "Erreur SQL : " . $e->getMessage();
    }
} else {
    $_SESSION['error_msg'] = "Identifiant d'inscription non spécifié.";
}

// Redirection vers l'index des inscriptions
header('Location: ../../pages/inscription/index.php');
exit();
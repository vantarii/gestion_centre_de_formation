<?php
require_once __DIR__ . '/../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validate'])) {
    $inscriptionUuid = !empty($_POST['inscription_uuid']) ? trim($_POST['inscription_uuid']) : null;
    $etudiantId = !empty($_POST['etudiant_id']) ? intval($_POST['etudiant_id']) : null;
    $formationId = !empty($_POST['formation_id']) ? intval($_POST['formation_id']) : null;

    if ($inscriptionUuid && $etudiantId && $formationId) {
        try {
            $update = $bdd->prepare('
                UPDATE inscription 
                SET etudiant_id = ?, formation_id = ?
                WHERE inscription_uuid = ? AND status = 1
            ');
            $update->execute([$etudiantId, $formationId, $inscriptionUuid]);

            $_SESSION['success_msg'] = "Inscription modifiée avec succès.";
            header('Location: ../../pages/inscriptions/index.php');
            exit();
        } catch (PDOException $e) {
            $_SESSION['error_msg'] = "Erreur lors de la modification : " . $e->getMessage();
        }
    }
}
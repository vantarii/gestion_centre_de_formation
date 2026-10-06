<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../users/security.php';
require_once __DIR__ . '/../../config/database.php';

// Vérifier que la requête a bien été soumise via le formulaire (méthode POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validate'])) {

    $idInscription = !empty($_POST['id_inscription']) ? intval($_POST['id_inscription']) : null;
    $etudiantId = !empty($_POST['etudiant_id']) ? intval($_POST['etudiant_id']) : null;
    $formationId = !empty($_POST['formation_id']) ? intval($_POST['formation_id']) : null;

    if ($idInscription && $etudiantId && $formationId) {

        try {
            // 1. Vérifier si l'inscription existe et est active
            $checkExists = $bdd->prepare('SELECT id FROM inscription WHERE id = ? AND status = 1');
            $checkExists->execute([$idInscription]);

            if ($checkExists->rowCount() === 0) {
                $_SESSION['error_msg'] = "L'inscription à modifier n'existe pas ou a été annulée.";
                header('Location: ../../pages/inscriptions/index.php');
                exit();
            }

            // 2. Vérifier si une AUTRE inscription active existe déjà pour ce même étudiant et cette même formation
            $checkDuplicate = $bdd->prepare('
                SELECT id FROM inscription 
                WHERE etudiant_id = ? AND formation_id = ? AND status = 1 AND id != ?
            ');
            $checkDuplicate->execute([$etudiantId, $formationId, $idInscription]);

            if ($checkDuplicate->rowCount() > 0) {
                $_SESSION['error_msg'] = "Cet étudiant est déjà inscrit à cette formation !";
                header('Location: ../../pages/inscriptions/modif.php?id=' . $idInscription);
                exit();
            }

            // 3. Exécuter la mise à jour
            $updateInscription = $bdd->prepare('
                UPDATE inscription 
                SET etudiant_id = ?, formation_id = ? 
                WHERE id = ? AND status = 1
            ');
            $updateInscription->execute([$etudiantId, $formationId, $idInscription]);

            $_SESSION['success_msg'] = "L'inscription a été modifiée avec succès !";
            header('Location: ../../pages/inscriptions/index.php');
            exit();

        } catch (PDOException $e) {
            $_SESSION['error_msg'] = "Erreur lors de la modification : " . $e->getMessage();
            header('Location: ../../pages/inscriptions/modif.php?id=' . $idInscription);
            exit();
        }

    } else {
        $_SESSION['error_msg'] = "Veuillez sélectionner un étudiant et une formation.";
        header('Location: ../../pages/inscriptions/modif.php?id=' . $idInscription);
        exit();
    }

} else {
    // Redirection directe si la page est appelée sans soumission POST
    header('Location: ../../pages/inscriptions/index.php');
    exit();
}
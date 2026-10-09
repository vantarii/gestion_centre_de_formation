<?php
require_once __DIR__ . '/../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Génère une chaîne aléatoire sécurisée pour inscription_uuid
 */
function generateRandomString($length = 6)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }

    return $randomString;
}

// 1. Récupération de l'identifiant (uuid prioritaire, id numérique en repli)
$paramIdentifiant = $_GET['uuid'] ?? $_GET['id'] ?? $_POST['inscription_uuid'] ?? $_POST['id_inscription'] ?? null;

if (!empty($paramIdentifiant)) {

    // Recherche de l'inscription active par inscription_uuid OU par id
    $getInscription = $bdd->prepare('SELECT * FROM inscription WHERE (inscription_uuid = ? OR id = ?) AND status = 1');
    $getInscription->execute([$paramIdentifiant, $paramIdentifiant]);

    if ($getInscription->rowCount() > 0) {
        $inscription = $getInscription->fetch(PDO::FETCH_ASSOC);
        $idInscription = $inscription['id'];

        // Si l'inscription n'a pas encore d'UUID enregistré, on lui en génère un automatiquement
        if (empty($inscription['inscription_uuid'])) {
            $newUuid = generateRandomString(6);
            $updateUuid = $bdd->prepare('UPDATE inscription SET inscription_uuid = ? WHERE id = ?');
            $updateUuid->execute([$newUuid, $idInscription]);
            $inscription['inscription_uuid'] = $newUuid;
        }

        $inscriptionUuid = $inscription['inscription_uuid'];

    } else {
        $_SESSION['error_msg'] = "L'inscription à modifier n'existe pas ou a été annulée.";
    }
} else {
    $_SESSION['error_msg'] = "Aucun identifiant transmis.";
}

// 2. Traitement de la modification lors de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validate'])) {

    $etudiantId = !empty($_POST['etudiant_id']) ? intval($_POST['etudiant_id']) : null;
    $formationId = !empty($_POST['formation_id']) ? intval($_POST['formation_id']) : null;

    if (isset($idInscription) && $etudiantId && $formationId) {

        try {
            // Vérifier si une AUTRE inscription active existe déjà pour ce même étudiant et cette même formation
            $checkDuplicate = $bdd->prepare('
                SELECT id FROM inscription 
                WHERE etudiant_id = ? AND formation_id = ? AND status = 1 AND id != ?
            ');
            $checkDuplicate->execute([$etudiantId, $formationId, $idInscription]);

            if ($checkDuplicate->rowCount() > 0) {
                $_SESSION['error_msg'] = "Cet étudiant est déjà inscrit à cette formation !";
                header('Location: ../../pages/inscriptions/modif.php?uuid=' . $inscriptionUuid);
                exit();
            }

            // Exécuter la mise à jour
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
            header('Location: ../../pages/inscriptions/modif.php?uuid=' . $inscriptionUuid);
            exit();
        }

    } else {
        $_SESSION['error_msg'] = "Veuillez sélectionner un étudiant et une formation.";
        header('Location: ../../pages/inscriptions/modif.php?uuid=' . ($inscriptionUuid ?? ''));
        exit();
    }
}
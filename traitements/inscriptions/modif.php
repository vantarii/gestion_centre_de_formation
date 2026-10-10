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

// Initialisation des listes pour les select
$etudiants = [];
$formations = [];

try {
    $etudiants = $bdd->query('SELECT id, nom, prenom FROM etudiant WHERE status = 1 ORDER BY nom ASC')->fetchAll(PDO::FETCH_ASSOC);
    $formations = $bdd->query('SELECT id, titre, prix FROM formation WHERE status = 1 ORDER BY titre ASC')->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorMsg = "Erreur lors du chargement des options : " . $e->getMessage();
}

// 1. Récupération de l'identifiant (uuid prioritaire, id numérique en repli)
$paramIdentifiant = $_GET['uuid'] ?? $_GET['id'] ?? null;

if (!empty($paramIdentifiant)) {

    // Recherche de l'inscription active
    $getInscription = $bdd->prepare('SELECT * FROM inscription WHERE (inscription_uuid = ? OR id = ?) AND status = 1');
    $getInscription->execute([$paramIdentifiant, $paramIdentifiant]);

    if ($getInscription->rowCount() > 0) {
        $inscriptionInfos = $getInscription->fetch(PDO::FETCH_ASSOC);
        $idInscription = $inscriptionInfos['id'];

        // Si l'inscription n'a pas d'UUID, on le génère immédiatement
        if (empty($inscriptionInfos['inscription_uuid'])) {
            $newUuid = generateRandomString(6);
            $updateUuid = $bdd->prepare('UPDATE inscription SET inscription_uuid = ? WHERE id = ?');
            $updateUuid->execute([$newUuid, $idInscription]);
            $inscriptionInfos['inscription_uuid'] = $newUuid;
        }

        $inscriptionUuid = $inscriptionInfos['inscription_uuid'];
        
        // Alias pour assurer la compatibilité si $inscription est utilisé dans la vue
        $inscription = $inscriptionInfos;

    } else {
        $errorMsg = "L'inscription à modifier n'existe pas ou a été annulée.";
    }
} else {
    $errorMsg = "Aucun identifiant transmis.";
}

// 2. Traitement du formulaire lors de la soumission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validate'])) {

    $etudiantId = !empty($_POST['etudiant_id']) ? intval($_POST['etudiant_id']) : null;
    $formationId = !empty($_POST['formation_id']) ? intval($_POST['formation_id']) : null;

    if (isset($idInscription) && $etudiantId && $formationId) {

        try {
            // Empêcher les doublons d'inscription
            $checkDuplicate = $bdd->prepare('
                SELECT id FROM inscription 
                WHERE etudiant_id = ? AND formation_id = ? AND status = 1 AND id != ?
            ');
            $checkDuplicate->execute([$etudiantId, $formationId, $idInscription]);

            if ($checkDuplicate->rowCount() > 0) {
                $errorMsg = "Cet étudiant est déjà inscrit à cette formation !";
            } else {
                $updateInscription = $bdd->prepare('
                    UPDATE inscription 
                    SET etudiant_id = ?, formation_id = ? 
                    WHERE id = ? AND status = 1
                ');
                $updateInscription->execute([$etudiantId, $formationId, $idInscription]);

                // Mise à jour des variables locales pour l'affichage
                $inscriptionInfos['etudiant_id'] = $etudiantId;
                $inscriptionInfos['formation_id'] = $formationId;
                $inscription = $inscriptionInfos;

                $_SESSION['success_msg'] = "L'inscription a été modifiée avec succès !";
                header('Location: index.php');
                exit();
            }

        } catch (PDOException $e) {
            $errorMsg = "Erreur SQL : " . $e->getMessage();
        }

    } else {
        $errorMsg = "Veuillez sélectionner un étudiant et une formation.";
    }
}
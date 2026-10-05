<?php
require_once __DIR__ . '/../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Récupérer les étudiants actifs
$getEtudiants = $bdd->query('SELECT id, nom, prenom FROM etudiant WHERE status = 1 ORDER BY nom ASC');
$etudiants = $getEtudiants->fetchAll(PDO::FETCH_ASSOC);

// 2. Récupérer les formations actives
$getFormations = $bdd->query('SELECT id, titre, prix FROM formation WHERE status = 1 ORDER BY titre ASC');
$formations = $getFormations->fetchAll(PDO::FETCH_ASSOC);

// 3. Traitement de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validate'])) {

    $etudiantId = !empty($_POST['etudiant_id']) ? intval($_POST['etudiant_id']) : (!empty($_POST['id_etudiant']) ? intval($_POST['id_etudiant']) : null);
    $formationId = !empty($_POST['formation_id']) ? intval($_POST['formation_id']) : (!empty($_POST['id_formation']) ? intval($_POST['id_formation']) : null);

    if ($etudiantId && $formationId) {

        // Vérification des doublons (étudiant déjà inscrit à cette formation)
        $checkInscription = $bdd->prepare('
            SELECT id FROM inscription 
            WHERE etudiant_id = ? AND formation_id = ? AND status = 1
        ');
        $checkInscription->execute([$etudiantId, $formationId]);

        if ($checkInscription->rowCount() > 0) {
            $errorMsg = "Cette étudiante est déjà inscrite à cette formation !";
        } else {
            try {
                $insertInscription = $bdd->prepare('
                    INSERT INTO inscription (etudiant_id, formation_id, date_inscription, status)
                    VALUES (?, ?, NOW(), 1)
                ');
                $insertInscription->execute([$etudiantId, $formationId]);

                $successMsg = "L'inscription a été enregistrée avec succès !";
            } catch (PDOException $e) {
                $errorMsg = "Erreur lors de l'enregistrement : " . $e->getMessage();
            }
        }

    } else {
        $errorMsg = "Veuillez sélectionner une étudiante et une formation.";
    }
}
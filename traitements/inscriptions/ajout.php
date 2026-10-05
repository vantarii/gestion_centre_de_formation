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

    $idEtudiant = !empty($_POST['id_etudiant']) ? intval($_POST['id_etudiant']) : null;
    $idFormation = !empty($_POST['id_formation']) ? intval($_POST['id_formation']) : null;

    if ($idEtudiant && $idFormation) {

        // Vérification des doublons (étudiant déjà inscrit à cette formation)
        $checkInscription = $bdd->prepare('
            SELECT id FROM inscription 
            WHERE id_etudiant = ? AND id_formation = ? AND status = 1
        ');
        $checkInscription->execute([$idEtudiant, $idFormation]);

        if ($checkInscription->rowCount() > 0) {
            $errorMsg = "Cet étudiant est déjà inscrit à cette formation !";
        } else {
            $created_by = $_SESSION['id'] ?? 1;

            try {
                $insertInscription = $bdd->prepare('
                    INSERT INTO inscription (id_etudiant, id_formation, date_inscription, status, date_created, created_by)
                    VALUES (?, ?, NOW(), 1, NOW(), ?)
                ');
                $insertInscription->execute([$idEtudiant, $idFormation, $created_by]);

                $successMsg = "L'inscription a été enregistrée avec succès !";
            } catch (PDOException $e) {
                $errorMsg = "Erreur lors de l'enregistrement : " . $e->getMessage();
            }
        }

    } else {
        $errorMsg = "Veuillez sélectionner un étudiant et une formation.";
    }
}
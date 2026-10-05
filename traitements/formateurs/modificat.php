<?php
require_once __DIR__ . '/../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Récupération des données de la formatrice active (status = 1)
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $idFormateur = $_GET['id'];

    $getFormateur = $bdd->prepare('SELECT * FROM formateur WHERE id = ? AND status = 1');
    $getFormateur->execute([$idFormateur]);

    if ($getFormateur->rowCount() > 0) {
        $formateurInfos = $getFormateur->fetch(PDO::FETCH_ASSOC);
        
        $nom = $formateurInfos['nom'];
        $prenom = $formateurInfos['prenom'];
        $email = $formateurInfos['email'];
        $telephone = $formateurInfos['telephone'];
        $specialite = $formateurInfos['specialite'];
    } else {
        $errorMsg = "Aucune formatrice active trouvée avec cet identifiant.";
    }
} else {
    $errorMsg = "Aucun identifiant transmis.";
}

// 2. Traitement de la modification lors de l'envoi du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validate'])) {

    $newNom = !empty($_POST['nom']) ? trim(htmlspecialchars($_POST['nom'])) : null;
    $newPrenom = !empty($_POST['prenom']) ? trim(htmlspecialchars($_POST['prenom'])) : null;
    $newEmail = !empty($_POST['email']) ? trim(htmlspecialchars($_POST['email'])) : null;
    $newTelephone = !empty($_POST['telephone']) ? trim(htmlspecialchars($_POST['telephone'])) : null;
    $newSpecialite = !empty($_POST['specialite']) ? trim(htmlspecialchars($_POST['specialite'])) : null;

    if ($newNom && $newPrenom && $newEmail && $newTelephone) {

        $updated_by = $_SESSION['id'] ?? 1;

        try {
            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $updateFormateur = $bdd->prepare('
                UPDATE formateur 
                SET nom = ?, prenom = ?, email = ?, telephone = ?, specialite = ?, date_updated = NOW(), updated_by = ?
                WHERE id = ? AND status = 1
            ');

            $updateFormateur->execute([
                $newNom,
                $newPrenom,
                $newEmail,
                $newTelephone,
                $newSpecialite,
                $updated_by,
                $idFormateur
            ]);

            // Mettre à jour les variables d'affichage du formulaire
            $nom = $newNom;
            $prenom = $newPrenom;
            $email = $newEmail;
            $telephone = $newTelephone;
            $specialite = $newSpecialite;

            $successMsg = "Les informations de la formatrice ont été mises à jour avec succès !";

        } catch (PDOException $e) {
            $errorMsg = "Erreur lors de la mise à jour : " . $e->getMessage();
        }

    } else {
        $errorMsg = "Veuillez remplir tous les champs obligatoires (*).";
    }
}

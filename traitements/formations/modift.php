<?php
require_once __DIR__ . '/../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validate'])) {
    $uuid = !empty($_POST['formation_uuid']) ? trim($_POST['formation_uuid']) : null;
    $titre = !empty($_POST['titre']) ? trim(htmlspecialchars($_POST['titre'])) : null;
    $description = !empty($_POST['description']) ? trim(htmlspecialchars($_POST['description'])) : null;
    $prix = !empty($_POST['prix']) ? floatval($_POST['prix']) : null;
    $duree = !empty($_POST['duree']) ? trim(htmlspecialchars($_POST['duree'])) : null;

    if ($uuid && $titre && $prix) {
        try {
            $update = $bdd->prepare('
                UPDATE formation 
                SET titre = ?, description = ?, prix = ?, duree = ?
                WHERE formation_uuid = ? AND status = 1
            ');
            $update->execute([$titre, $description, $prix, $duree, $uuid]);

            $_SESSION['success_msg'] = "Formation mise à jour avec succès !";
            header('Location: ../../pages/formations/index.php');
            exit();
        } catch (PDOException $e) {
            $_SESSION['error_msg'] = "Erreur SQL : " . $e->getMessage();
            header('Location: ../../pages/formations/modifFormation.php?uuid=' . $uuid);
            exit();
        }
    }
}
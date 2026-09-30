<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/loginTrait.php'; 
?>
<!DOCTYPE html>
<html lang="fr">
<?php include __DIR__ . '/../../includes/head.php'; ?>
<body>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <h2 class="mb-4 text-center">Connexion</h2>

                <?php if (isset($errorMsg)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= $errorMsg; ?>
                    </div>
                <?php endif; ?>
                <!-- Affichage du message de succès d'inscription -->
                <?php if (isset($_SESSION['success_signup'])): ?>
                    <div class="alert alert-success" role="alert">
                    <?= $_SESSION['success_signup']; ?>
                    </div>
                <?php unset($_SESSION['success_signup']); // Effacer le message après affichage ?>
                <?php endif; ?>

                <form method="POST">
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3" name="validate">
                        Se connecter
                    </button>

                    <div class="text-center">
                        <a href="signup.php" class="text-decoration-none">
                            Je n'ai pas de compte, je m'inscris
                        </a>
                    </div>
                    
                </form>

            </div>
        </div>
    </div>

</body>
</html>
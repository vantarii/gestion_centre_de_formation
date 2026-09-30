<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
    <a class="navbar-brand" href="/pages/etudiants/index.php">Centre de Formation</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
        <li class="nav-item">
            <a class="nav-link" href="/pages/etudiants/index.php">Étudiants</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/pages/formations/index.php">Formations</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/pages/formateur/index.php">Formateurs</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/pages/inscriptions/index.php">Inscriptions</a>
        </li>
        </ul>
        <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['auth']) && $_SESSION['auth'] === true): ?>
            <li class="nav-item">
            <span class="nav-link text-light">Bonjour, <?= htmlspecialchars($_SESSION['firstname'] ?? 'Gérant'); ?></span>
            </li>
        <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<?php
if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$role = $_SESSION['user']['role'];
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?= $role === 'admin' ? 'dashboard.php' : 'nouvelle.php' ?>">
            💊 Pharmacie
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav me-auto">

                <?php if ($role === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="liste.php">Médicaments</a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link" href="nouvelle.php">Ventes</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="historique.php">Historique</a>
                </li>
            </ul>

            <span class="text-white me-3">
                <?= htmlspecialchars($_SESSION['user']['nom']) ?>
                <small class="badge bg-light text-success ms-2">
                    <?= strtoupper($role) ?>
                </small>
            </span>

            <a href="logout.php" class="btn btn-light btn-sm">Déconnexion</a>
        </div>
    </div>
</nav>
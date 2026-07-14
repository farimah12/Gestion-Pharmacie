<?php
session_start();
require_once "db.php";


if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

// Stock faible (<= 10)
$stockFaible = $pdo->query(
    "SELECT nom, quantite FROM medicaments WHERE quantite <= 10"
)->fetchAll();

// Médicaments périmés
$perimes = $pdo->query(
    "SELECT nom, date_peremption 
     FROM medicaments 
     WHERE date_peremption < CURDATE()"
)->fetchAll();

// Médicaments bientôt périmés (30 jours)
$bientot = $pdo->query(
    "SELECT nom, date_peremption 
     FROM medicaments 
     WHERE date_peremption BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)"
)->fetchAll();

// Ventes du jour
$ventesJour = $pdo->query(
    "SELECT IFNULL(SUM(total),0) AS total 
     FROM ventes 
     WHERE DATE(date_vente) = CURDATE()"
)->fetch()['total'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Pharmacie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="container mt-4">

    <h4 class="mb-4">Bienvenue, <?= $_SESSION['user']['nom'] ?> 👋</h4>

    <!-- Statistiques -->
    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h6>💰 Ventes du jour</h6>
                    <h3><?= number_format($ventesJour, 0, ',', ' ') ?> FCFA</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-warning mb-3">
                <div class="card-body">
                    <h6>⚠️ Stock faible</h6>
                    <h3><?= count($stockFaible) ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-danger text-white mb-3">
                <div class="card-body">
                    <h6>⛔ Produits périmés</h6>
                    <h3><?= count($perimes) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes -->
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-warning">Stock faible</div>
                <ul class="list-group list-group-flush">
                    <?php foreach ($stockFaible as $p): ?>
                        <li class="list-group-item">
                            <?= $p['nom'] ?> — <?= $p['quantite'] ?> unités
                        </li>
                    <?php endforeach; ?>
                    <?php if (count($stockFaible) == 0): ?>
                        <li class="list-group-item text-success">Aucun problème 👍</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-danger text-white">Produits périmés</div>
                <ul class="list-group list-group-flush">
                    <?php foreach ($perimes as $p): ?>
                        <li class="list-group-item text-danger">
                            <?= $p['nom'] ?> — périmé le <?= $p['date_peremption'] ?>
                        </li>
                    <?php endforeach; ?>
                    <?php if (count($perimes) == 0): ?>
                        <li class="list-group-item text-success">Aucun produit périmé 👍</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Bientôt périmés -->
    <div class="card mt-3">
        <div class="card-header bg-info text-white">🕒 Bientôt périmés (30 jours)</div>
        <ul class="list-group list-group-flush">
            <?php foreach ($bientot as $p): ?>
                <li class="list-group-item">
                    <?= $p['nom'] ?> — <?= $p['date_peremption'] ?>
                </li>
            <?php endforeach; ?>
            <?php if (count($bientot) == 0): ?>
                <li class="list-group-item text-success">Rien à signaler 👍</li>
            <?php endif; ?>
        </ul>
    </div>

</div>
<?php include "footer.php"; ?>
</body>
</html>
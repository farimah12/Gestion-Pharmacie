<?php
session_start();
require_once "db.php";
require_once "auth.php";

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

// Récupérer toutes les ventes
$ventes = $pdo->query("SELECT * FROM ventes ORDER BY date_vente DESC")->fetchAll();

// Rôle utilisateur
$role = $_SESSION['user']['role'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des ventes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="container mt-4">
    <h4>📋 Historique des ventes</h4>

    <table class="table table-bordered table-hover">
        <thead class="table-success">
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Total</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($ventes as $v): ?>
            <tr>
                <td><?= $v['id'] ?></td>
                <td><?= $v['date_vente'] ?></td>
                <td><?= $v['total'] ?> FCFA</td>
                <td>
                    <a href="facture.php?id=<?= $v['id'] ?>" class="btn btn-sm btn-primary">
                        Voir facture
                    </a>

                    <?php if ($role === 'admin'): ?>
                        <a href="supprimerVente.php?id=<?= $v['id'] ?>" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Voulez-vous vraiment supprimer cette vente ?')">
                           Supprimer
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>

        <?php if (count($ventes) == 0): ?>
            <tr>
                <td colspan="4" class="text-center">Aucune vente</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include "footer.php"; ?>
</body>
</html>
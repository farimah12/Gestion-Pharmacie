<?php
session_start();
require_once "db.php";
require_once "auth.php";

// Vérification connexion
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

// 🔐 Vérification rôle ADMIN
if ($_SESSION['user']['role'] !== 'admin') {
    header("Location: nouvelle.php");
    exit;
}

$medicaments = $pdo->query(
    "SELECT m.*, c.nom AS categorie
     FROM medicaments m
     LEFT JOIN categories c ON m.categorie_id = c.id
     ORDER BY m.nom"
)->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Médicaments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between mb-3">
        <h4>💊 Médicaments</h4>
        <a href="ajouter.php" class="btn btn-success">+ Ajouter</a>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="table-success">
            <tr>
                <th>Code</th>
                <th>Nom</th>
                <th>Catégorie</th>
                <th>Prix</th>
                <th>Stock</th>
                <th>Péremption</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($medicaments as $m): ?>
            <?php
                $perime = $m['date_peremption'] < date('Y-m-d');
                $stockFaible = $m['quantite'] <= 10;
            ?>
            <tr class="<?= $perime ? 'table-danger' : ($stockFaible ? 'table-warning' : '') ?>">
                <td><?= $m['code'] ?></td>
                <td><?= $m['nom'] ?></td>
                <td><?= $m['categorie'] ?></td>
                <td><?= $m['prix'] ?> FCFA</td>
                <td><?= $m['quantite'] ?></td>
                <td><?= $m['date_peremption'] ?></td>
                <td>
                    <a href="modifier.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-primary">Modifier</a>
                    <a href="supprimer.php?id=<?= $m['id'] ?>" 
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Supprimer ce médicament ?')">
                       Supprimer
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>

        <?php if (count($medicaments) == 0): ?>
            <tr>
                <td colspan="7" class="text-center">Aucun médicament</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include "footer.php"; ?>
</body>
</html>
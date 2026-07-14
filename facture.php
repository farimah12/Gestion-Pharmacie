<?php
session_start();
require_once "db.php";
require_once "auth.php";

if (!isset($_SESSION['user']) || !isset($_GET['id'])) {
    header("Location: historique.php");
    exit;
}

$id = intval($_GET['id']);

$vente = $pdo->prepare("SELECT * FROM ventes WHERE id=?");
$vente->execute([$id]);
$vente = $vente->fetch();

$details = $pdo->prepare(
    "SELECT d.*, m.nom 
     FROM details_vente d 
     JOIN medicaments m ON d.medicament_id = m.id
     WHERE d.vente_id=?"
);
$details->execute([$id]);
$details = $details->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <div class="card p-4 shadow-sm">
        <h4 class="text-center mb-4">💊 Facture Pharmacie</h4>

        <p>Date : <?= $vente['date_vente'] ?></p>

        <table class="table table-bordered">
            <thead class="table-success">
                <tr>
                    <th>Médicament</th>
                    <th>Quantité</th>
                    <th>Prix total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($details as $d): ?>
                    <tr>
                        <td><?= $d['nom'] ?></td>
                        <td><?= $d['quantite'] ?></td>
                        <td><?= $d['prix'] ?> FCFA</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h5 class="text-end">Total : <?= $vente['total'] ?> FCFA</h5>
        <button class="btn btn-success" onclick="window.print()">Imprimer</button>
        <a href="nouvelle.php" class="btn btn-secondary">Nouvelle vente</a>
    </div>
</div>
<?php include "footer.php"; ?>
</body>
</html>
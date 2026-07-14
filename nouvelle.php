<?php
session_start();
require_once "db.php";
require_once "auth.php";

// Vérification connexion
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$erreur = "";
$success = "";

// Traitement du formulaire de vente
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ids       = $_POST['medicament_id'] ?? [];
    $quantites = $_POST['quantite'] ?? [];

    // Supprimer les entrées où quantité = 0
    $ids = array_filter($ids, fn($i) => $quantites[$i] > 0, ARRAY_FILTER_USE_KEY);
    $quantites = array_filter($quantites, fn($q) => $q > 0);

    if (empty($ids)) {
        $erreur = "Veuillez sélectionner au moins un médicament";
    } else {
        $total = 0;
        $medicaments_selectionnes = [];

        foreach ($ids as $i => $id) {
            $stmt = $pdo->prepare("SELECT id, nom, prix, quantite FROM medicaments WHERE id=?");
            $stmt->execute([$id]);
            $med = $stmt->fetch();

            if (!$med) {
                $erreur = "Médicament introuvable";
                break;
            }

            if ($med['quantite'] <= 0) {
                $erreur = "Le médicament " . $med['nom'] . " est épuisé";
                break;
            }

            if ($quantites[$i] > $med['quantite']) {
                $erreur = "Quantité insuffisante pour " . $med['nom'];
                break;
            }

            $total += $med['prix'] * $quantites[$i];
            $medicaments_selectionnes[$id] = $med;
        }

        if (!$erreur) {
            $stmt = $pdo->prepare("INSERT INTO ventes (date_vente, total) VALUES (NOW(), ?)");
            $stmt->execute([$total]);
            $vente_id = $pdo->lastInsertId();

            foreach ($ids as $i => $id) {
                $med = $medicaments_selectionnes[$id];
                $stmt = $pdo->prepare(
                    "INSERT INTO details_vente (vente_id, medicament_id, quantite, prix)
                     VALUES (?, ?, ?, ?)"
                );
                $stmt->execute([$vente_id, $id, $quantites[$i], $quantites[$i]*$med['prix']]);

                // Mise à jour stock
                $stmt = $pdo->prepare("UPDATE medicaments SET quantite = quantite - ? WHERE id=?");
                $stmt->execute([$quantites[$i], $id]);
            }

            $success = "Vente enregistrée avec succès";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle vente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="container mt-4">
    <h4>🛒 Nouvelle vente</h4>

    <!-- Formulaire de recherche -->
    <input type="text" id="recherche" class="form-control mb-3" placeholder="Rechercher un médicament...">

    <?php if ($erreur): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?> — 
            <a href="facture.php?id=<?= $vente_id ?>">Voir facture</a>
        </div>
    <?php endif; ?>

    <form method="post" class="card p-3 shadow-sm">
        <table class="table table-bordered">
            <thead class="table-success">
                <tr>
                    <th>Médicament</th>
                    <th>Prix</th>
                    <th>Stock</th>
                    <th>Quantité</th>
                </tr>
            </thead>
            <tbody id="tableau-medicaments">
                <!-- Les médicaments seront chargés ici via AJAX -->
            </tbody>
        </table>

        <button class="btn btn-success">Enregistrer la vente</button>
        <a href="historique.php" class="btn btn-secondary">Historique</a>
    </form>
</div>

<script>
function chargerMedicaments(query = '') {
    fetch('rechercheMedocVente.php?recherche=' + encodeURIComponent(query))
        .then(response => response.text())
        .then(html => {
            document.getElementById('tableau-medicaments').innerHTML = html;
        });
}

// Chargement initial
chargerMedicaments();

// Mise à jour au fur et à mesure que l'utilisateur tape
document.getElementById('recherche').addEventListener('input', function() {
    chargerMedicaments(this.value);
});
</script>
<?php include "footer.php"; ?>
</body>
</html>
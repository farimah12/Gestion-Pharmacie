<?php
session_start();
require_once "db.php";
require_once "auth.php";

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$erreur = "";
$success = "";

if (!isset($_GET['id'])) {
    header("Location: liste.php");
    exit;
}

$id = intval($_GET['id']);
$med = $pdo->prepare("SELECT * FROM medicaments WHERE id = ?");
$med->execute([$id]);
$medicament = $med->fetch();

if (!$medicament) {
    header("Location: liste.php");
    exit;
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $code  = strtoupper(trim($_POST['code']));
    $nom   = trim($_POST['nom']);
    $cat   = $_POST['categorie'];
    $prix  = $_POST['prix'];
    $qte   = $_POST['quantite'];
    $date  = $_POST['date_peremption'];

    if (!preg_match('/^[A-Z0-9]{5}$/', $code)) {
        $erreur = "Code médicament invalide (5 caractères)";
    } elseif ($date <= date('Y-m-d')) {
        $erreur = "Date de péremption invalide";
    } else {
        $stmt = $pdo->prepare(
            "UPDATE medicaments 
             SET code=?, nom=?, categorie_id=?, prix=?, quantite=?, date_peremption=? 
             WHERE id=?"
        );
        $stmt->execute([$code, $nom, $cat, $prix, $qte, $date, $id]);
        $success = "Médicament mis à jour avec succès";
        // actualiser les données
        $medicament = $pdo->prepare("SELECT * FROM medicaments WHERE id = ?");
        $medicament->execute([$id]);
        $medicament = $medicament->fetch();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier médicament</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="container mt-4">
    <h4>✏️ Modifier le médicament</h4>

    <?php if ($erreur): ?>
        <div class="alert alert-danger"><?= $erreur ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="post" class="card p-3 shadow-sm">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label>Code</label>
                <input type="text" name="code" maxlength="5" class="form-control" required value="<?= $medicament['code'] ?>">
            </div>

            <div class="col-md-8 mb-3">
                <label>Nom</label>
                <input type="text" name="nom" class="form-control" required value="<?= $medicament['nom'] ?>">
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label>Catégorie</label>
                <select name="categorie" class="form-select" required>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $medicament['categorie_id']==$c['id'] ? 'selected' : '' ?>>
                            <?= $c['nom'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label>Prix</label>
                <input type="number" name="prix" class="form-control" required value="<?= $medicament['prix'] ?>">
            </div>

            <div class="col-md-4 mb-3">
                <label>Quantité</label>
                <input type="number" name="quantite" class="form-control" required value="<?= $medicament['quantite'] ?>">
            </div>
        </div>

        <div class="mb-3">
            <label>Date de péremption</label>
            <input type="date" name="date_peremption" class="form-control" required value="<?= $medicament['date_peremption'] ?>">
        </div>

        <button class="btn btn-success">Mettre à jour</button>
        <a href="liste.php" class="btn btn-secondary">Retour</a>
    </form>
</div>
<?php include "footer.php"; ?>
</body>
</html>
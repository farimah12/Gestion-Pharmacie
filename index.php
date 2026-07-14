<?php
session_start();
require_once "db.php";

// Si déjà connecté → redirection selon rôle
if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['role'] === 'admin') {
        header("Location: dashboard.php");
    } elseif ($_SESSION['user']['role'] === 'pharmacien') {
        header("Location: liste.php");
    } else {
        header("Location: nouvelle.php");
    }
    exit;
}

$erreur = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $login = strtoupper(trim($_POST['login']));
    $mdp   = $_POST['motdepasse'];
    $role  = $_POST['role'] ?? '';

    // Validation login : 5 lettres MAJUSCULES
    if (!preg_match('/^[A-Z]{5}$/', $login)) {
        $erreur = "Login invalide (5 lettres majuscules)";
    }
    elseif (!in_array($role, ['admin','pharmacien','caissier'])) {
        $erreur = "Rôle invalide";
    }
    else {

        $stmt = $pdo->prepare(
            "SELECT id, nom, role, motdepasse
             FROM utilisateurs
             WHERE login = ? AND role = ?"
        );
        $stmt->execute([$login, $role]);
        $user = $stmt->fetch();

        if ($user && password_verify($mdp, $user['motdepasse'])) {

            // Création session
            $_SESSION['user'] = [
                'id'   => $user['id'],
                'nom'  => $user['nom'],
                'role' => $user['role']
            ];

            // Redirection selon rôle
            if ($user['role'] === 'admin') {
                header("Location: dashboard.php");
            }
            elseif ($user['role'] === 'pharmacien') {
                header("Location: liste.php");
            }
            else {
                header("Location: nouvelle.php");
            }
            exit;

        } else {
            $erreur = "Login, rôle ou mot de passe incorrect";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion | Pharmacie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height:100vh;">

<div class="card shadow" style="width:380px;">
    <div class="card-body">
        <h4 class="text-center text-success mb-4">💊 Connexion Pharmacie</h4>

        <?php if ($erreur): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <form method="post">

            <div class="mb-3">
                <label class="form-label">Login</label>
                <input type="text" name="login" class="form-control" maxlength="5" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="motdepasse" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Rôle</label>
                <select name="role" class="form-select" required>
                    <option value="">-- Sélectionner un rôle --</option>
                    <option value="admin">Administrateur</option>
                    <option value="pharmacien">Pharmacien</option>
                    <option value="caissier">Caissier</option>
                </select>
            </div>

            <button class="btn btn-success w-100">Se connecter</button>

        </form>
    </div>
</div>

</body>
</html>
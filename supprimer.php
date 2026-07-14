<?php
session_start();
require_once "db.php";
require_once "auth.php";
// 🔐 Vérification connexion
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

// 🔐 Vérification rôle ADMIN
if ($_SESSION['user']['role'] !== 'admin') {
    header("Location: liste.php");
    exit;
}

// 🔐 Vérification ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: liste.php");
    exit;
}

$id = (int) $_GET['id'];

// 🗑️ Suppression sécurisée
$stmt = $pdo->prepare("DELETE FROM medicaments WHERE id = ?");
$stmt->execute([$id]);

header("Location: liste.php");
exit;
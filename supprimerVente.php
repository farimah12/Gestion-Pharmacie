<?php
session_start();
require_once "db.php";
require_once "auth.php";

// Vérification connexion
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

// 🔐 Vérification du rôle (ADMIN uniquement)
if ($_SESSION['user']['role'] !== 'admin') {
    header("Location: historique.php?error=acces_refuse");
    exit;
}

// Vérifier l'ID de la vente
$vente_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($vente_id <= 0) {
    header("Location: historique.php");
    exit;
}

// Récupérer les détails de la vente
$stmt = $pdo->prepare("SELECT medicament_id, quantite FROM details_vente WHERE vente_id = ?");
$stmt->execute([$vente_id]);
$details = $stmt->fetchAll();

// Remettre le stock à jour
foreach ($details as $d) {
    $stmt = $pdo->prepare("
        UPDATE medicaments 
        SET quantite = quantite + :qte 
        WHERE id = :med
    ");
    $stmt->execute([
        'qte' => $d['quantite'],
        'med' => $d['medicament_id']
    ]);
}

// Supprimer les détails de la vente
$stmt = $pdo->prepare("DELETE FROM details_vente WHERE vente_id = ?");
$stmt->execute([$vente_id]);

// Supprimer la vente
$stmt = $pdo->prepare("DELETE FROM ventes WHERE id = ?");
$stmt->execute([$vente_id]);

header("Location: historique.php?success=vente_supprimee");
exit;
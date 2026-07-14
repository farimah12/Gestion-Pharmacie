<?php
/*
require_once "db.php";

// Comptes à créer
$utilisateurs = [
    [
        "nom" => "Administrateur",
        "login" => "ADMIN",
        "motdepasse" => "12345",
        "role" => "admin"
    ],
    [
        "nom" => "Pharmacien",
        "login" => "PHARM",
        "motdepasse" => "12345",
        "role" => "pharmacien"
    ],
    [
        "nom" => "Caissier",
        "login" => "CAISS",
        "motdepasse" => "12345",
        "role" => "caissier"
    ]
];

$sql = "INSERT INTO utilisateurs (nom, login, motdepasse, role)
        VALUES (:nom, :login, :motdepasse, :role)";

$stmt = $pdo->prepare($sql);

foreach ($utilisateurs as $user) {
    $hash = password_hash($user["motdepasse"], PASSWORD_DEFAULT);

    $stmt->execute([
        ":nom" => $user["nom"],
        ":login" => $user["login"],
        ":motdepasse" => $hash,
        ":role" => $user["role"]
    ]);
}

echo "✅ Comptes créés avec succès ! Supprime ce fichier après installation.";
*/




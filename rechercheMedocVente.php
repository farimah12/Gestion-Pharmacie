<?php
require_once "db.php";

$recherche = $_GET['recherche'] ?? '';

if ($recherche) {
    $stmt = $pdo->prepare(
        "SELECT * FROM medicaments 
         WHERE date_peremption >= CURDATE() 
         AND nom LIKE ? 
         ORDER BY nom"
    );
    $stmt->execute(['%' . $recherche . '%']);
    $medicaments = $stmt->fetchAll();
} else {
    $stmt = $pdo->query(
        "SELECT * FROM medicaments 
         WHERE date_peremption >= CURDATE() 
         ORDER BY nom"
    );
    $medicaments = $stmt->fetchAll();
}

if ($medicaments) {
    foreach ($medicaments as $m) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($m['nom']) . '</td>';
        echo '<td>' . number_format($m['prix'], 0, ',', ' ') . ' FCFA</td>';
        echo '<td>';
        if ($m['quantite'] <= 5) {
            echo '<span class="text-danger">' . $m['quantite'] . '</span>';
        } else {
            echo $m['quantite'];
        }
        echo '</td>';
        echo '<td>';
        echo '<input type="hidden" name="medicament_id[]" value="' . $m['id'] . '">';
        echo '<input type="number" name="quantite[]" min="0" max="' . $m['quantite'] . '" class="form-control" value="0" ' . ($m['quantite']==0?'disabled':'') . '>';
        if ($m['quantite'] == 0) {
            echo '<small class="text-danger">Épuisé</small>';
        }
        echo '</td>';
        echo '</tr>';
    }
} else {
    // Si aucun médicament trouvé
    echo '<tr><td colspan="4" class="text-center text-danger">Aucun médicament trouvé pour "' . htmlspecialchars($recherche) . '"</td></tr>';
}
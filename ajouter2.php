<?php
include 'db.php';

// Vérifier que les données sont envoyées via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $date_visite = $_POST['date_visite'];
    $nombre_visiteurs = $_POST['nombre_visiteurs'];
    $satisfaits = $_POST['satisfaits'];
    $non_satisfaits = $_POST['non_satisfaits'];

    // Validation basique des données
    if (empty($date_visite) || empty($nombre_visiteurs) || empty($satisfaits) || empty($non_satisfaits)) {
        echo json_encode(['success' => false, 'message' => 'Tous les champs sont requis.']);
        exit;
    }

    // Préparer la requête SQL
    $stmt = $conn->prepare("INSERT INTO visites (date_visite, nombre_visiteurs, satisfaits, non_satisfaits) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('siid', $date_visite, $nombre_visiteurs, $satisfaits, $non_satisfaits);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Données ajoutées avec succès.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout des données.']);
    }

    $stmt->close();
    $conn->close();
}
?>

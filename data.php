<?php
include 'db.php'; // Inclure la connexion

// Vérifier la connexion
if (!$conn) {
    die(json_encode(['error' => 'Échec de la connexion à la base de données']));
}

// Requête SQL pour récupérer les noms et les heures de travail
$sql = "SELECT CONCAT(nom, ' ', prenom) AS nom_complet, heures_travail FROM agents";
$result = $conn->query($sql);

// Vérifier si la requête a réussi
if (!$result) {
    die(json_encode(['error' => 'Erreur SQL: ' . $conn->error]));
}

// Récupérer les données
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Retourner les données en JSON
header('Content-Type: application/json; charset=UTF-8');
echo json_encode($data, JSON_UNESCAPED_UNICODE);
?>

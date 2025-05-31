<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "gestion_agents";

// Connexion à la base de données
$conn = new mysqli($host, $user, $password, $database);

// Vérification de la connexion
if ($conn->connect_error) {
    die(json_encode(['error' => 'Échec de la connexion : ' . $conn->connect_error]));
}

// Définir l'encodage en UTF-8
$conn->set_charset("utf8mb4");
?>

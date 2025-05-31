<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "municipalite";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

$sql = "SELECT id, nom_prenom, cin, email, adresse, 
               DATE_FORMAT(date_creation, '%d/%m/%Y %H:%i') as date_creation, 
               type_reclamation, sujet, statut, photo_path 
        FROM reclamations";
$result = $conn->query($sql);

$reclamations = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Corriger le chemin de l'image
        $row['photo_path'] = $row['photo_path'] ? 'uploads/' . basename($row['photo_path']) : null;
        $reclamations[] = $row;
    }
}

echo json_encode($reclamations);
$conn->close();
?>
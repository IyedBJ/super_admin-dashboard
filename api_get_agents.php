<?php
// Enable CORS for React app (adjust origin in production)
header('Access-Control-Allow-Origin: http://localhost:5173'); // Replace with your React app's URL
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_agents";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Récupération des agents
$sql = "SELECT id, prenom, nom, role, photo FROM agents";
$result = $conn->query($sql);

$agents = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $agents[] = [
            'id' => $row['id'],
            'prenom' => $row['prenom'],
            'nom' => $row['nom'],
            'categorie' => $row['role'], // Mapping 'role' to 'categorie' for the React component
            'photo' =>"C:\wamp64\www\admin+-Copie\uploads\sami.png", // Corrected path with uppercase 'U' and forward slashes
            'status' => 'Disponible' // Default status (modify if you have a status field)
        ];
    }
}

$conn->close();

// Retourner les données en JSON
header('Content-Type: application/json');
echo json_encode($agents);
?>
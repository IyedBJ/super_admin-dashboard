<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Autorise les requêtes OPTIONS pour CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "municipalite";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// Gère à la fois DELETE et GET
$id = null;
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? null;
} else {
    $id = $_GET['id'] ?? null;
}

if ($id) {
    // Supprimer d'abord la photo si elle existe
    $stmt = $conn->prepare("SELECT photo_path FROM reclamations WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row && $row['photo_path'] && file_exists($row['photo_path'])) {
        unlink($row['photo_path']);
    }
    
    // Supprimer la réclamation
    $stmt = $conn->prepare("DELETE FROM reclamations WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Réclamation supprimée']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID manquant']);
}

$conn->close();
?>
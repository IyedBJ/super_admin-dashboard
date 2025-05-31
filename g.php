<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'gestion_agents';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Connexion échouée: ' . $e->getMessage()]);
    exit;
}

// Récupérer les données des agents
$query = "SELECT nom, prenom, salaire FROM agents";
$stmt = $pdo->query($query);
$agents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Vérifier si des données existent
if (!$agents) {
    echo json_encode(['error' => 'Aucun agent trouvé']);
    exit;
}

// Préparer les données pour le JSON
$data = [
    'names' => array_map(fn($agent) => $agent['nom'] . ' ' . $agent['prenom'], $agents),
    'salaries' => array_column($agents, 'salaire')
];

// Retourner les données en JSON
header('Content-Type: application/json');
echo json_encode($data, JSON_UNESCAPED_UNICODE);
?>

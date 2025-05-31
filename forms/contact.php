<?php
header('Content-Type: application/json');

// Configuration de la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "municipalite";

// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => "Connection failed: " . $conn->connect_error]));
}

// Récupération des données avec correspondance des champs
$data = [
    'nom_prenom' => $_POST['name'] ?? '',
    'cin' => $_POST['cin'] ?? '',
    'email' => $_POST['email'] ?? '',
    'adresse' => $_POST['address'] ?? null,
    'type_reclamation' => $_POST['type'] ?? '',
    'sujet' => $_POST['subject'] ?? '',
    'photo_path' => null
];

// Validation des champs obligatoires
$required = ['nom_prenom', 'cin', 'email', 'type_reclamation', 'sujet'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        die(json_encode(['success' => false, 'message' => "Le champ correspondant à $field est obligatoire"]));
    }
}

// Gestion du fichier uploadé
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $filename = uniqid() . '_' . basename($_FILES['photo']['name']);
    $targetPath = $uploadDir . $filename;
    
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
        $data['photo_path'] = 'uploads/' . $filename;
    }
}

// Préparation de la requête avec les BONS noms de colonnes
$columns = implode(', ', array_keys($data));
$placeholders = implode(', ', array_fill(0, count($data), '?'));
$types = str_repeat('s', count($data));

$stmt = $conn->prepare("INSERT INTO reclamations ($columns, date_creation, statut) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 'Nouvelle')");
$stmt->bind_param($types, ...array_values($data));

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Réclamation enregistrée avec succès']);
} else {
    echo json_encode(['success' => false, 'message' => "Erreur: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
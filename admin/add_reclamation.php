<?php
header('Content-Type: application/json');

// Configuration de la base de données
$servername = "localhost";
$username = "root"; // Remplacez par votre nom d'utilisateur MySQL
$password = ""; // Remplacez par votre mot de passe MySQL
$dbname = "municipalite"; // Remplacez par le nom de votre base de données

// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// Récupérer les données du formulaire
$name = $_POST['name'] ?? '';
$cin = $_POST['cin'] ?? '';
$email = $_POST['email'] ?? '';
$address = $_POST['address'] ?? '';
$type = $_POST['type'] ?? '';
$subject = $_POST['subject'] ?? '';

// Gérer le fichier image
$photoPath = '';
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $filename = uniqid() . '_' . basename($_FILES['photo']['name']);
    $targetPath = $uploadDir . $filename;
    
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
        $photoPath = $targetPath;
    }
}

// Préparer et exécuter la requête SQL
$stmt = $conn->prepare("INSERT INTO reclamations (nom_prenom, cin, email, adresse, type_reclamation, sujet, photo_path, date_creation, statut) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 'Nouvelle')");
$stmt->bind_param("sssssss", $name, $cin, $email, $address, $type, $subject, $photoPath);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Votre réclamation a été envoyée. Merci!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
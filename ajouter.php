<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date_visite = $_POST['date_visite'];
    $nombre_visiteurs = intval($_POST['nombre_visiteurs']);
    $satisfaits = intval($_POST['satisfaits']);
    $non_satisfaits = intval($_POST['non_satisfaits']);

    if ($nombre_visiteurs > 0 && $satisfaits >= 0 && $non_satisfaits >= 0 && ($satisfaits + $non_satisfaits) <= $nombre_visiteurs) {
        $sql = "INSERT INTO visites (date_visite, nombre_visiteurs, satisfaits, non_satisfaits) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siii", $date_visite, $nombre_visiteurs, $satisfaits, $non_satisfaits);
        
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Données ajoutées avec succès."]);
        } else {
            echo json_encode(["success" => false, "message" => "Erreur lors de l'ajout des données : " . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Données invalides."]);
    }
    $conn->close();
}
?>

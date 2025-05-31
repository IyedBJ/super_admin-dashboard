<?php
include 'db.php';

$sql = "SELECT date_visite, 
               (satisfaits / nombre_visiteurs) * 100 AS pourcentage_satisfaits, 
               (non_satisfaits / nombre_visiteurs) * 100 AS pourcentage_non_satisfaits 
        FROM visites 
        ORDER BY date_visite ASC";

if ($result = $conn->query($sql)) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'Impossible de récupérer les données.']);
}

$conn->close();
?>

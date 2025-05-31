<?php
include 'db.php';

$sql = "SELECT date_visite, nombre_visiteurs, satisfaits, non_satisfaits FROM visites";
$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $row['pourcentage_satisfaits'] = ($row['satisfaits'] / $row['nombre_visiteurs']) * 100;
    $row['pourcentage_non_satisfaits'] = ($row['non_satisfaits'] / $row['nombre_visiteurs']) * 100;
    $data[] = $row;
}

echo json_encode($data);
?>

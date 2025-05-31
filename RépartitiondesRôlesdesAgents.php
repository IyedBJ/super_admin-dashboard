<?php 
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Activer le mode d'erreur

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_agents";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset("utf8");

    // Requête pour récupérer les rôles et le nombre d'agents
    $sql = "SELECT role, COUNT(*) as count FROM agents GROUP BY role";
    $result = $conn->query($sql);

    $total_agents = 0;
    $roles = [];
    $counts = [];

    while ($row = $result->fetch_assoc()) {
        $role = htmlspecialchars($row["role"]);
        $count = (int)$row["count"];

        $roles[] = $role;
        $counts[] = $count;
        $total_agents += $count;
    }

    $percentages = $total_agents > 0 ? array_map(fn($count) => round(($count / $total_agents) * 100, 2), $counts) : [];

    // Générer des couleurs dynamiques (assez pour tous les rôles)
    $colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4CAF50', '#8E44AD', '#E67E22', '#2ECC71'];
    while (count($colors) < count($roles)) {
        $colors[] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }

    $conn->close();
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        canvas {
            width: 400px !important;
            height: 400px !important;
        
            background: white;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            margin-top: 30px;
        }
    </style>
</head>
<body>
<div class="main-content">
    <h2>Répartition des Rôles des Agents (%)</h2>
    <canvas id="roleChart" style="max-width: 500px; height: 400px;"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('roleChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($roles, JSON_HEX_TAG); ?>,
            datasets: [{
                label: 'Pourcentage des agents par rôle',
                data: <?php echo json_encode($percentages); ?>,
                backgroundColor: <?php echo json_encode(array_slice($colors, 0, count($roles))); ?>,
                borderWidth: 1
            }]
        },
        options: {
            responsive: false,
            maintainAspectRatio: false,
            animation: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (tooltipItem) {
                            return tooltipItem.raw + '%';
                        }
                    }
                }
            }
        }
    });
});
</script>

</body>
</html>
<!-- HTML pour afficher le graphique -->

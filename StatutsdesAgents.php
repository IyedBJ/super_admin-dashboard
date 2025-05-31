
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_agents";

// Activer le mode d'erreur
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Connexion à la base de données
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset("utf8");

    // Récupération des données des statuts des agents
    $sql = "SELECT status, COUNT(*) as count FROM agents GROUP BY status";
    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("Erreur lors de l'exécution de la requête : " . $conn->error);
    }

    $total_agents = 0;
    $statuses = [];
    $counts = [];
    
    while ($row = $result->fetch_assoc()) {
        $statuses[] = htmlspecialchars($row["status"]);
        $counts[] = (int)$row["count"];
        $total_agents += $row["count"];
    }

    // Éviter une division par zéro
    $percentages = $total_agents > 0 ? array_map(fn($count) => round(($count / $total_agents) * 100, 2), $counts) : [];

    // Générer des couleurs dynamiques
    $colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4CAF50', '#8E44AD', '#E67E22', '#2ECC71'];
    while (count($colors) < count($statuses)) {
        $colors[] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }

    $conn->close();
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statut des Agents</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            display: flex;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .sidebar {
            width: 200px;
            height: 100vh;
            position: fixed;
            background: #2C3E50;
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            overflow-y: auto;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 15px;
        }
        .sidebar a {
            text-decoration: none;
            color: white;
            padding: 10px;
            display: block;
            background: #34495E;
            border-radius: 5px;
            text-align: center;
            transition: 0.3s;
        }
        .sidebar a:hover {
            background: #1ABC9C;
        }
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        canvas {
            width: 400px !important;
            height: 400px !important;
        
            background: white;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            margin-top: 30px;
        } 



.main-content {
            flex: 1;
            margin-left: 200px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
    </style>
</head>
<body>
<div class="sidebar">
        <h2>Menu</h2>
        <a href="exemple.php">Accueil</a>
        <a href="test21.html">Statistiques</a>
        <a href="dashboard.php">dashboard</a>
        <a href="tableau_agents.php">tableau agents</a>
        <a href="graphique_salaire.php">graphique salaire</a>
        <a href="connexion.html">Déconnecter</a>
    </div>

<div class="main-content">


    <h2>Répartition des Statuts des Agents (%)</h2>
    <canvas id="statusChart"></canvas>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('statusChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode($statuses, JSON_HEX_TAG); ?>,
                    datasets: [{
                        label: 'Pourcentage des agents',
                        data: <?php echo json_encode($percentages); ?>,
                        backgroundColor: <?php echo json_encode(array_slice($colors, 0, count($statuses))); ?>,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: false, // Désactiver le redimensionnement automatique
                    maintainAspectRatio: false, // Évite que le graphe se réajuste
                    animation: false, // Désactive toute animation
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
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

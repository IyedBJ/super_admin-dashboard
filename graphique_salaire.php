<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'gestion_agents';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connexion échouée: ' . $e->getMessage();
}

// Récupérer les données des agents
$query = "SELECT * FROM agents";
$stmt = $pdo->query($query);

$agents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Extraire les noms complets des agents et leurs salaires
$agent_names = [];
$agent_salaries = [];

foreach ($agents as $agent) {
    $agent_names[] = $agent['nom'] . ' ' . $agent['prenom']; // Modification ici
    $agent_salaries[] = $agent['salaire'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graphique des Salaires des Agents</title>
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
    position: fixed;
    height: 100vh; /* ajoute cette ligne */
    width: 250px;
    background-color: #2C3E50;
    color: white;
    padding: 20px;
    box-sizing: border-box;
}


.sidebar h2 {
            font-size: 26px;
    margin-bottom: 40px;
    text-align: center;
    color: #ECF0F1;
    border-bottom: 2px solid #1ABC9C;
    padding: 10px;
    width: 200px;
        
        }

        .sidebar ul {
            list-style: none;
            padding: 0;

        }

        .sidebar ul li {
            margin: 20px 0px;
        }

        .sidebar ul li a {
            color: white;
    text-decoration: none;
    font-size: 17px;
    padding: 12px;
    display: block;
    background: #34495E;
    border-radius: 8px;
    text-align: center;
    transition: all 0.3s ease;
        }

        .sidebar ul li a:hover {
            
            background: #1ABC9C;
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);

        }
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        canvas {
            background: white;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        h1 {
            text-align: center;
            color: #2C3E50 ;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Navigation</h2>
    <ul>
        <li><a href="exemple.php">Accueil</a></li>
        <li><a href="dashboard.php">Gestion des agents municipaux</a></li>
        <li><a href="tableau_agents.php">Liste des agents</a></li>
        <li><a href="ListedesAgentsavecleurManager.php">Liste des Agents avec leur Manager</a></li>
        <li><a href="test21.html">Ajouter des Données</a></li>
        <li><a href="graphique_salaire.php">graphique salaire</a></li>
        
        <li><a href="connexion.html">Déconnexion</a></li>
    </ul>
</div>

    <div class="main-content">
        <h1>Graphique des Salaires des Agents</h1>
        <canvas id="salaryChart" width="400" height="200"></canvas>
    </div>

    <script>
        const ctx = document.getElementById('salaryChart').getContext('2d');
        const salaryChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($agent_names); ?>, // Utilisation de nom complet
                datasets: [{
                    label: 'Salaire des Agents',
                    data: <?php echo json_encode($agent_salaries); ?>,
                    backgroundColor: 'rgba(26, 188, 156, 0.5)',
                    borderColor: 'rgba(26, 188, 156, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

</body>
</html>

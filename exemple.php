<?php
// Connexion à la base de données
$host = "localhost";
$user = "root";
$password = "";
$database = "gestion_agents";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Récupérer le nombre total d'agents
$sql = "SELECT COUNT(*) AS total_agents FROM agents";
$result = $conn->query($sql);
if (!$result) {
    die("Erreur lors de la récupération du nombre d'agents : " . $conn->error);
}
$row = $result->fetch_assoc();
$total_agents = $row['total_agents'];

// Graphique des rôles
$sql_roles = "SELECT role, COUNT(*) as count FROM agents GROUP BY role";
$result_roles = $conn->query($sql_roles);
if (!$result_roles) {
    die("Erreur lors de la récupération des rôles : " . $conn->error);
}

$roles = [];
$role_counts = [];

while ($row = $result_roles->fetch_assoc()) {
    $roles[] = htmlspecialchars($row["role"]);
    $role_counts[] = (int)$row["count"];
}

$role_percentages = $total_agents > 0 ? array_map(fn($count) => round(($count / $total_agents) * 100, 2), $role_counts) : [];

// Graphique des statuts
$sql_status = "SELECT status, COUNT(*) as count FROM agents GROUP BY status";
$result_status = $conn->query($sql_status);
if (!$result_status) {
    die("Erreur lors de la récupération des statuts : " . $conn->error);
}

$statuses = [];
$status_counts = [];

while ($row = $result_status->fetch_assoc()) {
    $statuses[] = htmlspecialchars($row["status"]);
    $status_counts[] = (int)$row["count"];
}

$status_percentages = $total_agents > 0 ? array_map(fn($count) => round(($count / $total_agents) * 100, 2), $status_counts) : [];

// Générer des couleurs dynamiques pour les graphiques
$colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4CAF50', '#8E44AD', '#E67E22', '#2ECC71'];
while (count($colors) < max(count($roles), count($statuses))) {
    $colors[] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title><script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment"></script>
    
    <style>
        canvas#roleChart {
    height: 300px !important;
    
            height: 400px !important;
        
            background: white;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            margin-top: 30px;
}

        



        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            
        }
        
        body {
            display: flex;
        }
        
        
        .content {
            margin-left: 250px;
            width: calc(100% - 220px);
            padding: 20px;
        }
        .topbar {
            display: flex;
            justify-content: flex-end;
            padding: 10px;
            background: #34495e;
            border-radius: 5px;
            color: aliceblue;
        }
        .cards-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 20px;
        }
        .card {
            background: white;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            text-align: center;
        
        display: flex;
        flex-direction: column; /* Garde les éléments empilés */
        justify-content: center;
        align-items: center;
}
        .counter {
            font-size: 50px;
            font-weight: bold;
            color: #2c3e50;
            transition: transform 0.3s ease-in-out, color 0.3s ease-in-out;
        }
        .counter.animated {
            transform: scale(1.2);
            color: #27ae60;
        }
        
        .main-content {
            flex: 1;
            padding: 20px;
        }
        canvas {
            background: white;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            margin-top: 30px;
        }  
         /* Sidebar */
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
    padding-bottom: 10px;
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


        h2 {
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
    <div class="content">
        <div class="topbar">Connecté en tant que Superuser</div>
        <div class="cards-container">
            <div class="card">
                <h2>Nombre Total d'Employés</h2>
                
                <div class="counter" id="employeeCount">0</div>
            </div>


            

            
            <div class="card">
            <div class="main-content">
            

                <h2>Graphique des Salaires des Agents</h2>
                <canvas id="salaryChart" width="400" height="200"></canvas>
    </div>
            </div>
            
            <div class="card">
    <h2>Répartition des Rôles des Agents (%)</h2>
    <canvas id="roleChart"></canvas>
</div>
            <div class="card">
            <h2>Répartition des Statuts des Agents (%)</h2>
            <canvas id="statusChart"></canvas>
            </div>
            <div class="card">
                <h2>Heures de Travail des Agents</h2>
                <canvas id="workChart"></canvas>
            </div>
            <div class="card">
                <h2>Courbe de satisfaction dynamique</h2>
                <canvas id="satisfactionChart"></canvas>
     
            </div>
        </div>
    </div>
    <script>
        let count = <?php echo $total_agents; ?>;
        let display = document.getElementById("employeeCount");

        let current = 0;
        let speed = Math.ceil(count / 100);
        let interval = setInterval(() => {
            if (current >= count) {
                clearInterval(interval);
                display.innerText = count;
                display.classList.add("animated"); // Effet final
            } else {
                current += speed;
                display.innerText = current;
            }
        }, 20);




        let chartInstance;

        function ajouterDonnees() {
            let formData = new FormData();
            formData.append("date_visite", document.getElementById("date_visite").value);
            formData.append("nombre_visiteurs", document.getElementById("nombre_visiteurs").value);
            formData.append("satisfaits", document.getElementById("satisfaits").value);
            formData.append("non_satisfaits", document.getElementById("non_satisfaits").value);
            
            fetch('ajouter2.php', {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    loadDataAndUpdateChart();
                }
            })
            .catch(error => console.error("Erreur :", error));
        }

        function loadDataAndUpdateChart() {
            fetch('data2.php')
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }

                    let dates = data.map(item => item.date_visite);
                    let satisfaits = data.map(item => item.pourcentage_satisfaits);
                    let nonSatisfaits = data.map(item => item.pourcentage_non_satisfaits);

                    if (chartInstance) {
                        chartInstance.destroy();
                    }

                    let ctx = document.getElementById('satisfactionChart').getContext('2d');
                    chartInstance = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: dates,
                            datasets: [
                                {
                                    label: 'Satisfaits (%)',
                                    data: satisfaits,
                                    borderColor: '#2ECC71',
                                    backgroundColor: 'rgba(46, 204, 113, 0.2)',
                                    fill: true
                                },
                                {
                                    label: 'Non Satisfaits (%)',
                                    data: nonSatisfaits,
                                    borderColor: '#E74C3C',
                                    backgroundColor: 'rgba(231, 76, 60, 0.2)',
                                    fill: true
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                x: {
                                    type: 'time',
                                    time: {
                                        unit: 'day',
                                        tooltipFormat: 'YYYY-MM-DD',
                                        displayFormats: {
                                            day: 'YYYY-MM-DD'
                                        }
                                    }
                                },
                                y: { beginAtZero: true }
                            },
                            plugins: {
                                tooltip: {
                                    mode: 'index',
                                    intersect: false
                                }
                            }
                        }
                    });
                })
                .catch(error => console.error("Erreur lors du chargement des données :", error));
        }

        loadDataAndUpdateChart();




        fetch('g.php')
            .then(response => response.json())
            .then(data => {
                const ctx = document.getElementById('salaryChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.names, // Noms des agents
                        datasets: [{
                            label: 'Salaire des Agents',
                            data: data.salaries, // Salaires des agents
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
            })
            .catch(error => console.error('Erreur lors du chargement des données:', error));



            fetch('data.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                    return;
                }

                let names = data.map(item => item.nom_complet); // Correction ici
                let hours = data.map(item => item.heures_travail);

                let ctx = document.getElementById('workChart').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: names,
                        datasets: [{
                            label: 'Heures de travail',
                            data: hours,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.6)',
                                'rgba(54, 162, 235, 0.6)',
                                'rgba(255, 206, 86, 0.6)',
                                'rgba(204, 255, 254, 0.6)',
                                'rgba(153, 102, 255, 0.6)',
                                'rgba(255, 159, 64, 0.6)',
                                'rgba(51, 204, 204, 0.6)'
                            ],
                            borderColor: '#fff',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        let index = tooltipItem.dataIndex;
                                        return names[index] + ': ' + hours[index] + ' heures';
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Erreur lors du chargement des données:', error));
    </script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('statusChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($statuses, JSON_HEX_TAG); ?>,
            datasets: [{
                label: 'Pourcentage des agents par statut',
                data: <?php echo json_encode($status_percentages); ?>,  // Utilisation des pourcentages des statuts
                backgroundColor: <?php echo json_encode(array_slice($colors, 0, count($statuses))); ?>,
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


<script>
        const ctxRole = document.getElementById('roleChart').getContext('2d');
new Chart(ctxRole, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($roles, JSON_HEX_TAG); ?>,
        datasets: [{
            label: 'Pourcentage des agents par rôle',
            data: <?php echo json_encode($role_percentages); ?>,  // Utilisation des pourcentages des rôles
            backgroundColor: <?php echo json_encode(array_slice($colors, 0, count($roles))); ?>,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            tooltip: {
                callbacks: {
                    label: function (tooltipItem) {
                        return tooltipItem.label + ': ' + tooltipItem.raw + '%';
                    }
                }
            }
        }
    }
});

    </script>

</body>
</html>

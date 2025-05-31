<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Agents</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            
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

        /* Contenu principal */
        .main-content {
            flex-grow: 1;
            background-color: #f4f4f4;
            padding: 20px;
            overflow-y: auto;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #2C3E50 ;
            margin-bottom: 20px;
        }

        .manager {
            background-color: #e7f4f7;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .agent {
            background-color: #f9f9f9;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
        }

        img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
        }

        hr {
            border: 0;
            border-top: 1px solid #ccc;
            margin: 20px 0;
        }

        @media screen and (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
            }
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
    <?php
    // Connexion à la base de données
    $host = 'localhost';
    $dbname = 'gestion_agents';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "
        SELECT a.id AS agent_id, a.nom AS agent_nom, a.prenom AS agent_prenom, a.photo AS agent_photo, 
               m.id AS manager_id, m.nom AS manager_nom, m.prenom AS manager_prenom, m.photo AS manager_photo
        FROM agents a
        LEFT JOIN agents m ON a.id_manager = m.id
        WHERE a.id_manager IS NOT NULL
        ORDER BY m.id, a.id;
        ";

        $stmt = $pdo->query($query);
        $managers_affiches = [];

        echo "<div class='container'>";
        echo "<h1>Liste des Agents avec leur Manager</h1>";

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (!in_array($row['manager_id'], $managers_affiches)) {
                echo "<div class='manager'>";
                echo "<h3>Manager: " . htmlspecialchars($row['manager_nom']) . " " . htmlspecialchars($row['manager_prenom']) . "</h3>";
                echo "<p><img src='uploads/" . htmlspecialchars($row['manager_photo']) . "' alt='Photo du manager'></p>";
                echo "</div>";
                echo "<hr>";

                $managers_affiches[] = $row['manager_id'];
            }

            echo "<div class='agent'>";
            echo "<p>Agent: " . htmlspecialchars($row['agent_nom']) . " " . htmlspecialchars($row['agent_prenom']) . "</p>";
            echo "<p><img src='uploads/" . htmlspecialchars($row['agent_photo']) . "' alt='Photo de l'agent'></p>";
            echo "</div>";
            echo "<hr>";
        }
        echo "</div>";
    } catch (PDOException $e) {
        echo "Erreur de connexion ou de requête : " . $e->getMessage();
    }
    ?>
</div>

</body>
</html>

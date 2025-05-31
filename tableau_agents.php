<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'gestion_agents';
$username = 'root';
$password = '';

// Créer une connexion
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Définir le mode d'erreur
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connexion échouée: ' . $e->getMessage();
}

// Récupérer les données des agents
$query = "SELECT * FROM agents";
$stmt = $pdo->query($query);

// Stocker les résultats dans un tableau associatif
$agents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau des Agents</title>
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

/* Sidebar */
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

/* Main Content */
.main-content {
    flex: 1;
    padding: 20px;
    margin-left: 250px; /* Pour éviter que le contenu ne soit sous la sidebar */
}

h1, h2 {
    color: #2C3E50;
    margin-bottom: 15px;
    
}

/* Tableau */
table {
    width: 100%;
    margin-top: 30px;
    border-collapse: collapse;
    background: white;
    box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
}

th, td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background-color: #2C3E50;
    color: white;
}

tr:hover {
    background-color: #ecf0f1;
}
img {
    width: 100px; /* Largeur de l'image (ajuste selon tes besoins) */
    height: 100px; /* Hauteur de l'image (doit être égale à la largeur) */
    border-radius: 50%; /* Rend l'image circulaire */
    object-fit: cover; /* Optionnel : permet à l'image de bien s'ajuster à la forme du cercle */
}


/* Style pour le bouton */
button[type="submit"] {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            background-color: #34495E;
            color: white;
            display: block;       /* Transforme le bouton en élément block */
            margin: 20px auto;       /* Centre horizontalement */
            
}


        button:hover {
            background-color:#1ABC9C;
        }

        .btn{


            
            display: flex;
            justify-content: center;   /* Centre horizontalement */
            align-items: center;       /* Centre verticalement */
            height: 10vh;              /* Si tu veux centrer dans toute la hauteur de l'écran */


        }

        <style>
a {
    text-decoration: none;
}


.header-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%; /* Prend toute la largeur */
}


th ,td {
    text-align: center;
    vertical-align: middle;
    height: 100px;
    width: 200px;
}





</style>

    </style>
</head>
<body>

    <!-- Sidebar -->
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

    <!-- Main Content -->
    <div class="main-content">




        <div class="header-container">
    <h1>Tableau des Agents</h1>
    <div class="btn">
        <a href="tableau_agentscopycopy.php" style="text-decoration: none;">
            <button type="submit">Modifier</button>
        </a>
        
    </div>
</div>

        <!-- Tableau des agents -->
        <h2>Liste des Agents</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Photo</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Rôle</th>
                    <th>Salaire</th>
                    <th>Heures de Travail</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Afficher les données des agents
                foreach ($agents as $agent) {
                    echo "<tr>
                            <td>{$agent['id']}</td>
                            <td><img src='uploads/{$agent['photo']}' alt='Photo de {$agent['nom']}' width='100' height='100'></td>
                            <td>{$agent['nom']}</td>
                            <td>{$agent['prenom']}</td>
                            <td>{$agent['role']}</td>
                            <td>{$agent['salaire']} DT</td>
                            <td>{$agent['heures_travail']}</td>
                            <td>{$agent['status']} </td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>


    

    <script>
document.addEventListener("DOMContentLoaded", function () {
    let colors = {
        "En formation": "yellow",
        "En congé": "red",
        "Disponible": "green",
        "En mission": "purple"
    };

    document.body.innerHTML = document.body.innerHTML.replace(
        /(En formation|En congé|Disponible|En mission)/g,
        function (match) {
            return `<span style="background-color: ${colors[match]}; color: white; padding: 10px 25px; border-radius: 7px;">${match}</span>`;
        }
    );
});
</script>


</body>
</html>

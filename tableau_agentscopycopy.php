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
$query_agents = "SELECT * FROM agents";
$stmt_agents = $pdo->query($query_agents);
$agents = $stmt_agents->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les options de statut
$query_status = "SELECT * FROM status";
$stmt_status = $pdo->query($query_status);
$status_options = $stmt_status->fetchAll(PDO::FETCH_ASSOC);

// Mettre à jour les statuts des agents
if (isset($_POST['update_status'])) {
    foreach ($_POST['status'] as $agent_id => $new_status) {
        $update_query = "UPDATE agents SET status = :status WHERE id = :id";
        $stmt_update = $pdo->prepare($update_query);
        $stmt_update->bindParam(':status', $new_status);
        $stmt_update->bindParam(':id', $agent_id);
        $stmt_update->execute();
    }
    header('Location: tableau_agents.php'); // Pour éviter un rechargement de la page avec des données du formulaire
    exit();
}
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

        .main-content {
            flex: 1;
            padding: 20px;
            margin-left: 200px;
        }

        h1, h2 {
            color: #2C3E50;
            margin-bottom: 15px;
        }

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


        th ,td {
    text-align: center;
    vertical-align: middle;
    height: 100px;
    width: 200px;
}


        th {
            background-color: #2C3E50;
            color: white;
        }

        tr:hover {
            background-color: #ecf0f1;
        }

        img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
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

        

        /* Style pour les options du select */
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: white;
        }

        /* Changer la couleur de fond des options */
        select option {
            background-color: white;
            color: black;
        }

        select option[value="En congé"] {
            background-color: red;
            color: white;
        }

        select option[value="Disponible"] {
            background-color: green;
            color: white;
        }

        select option[value="En formation"] {
            background-color: yellow;
            color: black;
        }

        select option[value="En mission"] {
            background-color: purple;
            color: white;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Menu</h2>
        <a href="exemple.php">Accueil</a>
        <a href="test21.html">Statistiques</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="tableau_agents.php">Tableau Agents</a>
        <a href="graphique_salaire.php">Graphique Salaire</a>
        <a href="connexion.html">Déconnexion</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Tableau des Agents</h1>

        <!-- Formulaire global pour la mise à jour des statuts -->
        <form action="" method="POST">
            <h2>Liste des Agents</h2>

            <!-- Tableau des agents -->
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
                        
                        <th>Modifier Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($agents as $agent) {
                        // Déterminer la classe à appliquer pour le bouton en fonction du statut
                        $button_class = '';
                        switch ($agent['status']) {
                            case 'Disponible':
                                $button_class = 'disponible';
                                break;
                            case 'En formation':
                                $button_class = 'formation';
                                break;
                            case 'En mission':
                                $button_class = 'mission';
                                break;
                            case 'En congé':
                                $button_class = 'conge';
                                break;
                            default:
                                $button_class = '';
                        }

                        echo "<tr>
                                <td>{$agent['id']}</td>
                                <td><img src='uploads/{$agent['photo']}' alt='Photo de {$agent['nom']}' width='100' height='100'></td>
                                <td>{$agent['nom']}</td>
                                <td>{$agent['prenom']}</td>
                                <td>{$agent['role']}</td>
                                <td >{$agent['salaire']} DT</td>
                                <td>{$agent['heures_travail']}</td>
                                
                                <td>
                                    <select name='status[{$agent['id']}]'>
                                        ";
                                        foreach ($status_options as $status) {
                                            echo "<option value='{$status['status_name']}'" . ($agent['status'] == $status['status_name'] ? " selected" : "") . ">{$status['status_name']}</option>";
                                        }
                        echo "
                                    </select>
                                </td>
                            </tr>";
                    }
                    ?>
                </tbody>
            </table>
            <div class="btn">
                 <!-- Bouton "Mettre à jour" avec la classe dynamique -->
                <button  type="submit" name="update_status" class="<?php echo $button_class; ?> ">Mettre à jour</button>

            </div>
           
        </form>
    </div>


    

</body>
</html>

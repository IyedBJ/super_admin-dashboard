<?php
session_start(); // Démarrer la session

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_agents";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Ajout d'un agent
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_agent'])) {
    $prenom = $_POST['agent-prenom'];
    $nom = $_POST['agent-nom'];
    $role = $_POST['agent-role'];
    $salaire = $_POST['agent-salaire'];
    $heures_travail = $_POST['agent-heures_travail'];
    $photo = $_FILES['agent-photo']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["agent-photo"]["name"]);
    move_uploaded_file($_FILES["agent-photo"]["tmp_name"], $target_file);


    $sql = "INSERT INTO agents (prenom, nom, role, salaire, heures_travail, photo) 
        VALUES ('$prenom', '$nom', '$role', '$salaire', '$heures_travail', '$photo')";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "✅ Nouvel agent créé avec succès.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "❌ Erreur: " . $conn->error;
        $_SESSION['message_type'] = "error";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


// Suppression d'un agent
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_agent'])) {
    $id = $_POST['agent_id'];
    $sql = "DELETE FROM agents WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "✅ Agent supprimé avec succès.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "❌ Erreur: " . $conn->error;
        $_SESSION['message_type'] = "error";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Récupération des agents et rôles
$sql_agents = "SELECT * FROM agents";
$agents_result = $conn->query($sql_agents);

$sql_roles = "SELECT * FROM roles";
$roles_result = $conn->query($sql_roles);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des agents municipaux</title>
    <style>
        /* CSS de la barre latérale (sidebar) */
        .sidebar {
            
            height: 100vh;
            
         
          
            position: fixed;
       
            width: 160px;
            background: #2C3E50;
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
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
/* Contenu principal de la page */
.content {
    margin-left: 250px; /* Décale le contenu vers la droite pour qu'il ne soit pas caché derrière la sidebar */
    padding: 20px;
}

/* Style pour le corps de la page */
body {
    font-family: Arial, sans-serif;
    background-color: #e5e5e5;
    margin: 0;
    padding: 0;
    color: #333;
}

/* En-tête */
header {
    border-radius: 15px;
    background-color: #2C3E50;
    color: white;
    padding: 50px;
    text-align: center;
    font-size: 24px;
    letter-spacing: 1px;
    margin-left: 20px;
    margin-right: 20px; /* Décale l'en-tête vers la droite pour qu'il soit à côté de la sidebar */
}

header h1 {
    margin: 0;
}

/* Style pour les boutons */
button {
    background-color: #3498DB;
    margin-top: 25px;
    color: white;
    border: none;
    padding: 12px 25px;
    cursor: pointer;
    font-size: 16px;
    border-radius: 5px;
    transition: background-color 0.3s ease-in-out;
}

button:hover {
    background-color: #2980B9;
}

/* Zone du message */
#message-box {
    display: none;
    padding: 15px;
    margin: 20px;
    border-radius: 5px;
    text-align: center;
    font-weight: bold;
    width: 80%;
    margin-left: auto;
    margin-right: auto;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
}

/* Liste des agents */
#agent-list {
    padding: 30px;
    margin: 20px;
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
}

#agents {
    list-style-type: none;
    padding: 0;
}

#agents li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #f9f9f9;
    margin: 8px 0;
    padding: 12px 20px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: background-color 0.3s;
}

#agents li:hover {
    background-color: #ecf0f1;
}

/* Style du bouton de suppression */
#agents form button {
    background-color: #e74c3c;
    border-radius: 4px;
    padding: 8px 15px;
    font-size: 14px;
    transition: background-color 0.3s ease-in-out;
}

#agents form button:hover {
    background-color: #c0392b;
}

/* Modal */
/* Modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.4);
    padding-top: 3px; /* Réduction de l'espacement en haut */
}

/* Contenu du modal */
.modal-content {
    background-color: #fff;
    margin: 1% auto; /* Déplacement plus haut */
    padding: 30px;
    border-radius: 8px;
    width: 40%;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}


/* Style de la croix pour fermer le modal */
.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover {
    color: #000;
    cursor: pointer;
}

/* Style pour les champs de saisie */
input, select {
    width: 100%;
    padding: 12px;
    margin: 8px 0;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
}

/* Focus sur les champs */
input:focus, select:focus {
    border-color: #3498DB;
    outline: none;
}

/* Style des boutons dans le formulaire */
form button {
    width: 100%;
    background-color: #3498DB;
    padding: 12px;
    font-size: 16px;
    border-radius: 5px;
    color: white;
    border: none;
    transition: background-color 0.3s ease-in-out;
}

form button:hover {
    background-color: #2980B9;
}


img {
    width: 100px; /* Largeur de l'image (ajuste selon tes besoins) */
    height: 100px; /* Hauteur de l'image (doit être égale à la largeur) */
    border-radius: 50%; /* Rend l'image circulaire */
    object-fit: cover; /* Optionnel : permet à l'image de bien s'ajuster à la forme du cercle */
}


    </style>
</head>

<body>

    <!-- Menu à gauche -->
    <div class="sidebar">
        <h2 style="text-align: center;">Menu</h2>
        <a href="exemple.php">Accueil</a>
            <a href="test21.html">Statistiques</a>
            <a href="dashboard.php">dashboard</a>
            <a href="tableau_agents.php">tableau agents</a>
            <a href="graphique_salaire.php">graphique salaire</a>
            <a href="connexion.html">Deconneter</a>
            
    </div>

    <!-- Contenu principal de la page -->
    <div class="content">
        <header>
            <h1>Gestion des agents municipaux</h1>
            <button onclick="toggleModal('create')">Créer un agent</button>
        </header>

        <div id="message-box"></div>

        <section id="agent-list">
            <h2>Liste des agents</h2>
            <ul id="agents">
                <?php
                if ($agents_result->num_rows > 0) {
                    while ($row = $agents_result->fetch_assoc()) {


                        echo "<li>
                        <img src='uploads/{$row['photo']}' width='50' height='50' alt='Photo'>
                        {$row['prenom']} {$row['nom']}  <span style='color:rgb(0, 58, 97);'>{$row['role']}</span>
                
                        <form method='POST' style='display:inline;'>
                            <input type='hidden' name='agent_id' value='{$row['id']}'>
                            <button type='submit' name='delete_agent'>Supprimer</button>
                        </form>
                      </li>";
                
                    }
                } else {
                    echo "<p>Aucun agent trouvé.</p>";
                }
                ?>
            </ul>
        </section>

        <!-- Modal pour la création d'un agent -->
        <div id="create-agent-modal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('create')">&times;</span>
                <h2>Créer un nouvel agent</h2>
                <form method="POST" enctype="multipart/form-data">
                <label for="agent-prenom">Prénom</label>
                <input type="text" id="agent-prenom" name="agent-prenom" required>

                <label for="agent-nom">Nom</label>
                <input type="text" id="agent-nom" name="agent-nom" required>

                <label for="agent-photo">Photo</label>
                <input type="file" id="agent-photo" name="agent-photo" accept="image/*" required>



            

                    <label for="agent-role">Rôle</label>
                    <select id="agent-role" name="agent-role">
                        <?php
                        if ($roles_result->num_rows > 0) {
                            while ($role = $roles_result->fetch_assoc()) {
                                echo "<option value='{$role['name']}'>{$role['name']}</option>";
                            }
                        }
                        ?>
                    </select>

                    <label for="agent-salaire">Salaire</label>
                    <input type="number" id="agent-salaire" name="agent-salaire" required>

                    <label for="agent-heures_travail">Heures de travail</label>
                    <input type="number" id="agent-heures_travail" name="agent-heures_travail" required>

                    

                    <button type="submit" name="create_agent">Créer</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Script pour ouvrir et fermer les modals
        function toggleModal(action) {
            document.getElementById(action + "-agent-modal").style.display = "block";
        }
        function closeModal(action) {
            document.getElementById(action + "-agent-modal").style.display = "none";
        }
    </script>

</body>
</html>

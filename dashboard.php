

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
    $email = $_POST['agent-email'];
    $password = password_hash($_POST['agent-password'], PASSWORD_DEFAULT); // Hash du mot de passe

    // Traitement de la photo
    $photo = $_FILES['agent-photo']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["agent-photo"]["name"]);
    move_uploaded_file($_FILES["agent-photo"]["tmp_name"], $target_file);

    // Manager sélectionné (si un manager est sélectionné)
    $id_manager = !empty($_POST['agent-manager']) ? $_POST['agent-manager'] : "NULL";

    // Requête SQL avec email, mot de passe et manager
    $sql = "INSERT INTO agents (prenom, nom, role, salaire, heures_travail, photo, email, password, id_manager) 
            VALUES ('$prenom', '$nom', '$role', '$salaire', '$heures_travail', '$photo', '$email', '$password', $id_manager)";

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

//------------------
// Mise à jour d'un agent
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_agent'])) {
    $id = $_POST['agent_id'];
    $salaire = $_POST['agent-salaire'];
    $heures_travail = $_POST['agent-heures_travail'];
    $password = !empty($_POST['agent-password']) ? password_hash($_POST['agent-password'], PASSWORD_DEFAULT) : null;
    $id_manager = $_POST['agent-manager'];

    // Préparer la requête SQL pour mettre à jour l'agent
    $sql = "UPDATE agents SET salaire = ?, heures_travail = ?, id_manager = ?";
    if ($password) {
        $sql .= ", password = ?";
    }
    $sql .= " WHERE id = ?";

    // Préparer la requête
    $stmt = $conn->prepare($sql);

    // Lier les paramètres
    if ($password) {
        $stmt->bind_param("dsdsi", $salaire, $heures_travail, $id_manager, $password, $id);
    } else {
        $stmt->bind_param("dsdi", $salaire, $heures_travail, $id_manager, $id);
    }

    // Exécuter la requête
    if ($stmt->execute()) {
        $_SESSION['message'] = "✅ Agent mis à jour avec succès.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "❌ Erreur: " . $conn->error;
        $_SESSION['message_type'] = "error";
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}







//---------------------




// Récupération des agents et rôles
$sql_agents = "SELECT * FROM agents";
$agents_result = $conn->query($sql_agents);

// Récupération des rôles
$sql_roles = "SELECT * FROM roles";
$roles_result = $conn->query($sql_roles);

// Récupération des managers (agents avec rôle 'Manager')
$sql_managers = "SELECT * FROM agents WHERE role = 'Manager'";
$managers_result = $conn->query($sql_managers);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des agents municipaux</title>
    <style>
        /* CSS de la barre latérale (sidebar) */
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
#supprimer {
    background-color: #e74c3c;
    border-radius: 4px;
    padding: 8px 15px;
    font-size: 14px;
    transition: background-color 0.3s ease-in-out;
}

#supprimer:hover {
    background-color: #c0392b;
}

/* Style du bouton de modification */
#modifier {
    background-color: rgb(255, 251, 0);
    border-radius: 4px;
    padding: 8px 15px;
    font-size: 14px;
    transition: background-color 0.3s ease-in-out;
}

#modifier:hover {
    background-color: rgb(190, 192, 43);
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
    
    padding: 20px;
    border-radius: 8px;
    width: 70%;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    position: absolute;
    top: 50%;
    left: 60%;
    transform: translate(-50%, -50%); /* Centrage parfait */
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
    width: 50%;
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
#modifier{
    background-color: green;
}


    </style>
</head>

<body>

    <!-- Menu à gauche -->
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
                                       
                                        <button id='modifier' type='button' onclick='window.location.href=\"modifier_agent.php?id={$row['id']}\"'>Modifier</button>
                                         <button id='supprimer' type='submit' name='delete_agent'>Supprimer</button>
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
                <h3>Créer un nouvel agent</h3>
                <form method="POST" enctype="multipart/form-data">
                <form method="POST" enctype="multipart/form-data">
    <table style="width: 100%;">
        <tr>
            <td><label for="agent-prenom">Prénom</label></td>
            <td><input type="text" id="agent-prenom" name="agent-prenom" required></td>
            <td><label for="agent-nom">Nom</label></td>
            <td><input type="text" id="agent-nom" name="agent-nom" required></td>
        </tr>

        
        
        <tr>
            <td><label for="agent-role">Rôle</label></td>
            <td>
                <select id="agent-role" name="agent-role">
                    <?php
                    if ($roles_result->num_rows > 0) {
                        while ($role = $roles_result->fetch_assoc()) {
                            echo "<option value='{$role['name']}'>{$role['name']}</option>";
                        }
                    }
                    ?>
                </select>
            </td>
            <td><label for="agent-manager">Manager de l'équipe (optionnel)</label></td>
            <td>
                <select id="agent-manager" name="agent-manager">
                    <option value="">Aucun</option>
                    <?php
                    // Récupère les managers de la BD
                    $sql_managers = "SELECT id, prenom, nom FROM agents WHERE role = 'Manager'";
                    $managers_result = $conn->query($sql_managers);
                    if ($managers_result->num_rows > 0) {
                        while ($manager = $managers_result->fetch_assoc()) {
                            echo "<option value='{$manager['id']}'>{$manager['prenom']} {$manager['nom']}</option>";
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><label for="agent-salaire">Salaire</label></td>
            <td><input type="number" id="agent-salaire" name="agent-salaire" required></td>
            <td><label for="agent-heures_travail">Heures de travail</label></td>
            <td><input type="number" id="agent-heures_travail" name="agent-heures_travail" required></td>
        </tr>
        
        <tr>
            <td><label for="agent-email">Email</label></td>
            <td><input type="email" id="agent-email" name="agent-email" required></td>
            <td><label for="agent-password">Mot de passe</label></td>
            <td><input type="password" id="agent-password" name="agent-password" required></td>
        </tr>
        <tr>
            <td><label for="agent-photo">Photo</label></td>
            <td colspan="3"><input type="file" id="agent-photo" name="agent-photo" accept="image/*" required></td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: center;">
                <button type="submit" name="create_agent">Créer</button>
            </td>
        </tr>
    </table>
</form>

</form>


            </div>
        </div>
    </div>








    <!-- Formulaire pour modifier l'agent, visible lorsque l'agent est sélectionné -->
    <?php if (isset($agent)): ?>
    <h3>Modifier l'agent: <?php echo $agent['prenom'] . ' ' . $agent['nom']; ?></h3>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="agent_id" value="<?php echo $agent['id']; ?>">

        <label for="agent-manager">Manager (optionnel):</label>
        <select name="agent-manager">
            <option value="">Sélectionner un manager</option>
            <?php while ($manager = $managers_result->fetch_assoc()): ?>
                <option value="<?php echo $manager['id']; ?>" <?php echo $agent['id_manager'] == $manager['id'] ? 'selected' : ''; ?>>
                    <?php echo $manager['prenom'] . ' ' . $manager['nom']; ?>
                </option>
            <?php endwhile; ?>
        </select><br>

        <label for="agent-salaire">Salaire:</label>
        <input type="text" name="agent-salaire" value="<?php echo $agent['salaire']; ?>" required><br>

        <label for="agent-heures_travail">Heures de travail:</label>
        <input type="text" name="agent-heures_travail" value="<?php echo $agent['heures_travail']; ?>" required><br>

        <label for="agent-password">Mot de passe:</label>
        <input type="password" name="agent-password" placeholder="Nouveau mot de passe"><br>

        <button type="submit" name="update_agent">Mettre à jour</button>
    </form>
<?php endif; ?>


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

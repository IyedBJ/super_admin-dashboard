<?php  
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_agents";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Récupérer l'ID de l'agent
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sécurisation de l'ID
    
    // Requête pour récupérer les détails de l'agent
    $sql = "SELECT * FROM agents WHERE id = $id";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $agent = $result->fetch_assoc();
    } else {
        $_SESSION['message'] = "❌ Agent non trouvé.";
        $_SESSION['message_type'] = "error";
        header("Location: dashboard.php");
        exit();
    }
} else {
    $_SESSION['message'] = "❌ Aucun ID d'agent fourni.";
    $_SESSION['message_type'] = "error";
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Agent</title>
    <style>

       
        body {
    font-family: Arial, sans-serif;
    background-color: #f4f7fc;
    margin: 0;
    padding: 0;
}

form {
    background-color: #ffffff;
    padding: 20px;
    max-width: 600px;
    margin: 50px auto;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

h3 {
    font-size: 18px;
    color: #333;
    margin: 10px 0 5px;
}

p {
    font-size: 16px;
    color: #555;
    margin: 5px 0 20px;
}

label {
    font-size: 14px;
    color: #333;
    margin-bottom: 6px;
    display: block;
}

input[type="text"],
input[type="email"],
input[type="number"],
input[type="password"],
select {
    width: 100%;
    padding: 12px;
    margin: 8px 0 20px 0;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    font-size: 14px;
}

input[readonly] {
    background-color: #f7f7f7;
}

select {
    width: 100%;
    padding: 12px;
    margin: 8px 0 20px 0;
    border: 1px solid #ccc;
    border-radius: 4px;
    background-color: #fff;
    font-size: 14px;
}

button {
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    width: 100%;
    margin-bottom: 10px;  /* Espace entre les boutons */
    display: inline-block; /* Pour les rendre inline-block si on les place dans un conteneur flex */
}

button#update {
    background-color: #4CAF50;  /* Vert pour "Mettre à jour" */
}

button#update:hover {
    background-color: #45a049;  /* Vert plus foncé au survol */
}

button#allumer {
    background-color: #f44336;  /* Rouge pour "Allumer" */
}

button#allumer:hover {
    background-color: #da190b;  /* Rouge plus foncé au survol */
}

.container {
    display: flex;
    justify-content: space-between;  /* Espace entre les boutons */
}


img {
    width: 200px; /* Largeur de l'image (ajuste selon tes besoins) */
    height: 200px; /* Hauteur de l'image (doit être égale à la largeur) */
    border-radius: 50%; /* Rend l'image circulaire */
    object-fit: cover; 
    display: block;  /* Permet de centrer l'image */
    margin-left: auto;  /* Centre l'image à gauche */
    margin-right: auto;  /* Centre l'image à droite */
}





    </style>
</head>
<body>
    <!-- Formulaire de modification -->
    <form method="POST">
        <img src="uploads/<?php echo htmlspecialchars($agent['photo']); ?>"  alt="Photo de l'agent">

        <input type="hidden" name="agent_id" value="<?php echo htmlspecialchars($agent['id']); ?>">

        <h3>Nom</h3>
        <p><?php echo htmlspecialchars($agent['nom']); ?></p>

        <h3>Prénom</h3>
        <p><?php echo htmlspecialchars($agent['prenom']); ?></p>

        <label for="agent-email">Email</label>
        <input type="email" id="agent-email" name="agent-email" value="<?php echo htmlspecialchars($agent['email']); ?>" readonly>

        <label for="agent-salaire">Salaire</label>
        <input type="number" id="agent-salaire" name="agent-salaire" value="<?php echo htmlspecialchars($agent['salaire']); ?>" required>

        <label for="agent-heures_travail">Heures de travail</label>
        <input type="number" id="agent-heures_travail" name="agent-heures_travail" value="<?php echo htmlspecialchars($agent['heures_travail']); ?>" required>

        <label for="agent-password">Mot de passe</label>
        <input type="password" id="agent-password" name="agent-password">

        <label for="agent-manager">Manager</label>
        <select id="agent-manager" name="agent-manager">
            <option value="">Aucun</option>
            <?php
            // Récupérer les managers disponibles
            $sql_managers = "SELECT id, prenom, nom FROM agents WHERE role = 'Manager'";
            $managers_result = $conn->query($sql_managers);
            while ($manager = $managers_result->fetch_assoc()) {
                $selected = ($agent['id_manager'] == $manager['id']) ? "selected" : "";
                echo "<option value='{$manager['id']}' $selected>{$manager['prenom']} {$manager['nom']}</option>";
            }
            ?>
        </select>

        <div class="container">
    <button id="update" type="submit" name="update_agent">Mettre à jour</button>
    <button id="allumer">
        <a href="dashboard.php" style="color: white; text-decoration: none;">Allumer</a>
    </button>
</div>


    </form>

    <?php
    // Traitement de la mise à jour
    if (isset($_POST['update_agent'])) {
        $agent_id = $_POST['agent_id'];
        $salaire = $_POST['agent-salaire'];
        $heures_travail = $_POST['agent-heures_travail'];
        $password = $_POST['agent-password'];
        $id_manager = $_POST['agent-manager'];

        // Initialisation de la requête pour mettre à jour les champs autorisés
        $update_sql = "UPDATE agents SET 
                    salaire = '$salaire', 
                    heures_travail = '$heures_travail',
                    id_manager = '$id_manager'";

        // Si un mot de passe a été fourni, on le met à jour
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update_sql .= ", password = '$hashed_password'";
        }

        // Ajouter la condition WHERE pour mettre à jour l'agent spécifique
        $update_sql .= " WHERE id = $agent_id";
        
        // Exécution de la mise à jour
        if ($conn->query($update_sql) === TRUE) {
            $_SESSION['message'] = "✅ Agent mis à jour avec succès.";
            $_SESSION['message_type'] = "success";
            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['message'] = "❌ Erreur de mise à jour : " . $conn->error;
            $_SESSION['message_type'] = "error";
            header("Location: dashboard.php");
            exit();
        }
    }
    ?>
</body>
</html>

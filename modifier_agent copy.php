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
    $id = (int) $_GET['id']; // Assurez-vous que l'ID est un entier
    
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
<html lang="en">
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
        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <!-- Formulaire de modification -->
    <form method="POST" enctype="multipart/form-data">
        <h3><?php echo htmlspecialchars($agent['id']); ?></h3>
        <input type="hidden" name="agent_id" value="<?php echo htmlspecialchars($agent['id']); ?>">
        
        <label for="agent-nom">Nom</label>
        <input type="text" id="agent-nom" name="agent-nom" value="<?php echo htmlspecialchars($agent['nom']); ?>" readonly>
        
        <label for="agent-prenom">Prénom</label>
        <input type="text" id="agent-prenom" name="agent-prenom" value="<?php echo htmlspecialchars($agent['prenom']); ?>" readonly>
        
        <label for="agent-email">Email</label>
        <input type="email" id="agent-email" name="agent-email" value="<?php echo htmlspecialchars($agent['email']); ?>" readonly>
        
        <label for="agent-salaire">Salaire</label>
        <input type="number" id="agent-salaire" name="agent-salaire" value="<?php echo htmlspecialchars($agent['salaire']); ?>">
        
        <label for="agent-heures_travail">Heures de travail</label>
        <input type="number" id="agent-heures_travail" name="agent-heures_travail" value="<?php echo htmlspecialchars($agent['heures_travail']); ?>">
        
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
                echo "<option value='{$manager['id']}'" . ($agent['id_manager'] == $manager['id'] ? " selected" : "") . ">{$manager['prenom']} {$manager['nom']}</option>";
            }
            ?>
        </select>

        <label for="agent-photo">Photo</label>
        <input type="file" id="agent-photo" name="agent-photo" accept="image/*">
        
        <button type="submit" name="update_agent">Mettre à jour</button>
    </form>

    <?php
    // Traitement de la mise à jour
    if (isset($_POST['update_agent'])) {
        $agent_id = $_POST['agent_id'];
        $salaire = $_POST['agent-salaire'];
        $heures_travail = $_POST['agent-heures_travail'];
        $password = $_POST['agent-password'];
        $id_manager = $_POST['agent-manager'];
        $photo = $_FILES['agent-photo'];

        // Initialisation de la requête pour mettre à jour les champs autorisés
        $update_sql = "UPDATE agents SET 
                    salaire = '$salaire', 
                    heures_travail = '$heures_travail',
                    id_manager = '$id_manager'";

        // Vérifier si une nouvelle image a été téléchargée
        if ($photo['size'] > 0) {
            // Déplacer l'image vers le dossier "uploads"
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($photo["name"]);
            move_uploaded_file($photo["tmp_name"], $target_file);
            $update_sql .= ", photo = '$target_file'";
        }

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

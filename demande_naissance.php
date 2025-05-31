<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Informations de connexion à la base de données
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "mairieconnect";

    // Connexion à la base de données MySQL avec MySQLi
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Vérifier la connexion
    if (!$conn) {
        die("Connexion échouée : " . mysqli_connect_error());
    }

    // Vérifier si le formulaire a été soumis
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Préparer la requête SQL avec des placeholders
        $sql = "SELECT fichier_acte FROM actes_naissance1 WHERE nom = ? AND prenom = ? AND date_naissance = ? AND lieu_naissance = ?";
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt === false) {
            die('Erreur de préparation de la requête : ' . mysqli_error($conn));
        }

        // Lier les paramètres à la requête
        mysqli_stmt_bind_param($stmt, "ssss", $_POST['nom'], $_POST['prenom'], $_POST['date_naissance'], $_POST['lieu_naissance']);

        // Exécuter la requête
        mysqli_stmt_execute($stmt);

        // Récupérer les résultats
        $result = mysqli_stmt_get_result($stmt);

        // Si un acte est trouvé
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $fichier_acte = $row['fichier_acte'];

            // Vérifier si le fichier existe sur le serveur
            if (file_exists($fichier_acte)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . basename($fichier_acte) . '"');
                header('Content-Length: ' . filesize($fichier_acte));
                readfile($fichier_acte);
                exit;
            } else {
                echo "Fichier de l'acte non trouvé.";
            }
        } else {
            echo "Aucun acte de naissance trouvé pour ces informations.";
        }

        // Fermer la déclaration préparée
        mysqli_stmt_close($stmt);
    }

    // Fermer la connexion à la base de données
    mysqli_close($conn);
?>

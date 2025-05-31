<?php
// Démarrer la session pour pouvoir accéder aux variables de session
session_start();

// Suppression de toutes les variables de session
session_unset();

// Détruire la session
session_destroy();

// Supprimer l'historique de navigation pour éviter le retour en arrière
if (function_exists('header')) {
    // Envoi des en-têtes pour éviter la mise en cache
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
    header("Expires: 0");
}

// Redirection vers la page de connexion après la déconnexion
header("Location: connexion.html");
exit();
?>

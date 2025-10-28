<?php
// 1. Démarrer la session si elle est active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Détruire toutes les variables de session
// Utilisez session_unset() et $_SESSION = [] pour une compatibilité maximale
$_SESSION = [];
session_unset();

// 3. Détruire le cookie de session
// Si un cookie de session existe, le détruire pour s'assurer que l'utilisateur est bien déconnecté.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Détruire la session elle-même
session_destroy();

// 5. Redirection vers la page d'accueil ou de connexion
// Remplacez 'index.php' par la page vers laquelle vous voulez rediriger l'utilisateur.
header('Location: index.php');
exit; // Toujours mettre exit après une redirection pour s'assurer que le script s'arrête.
?>
<?php
session_start();
require_once '../config/database.php';

// Récupérer l'action demandée dans l'URL
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'logout':
        deconnecter();
        break;

    default:
        header("Location: ../index.php");
        exit();
}

// ─────────────────────────────────────────
// FONCTION : Déconnexion
// ─────────────────────────────────────────
function deconnecter() {
    // Détruire toutes les variables de session
    $_SESSION = [];

    // Détruire le cookie de session
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(), '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    // Détruire la session
    session_destroy();

    // Rediriger vers la page de connexion
    header("Location: ../views/auth/login.php");
    exit();
}
?>

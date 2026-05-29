<?php
// ─────────────────────────────────────────
// Protection CSRF
// Génère un token unique par session
// ─────────────────────────────────────────

function genererTokenCSRF() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifierTokenCSRF() {
    $token = $_POST['csrf_token'] ?? '';
    if (empty($token) || $token !== ($_SESSION['csrf_token'] ?? '')) {
        die("⛔ Requête invalide — Token CSRF incorrect.");
    }
}

function champCSRF() {
    $token = genererTokenCSRF();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}
?>
<?php
session_start();
require_once '../config/database.php';
require_once '../models/Order.php';
require_once '../models/Cart.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/auth/login.php");
    exit();
}

$action = $_GET['action'] ?? '';

switch ($action) {

    case 'valider':
        validerCommande();
        break;

    case 'changer_statut':
        // Réservé à l'admin
        changerStatut();
        break;

    default:
        header("Location: ../views/products/index.php");
        exit();
}

// ─────────────────────────────────────────
// FONCTION 1 : Valider la commande
// ─────────────────────────────────────────
function validerCommande() {
    $user_id = $_SESSION['user_id'];
    $cart    = new Cart();
    $order   = new Order();

    // Vérifier que le panier n'est pas vide
    $items = $cart->getPanier($user_id);
    if (empty($items)) {
        header("Location: ../views/cart/index.php?error=panier_vide");
        exit();
    }

    // Récupérer l'adresse depuis le formulaire POST
    $address = trim($_POST['address'] ?? '');
    if (empty($address)) {
        header("Location: ../views/cart/index.php?error=adresse_manquante");
        exit();
    }

    // Calculer le total
    $total = $cart->getTotal($user_id);

    // Créer la commande (insère dans orders + order_items + décrémente stock)
    $order_id = $order->creer($user_id, $total, $address);

    // Vider le panier après la commande
    $cart->vider($user_id);

    // Rediriger vers la confirmation
    header("Location: ../views/orders/confirmation.php?id=$order_id");
    exit();
}

// ─────────────────────────────────────────
// FONCTION 2 : Changer le statut (admin)
// ─────────────────────────────────────────
function changerStatut() {
    // Vérifier que c'est bien un admin
    if ($_SESSION['user_role'] !== 'admin') {
        header("Location: ../views/products/index.php");
        exit();
    }

    $order_id = (int) ($_GET['order_id'] ?? 0);
    $statut   = $_GET['statut'] ?? '';

    $order = new Order();
    $order->changerStatut($order_id, $statut);

    header("Location: ../views/admin/dashboard.php?section=commandes&updated=1");
    exit();
}
?>

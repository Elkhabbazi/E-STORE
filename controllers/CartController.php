

// Gérera : ajouter, modifier quantité, supprimer, total
<?php
session_start();
require_once '../config/database.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/auth/login.php");
    exit();
}

// Récupérer l'action demandée
$action = $_GET['action'] ?? '';

switch ($action) {

    case 'add':
        ajouterAuPanier();
        break;

    case 'update':
        modifierQuantite();
        break;

    case 'remove':
        supprimerDuPanier();
        break;

    default:
        header("Location: ../views/cart/index.php");
        exit();
}

// ─────────────────────────────────────────
// FONCTION 1 : Ajouter un produit au panier
// ─────────────────────────────────────────
function ajouterAuPanier() {
    $conn       = getConnection();
    $user_id    = $_SESSION['user_id'];
    $product_id = (int) ($_GET['product_id'] ?? 0);

    // Vérifier que le produit existe et est en stock
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND stock > 0");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        header("Location: ../views/products/index.php");
        exit();
    }

    // Vérifier si le produit est déjà dans le panier
    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cartItem) {
        // Produit déjà dans le panier → augmenter la quantité
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
    } else {
        // Produit pas encore dans le panier → l'ajouter
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
        $stmt->execute([$user_id, $product_id]);
    }

    // Rediriger vers la page detail avec message succès
    header("Location: ../views/products/detail.php?id=$product_id&added=1");
    exit();
}

// ─────────────────────────────────────────
// FONCTION 2 : Modifier la quantité
// ─────────────────────────────────────────
function modifierQuantite() {
    $conn       = getConnection();
    $user_id    = $_SESSION['user_id'];
    $product_id = (int) ($_GET['product_id'] ?? 0);
    $quantity   = (int) ($_GET['quantity']   ?? 1);

    if ($quantity <= 0) {
        // Si quantité = 0 ou moins → supprimer du panier
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
    } else {
        // Sinon mettre à jour la quantité
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$quantity, $user_id, $product_id]);
    }

    header("Location: ../views/cart/index.php");
    exit();
}

// ─────────────────────────────────────────
// FONCTION 3 : Supprimer un produit
// ─────────────────────────────────────────
function supprimerDuPanier() {
    $conn       = getConnection();
    $user_id    = $_SESSION['user_id'];
    $product_id = (int) ($_GET['product_id'] ?? 0);

    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);

    header("Location: ../views/cart/index.php");
    exit();
}
?>

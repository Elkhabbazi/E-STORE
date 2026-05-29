<?php
session_start();
require_once '../config/database.php';
require_once '../models/Product.php';

// Vérifier que c'est un admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../views/auth/login.php");
    exit();
}

$action = $_GET['action'] ?? '';

switch ($action) {

    case 'ajouter':
        ajouterProduit();
        break;

    case 'modifier':
        modifierProduit();
        break;

    case 'supprimer':
        supprimerProduit();
        break;

    default:
        header("Location: ../views/admin/dashboard.php");
        exit();
}

// ─────────────────────────────────────────
// FONCTION 1 : Ajouter un produit
// ─────────────────────────────────────────
function ajouterProduit() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/admin/dashboard.php");
        exit();
    }

    // Gérer l'upload de l'image
    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $uploadDir  = '../public/images/';
        $extension  = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imageName  = uniqid('prod_') . '.' . $extension;
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName);
    }

    $product = new Product();
    $product->ajouter([
        'name'        => trim($_POST['name']),
        'description' => trim($_POST['description']),
        'price'       => (float) $_POST['price'],
        'image'       => $imageName,
        'category'    => trim($_POST['category']),
        'stock'       => (int) $_POST['stock'],
    ]);

    header("Location: ../views/admin/dashboard.php?section=produits&added=1");
    exit();
}

// ─────────────────────────────────────────
// FONCTION 2 : Modifier un produit
// ─────────────────────────────────────────
function modifierProduit() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/admin/dashboard.php");
        exit();
    }

    $id      = (int) $_POST['id'];
    $product = new Product();

    // Récupérer l'ancienne image
    $ancien  = $product->getParId($id);
    $imageName = $ancien['image']; // Garder l'ancienne par défaut

    // Si une nouvelle image est uploadée
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = '../public/images/';
        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imageName = uniqid('prod_') . '.' . $extension;
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName);
    }

    $product->modifier($id, [
        'name'        => trim($_POST['name']),
        'description' => trim($_POST['description']),
        'price'       => (float) $_POST['price'],
        'image'       => $imageName,
        'category'    => trim($_POST['category']),
        'stock'       => (int) $_POST['stock'],
    ]);

    header("Location: ../views/admin/dashboard.php?section=produits&updated=1");
    exit();
}

// ─────────────────────────────────────────
// FONCTION 3 : Supprimer un produit
// ─────────────────────────────────────────
function supprimerProduit() {
    $id      = (int) ($_GET['id'] ?? 0);
    $product = new Product();
    $product->supprimer($id);

    header("Location: ../views/admin/dashboard.php?section=produits&deleted=1");
    exit();
}
?>

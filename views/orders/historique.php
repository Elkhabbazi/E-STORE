<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/Order.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$order    = new Order();
$commandes = $order->getCommandesClient($_SESSION['user_id']);

// Couleurs des statuts
$statusColors = [
    'En attente' => '#C9A84C',
    'Confirmée'  => '#4A90D9',
    'Expédiée'   => '#8B6914',
    'Livrée'     => '#4a7c59',
    'Annulée'    => '#c0392b',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Mes Commandes — Dar Al Caftan</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;1,400&family=Raleway:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../../public/css/historique.css">

</head>
<body>
<nav>
  <a class="nav-brand" href="../../index.php">DAR AL CAFTAN</a>
  <div class="nav-links">
    <a href="../products/index.php">Collections</a>
    <a href="../cart/index.php">🛒 Panier</a>
    <a href="../../controllers/AuthController.php?action=logout">Déconnexion</a>
  </div>
</nav>

<div class="container">
  <h1>Mes <em>Commandes</em></h1>
  <p class="subtitle">Suivi de toutes vos commandes passées</p>

  <?php if (empty($commandes)): ?>
    <div class="empty">
      <div class="empty-icon">📦</div>
      <p>Vous n'avez pas encore passé de commande.</p>
      <a href="../products/index.php" style="display:inline-block;margin-top:1rem;font-size:0.65rem;letter-spacing:0.2em;text-transform:uppercase;padding:0.8rem 2rem;background:var(--charcoal);color:var(--ivory);text-decoration:none;">
        Découvrir la collection
      </a>
    </div>
  <?php else: ?>
    <?php foreach ($commandes as $cmd): ?>
    <div class="order-card">
      <div class="order-header">
        <span class="order-num">Commande #<?= str_pad($cmd['id'], 5, '0', STR_PAD_LEFT) ?></span>
        <span class="order-date"><?= date('d/m/Y', strtotime($cmd['created_at'])) ?></span>
        <span class="order-status" style="background:<?= $statusColors[$cmd['status']] ?? '#888' ?>">
          <?= htmlspecialchars($cmd['status']) ?>
        </span>
      </div>
      <div class="order-body">
        <div>
          <p style="font-size:0.7rem;color:#6B6560;margin-bottom:0.2rem;">Adresse de livraison</p>
          <p style="font-size:0.8rem;"><?= htmlspecialchars($cmd['address'] ?? 'Non spécifiée') ?></p>
        </div>
        <div style="text-align:right;">
          <div class="order-total"><?= number_format($cmd['total_price'], 2) ?> <small>DH</small></div>
          <a href="confirmation.php?id=<?= $cmd['id'] ?>" class="btn-detail" style="display:inline-block;margin-top:0.5rem;">
            Voir le détail
          </a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
</body>
</html>

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
<a href="../../index.php" class="nav-logo">
  <svg width="48" height="48" viewBox="0 0 86 86">
    <polygon points="43,4 80,23 80,63 43,82 6,63 6,23" fill="#FFF8F0" stroke="#C4956A" stroke-width="1"/>
    <polygon points="43,13 70,28 70,58 43,73 16,58 16,28" fill="none" stroke="rgba(196,149,106,0.28)" stroke-width="0.6"/>
    <text x="43" y="39" text-anchor="middle" font-family="serif" font-size="15" fill="#8B4513" letter-spacing="2">DAC</text>
    <line x1="28" y1="46" x2="58" y2="46" stroke="#C4956A" stroke-width="0.7"/>
    <text x="43" y="58" text-anchor="middle" font-family="sans-serif" font-size="6" fill="#C4956A" letter-spacing="3">MAROC</text>
    <circle cx="43" cy="4"  r="2.5" fill="#C4956A"/>
    <circle cx="80" cy="23" r="2.5" fill="#C4956A"/>
    <circle cx="80" cy="63" r="2.5" fill="#C4956A"/>
    <circle cx="43" cy="82" r="2.5" fill="#C4956A"/>
    <circle cx="6"  cy="63" r="2.5" fill="#C4956A"/>
    <circle cx="6"  cy="23" r="2.5" fill="#C4956A"/>
  </svg>
  <div class="nav-logo-text">
    DAR AL CAFTAN
    <span>Haute Couture Marocaine</span>
  </div>
</a>
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

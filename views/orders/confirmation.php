<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/Order.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$order_id = (int) ($_GET['id'] ?? 0);
$order    = new Order();
$items    = $order->getDetailCommande($order_id);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Commande confirmée — Dar Al Caftan</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;1,400&family=Raleway:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../../public/css/confirmation.css">


</head>
<body>
<div class="box">
  <div class="check">✦</div>
  <h1 class="title">Commande <em>confirmée</em> !</h1>
  <p class="sub">Merci pour votre confiance. Votre commande a bien été enregistrée.</p>

  <div class="order-num">Commande N° <?= str_pad($order_id, 5, '0', STR_PAD_LEFT) ?></div>

  <?php if (!empty($items)): ?>
  <div class="items-list">
    <?php foreach ($items as $item): ?>
    <div class="item-row">
      <span><?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?></span>
      <span><?= number_format($item['sous_total'], 2) ?> DH</span>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <div class="actions">
    <a href="../products/index.php" class="btn btn-dark">Continuer les achats</a>
    <a href="historique.php" class="btn btn-outline">Mes commandes</a>
  </div>
</div>
</body>
</html>

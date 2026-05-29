<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/Cart.php';
require_once '../../config/csrf.php';

// Si l'utilisateur n'est pas connecté → redirection
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$cart    = new Cart();
$items   = $cart->getPanier($user_id);
$total   = $cart->getTotal($user_id);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mon Panier — Dar Al Caftan</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,400&family=Cinzel:wght@400;500&family=Raleway:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../../public/css/cartIndex.css">

</head>
<body>

<!-- NAV -->
<nav>
  <a href="../../index.php" class="nav-logo">
    <svg width="36" height="36" viewBox="0 0 86 86">
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
  <div class="nav-right">
    <span style="font-size:0.75rem; color:var(--warm-gray);">
      Bonjour, <strong style="color:var(--charcoal);"><?= htmlspecialchars($_SESSION['user_name']) ?></strong>
    </span>
    <a href="../products/index.php" class="btn-sm btn-outline">Collections</a>
    <a href="../../controllers/AuthController.php?action=logout" class="btn-sm btn-outline">Déconnexion</a>
  </div>
</nav>

<!-- PAGE HEADER -->
<div class="page-header">
  <div class="page-header-pattern"></div>
  <div class="page-header-inner">
    <p class="page-eyebrow">Espace client</p>
    <h1 class="page-title">Mon <em>Panier</em></h1>
  </div>
</div>

<!-- CONTENU -->
<div style="padding: 2rem 6rem 0;">
  <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-error">
      <?php if ($_GET['error'] === 'panier_vide'): ?>
        ⚠ Votre panier est vide. Ajoutez des articles avant de commander.
      <?php elseif ($_GET['error'] === 'adresse_manquante'): ?>
        ⚠ Veuillez entrer une adresse de livraison.
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>

<div class="cart-layout">

  <?php if (empty($items)): ?>
    <!-- Panier vide -->
    <div class="empty-cart">
      <div class="empty-ornament">🛒</div>
      <h2 class="empty-title">Votre panier est vide</h2>
      <p class="empty-sub">Découvrez nos créations et ajoutez vos pièces favorites.</p>
      <a href="../products/index.php" class="empty-link">Explorer la collection</a>
    </div>

  <?php else: ?>

    <!-- LISTE DES ARTICLES -->
    <div>
      <div class="cart-items">
        <?php foreach ($items as $item): ?>
        <div class="cart-item">

          <!-- Image -->
          <div class="item-img">
            <?php if (!empty($item['image'])): ?>
              <img src="../../public/images/<?= htmlspecialchars($item['image']) ?>"
                   alt="<?= htmlspecialchars($item['name']) ?>">
            <?php else: ?>
              <span class="item-img-icon">👘</span>
            <?php endif; ?>
          </div>

          <!-- Infos produit -->
          <div class="item-info">
            <p class="item-category"><?= htmlspecialchars($item['category']) ?></p>
            <h3 class="item-name"><?= htmlspecialchars($item['name']) ?></h3>
            <p class="item-price-unit"><?= number_format($item['price'], 2) ?> DH / unité</p>
          </div>

          <!-- Quantité -->
          <div class="qty-control">
            <a href="../../controllers/CartController.php?action=update&product_id=<?= $item['product_id'] ?>&quantity=<?= $item['quantity'] - 1 ?>"
               class="qty-btn" title="Diminuer">−</a>
            <span class="qty-value"><?= $item['quantity'] ?></span>
            <a href="../../controllers/CartController.php?action=update&product_id=<?= $item['product_id'] ?>&quantity=<?= $item['quantity'] + 1 ?>"
               class="qty-btn" title="Augmenter">+</a>
          </div>

          <!-- Sous-total + supprimer -->
          <div class="item-right">
            <div class="item-subtotal">
              <?= number_format($item['sous_total'], 2) ?>
              <small>DH</small>
            </div>
            <a href="../../controllers/CartController.php?action=remove&product_id=<?= $item['product_id'] ?>"
               class="btn-remove">✕ Retirer</a>
          </div>

        </div>
        <?php endforeach; ?>
      </div>

      <!-- Lien continuer les achats -->
      <div style="margin-top: 1.5rem;">
        <a href="../products/index.php" style="font-size:0.65rem; letter-spacing:0.15em; text-transform:uppercase; color:var(--warm-gray); text-decoration:none; border-bottom: 0.5px solid var(--border); padding-bottom:2px;">
          ← Continuer les achats
        </a>
      </div>
    </div>

    <!-- RÉSUMÉ & COMMANDE -->
    <div class="cart-summary">
      <h2 class="summary-title">Résumé de la commande</h2>

      <!-- Détail par article -->
      <?php foreach ($items as $item): ?>
        <div class="summary-line">
          <span><?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?></span>
          <span><?= number_format($item['sous_total'], 2) ?> DH</span>
        </div>
      <?php endforeach; ?>

      <div class="summary-line" style="margin-top:0.5rem; padding-top:0.5rem; border-top:0.5px solid var(--border);">
        <span>Livraison</span>
        <span style="color:var(--gold);">Gratuite</span>
      </div>

      <!-- Total -->
      <div class="summary-total">
        <span class="summary-total-label">Total</span>
        <div class="summary-total-price">
          <?= number_format($total, 2) ?>
          <small>DH TTC</small>
        </div>
      </div>

      <!-- Formulaire de commande -->
      <form method="POST" action="../../controllers/OrderController.php?action=valider">
      <?= champCSRF() ?> 
      <div class="address-section">
          <label class="address-label">Adresse de livraison</label>
          <textarea name="address" class="address-input"
            placeholder="N° rue, quartier, ville, code postal…" required></textarea>
        </div>
        <button type="submit" class="btn-order">
          ✦ Confirmer la commande
        </button>
      </form>

      <a href="../products/index.php" class="btn-continue">
        ← Continuer les achats
      </a>

      <p class="secure-note">🔒 Paiement sécurisé — Livraison gratuite au Maroc</p>
    </div>

  <?php endif; ?>
</div>

</body>
</html>

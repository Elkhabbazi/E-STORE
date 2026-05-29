<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/Visite.php';
(new Visite())->enregistrer('Détail produit');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id   = (int) $_GET['id'];
$conn = getConnection();

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($product['name']) ?> — Dar Al Caftan</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Cinzel:wght@400;500&family=Raleway:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../../public/css/detail.css">

</head>
<body>

<!-- NAV -->
<nav>
  <a href="../../index.php" class="nav-logo">
    <svg width="40" height="40" viewBox="0 0 86 86">
      <polygon points="43,4 80,23 80,63 43,82 6,63 6,23" fill="#FFF8F0" stroke="#C4956A" stroke-width="1"/>
      <polygon points="43,13 70,28 70,58 43,73 16,58 16,28" fill="none" stroke="rgba(196,149,106,0.28)" stroke-width="0.6"/>
      <text x="43" y="39" text-anchor="middle" font-family="'Playfair Display', serif" font-size="15" font-weight="500" fill="#8B4513" letter-spacing="2">DAC</text>
      <line x1="28" y1="46" x2="58" y2="46" stroke="#C4956A" stroke-width="0.7"/>
      <text x="43" y="58" text-anchor="middle" font-family="'DM Sans', sans-serif" font-size="6" font-weight="300" fill="#C4956A" letter-spacing="3">MAROC</text>
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
    <a href="../../index.php">Accueil</a>
    <a href="index.php" class="active">Collections</a>
    <a href="index.php?category=Caftan">Caftans</a>
    <a href="index.php?category=Takchita">Takchitas</a>
    <a href="index.php?category=Jellaba">Jellabas</a>
  </div>

  <div class="nav-actions">
    <?php if (isset($_SESSION['user_id'])): ?>
      <span class="nav-user">Bonjour, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong></span>
      <a href="../cart/index.php" class="btn-outline-sm">🛒 Panier</a>
      <?php if ($_SESSION['user_role'] === 'admin'): ?>
        <a href="../admin/dashboard.php" class="btn-gold-sm">Dashboard</a>
      <?php endif; ?>
      <a href="../../controllers/AuthController.php?action=logout" class="btn-outline-sm">Déconnexion</a>
    <?php else: ?>
      <a href="../auth/login.php" class="btn-outline-sm">Connexion</a>
      <a href="../auth/register.php" class="btn-gold-sm">S'inscrire</a>
    <?php endif; ?>
  </div>
</nav>

<!-- BREADCRUMB -->
<div class="breadcrumb-bar">
  <a href="index.php">Collections</a>
  <span class="sep">›</span>
  <a href="index.php?category=<?= urlencode($product['category']) ?>"><?= htmlspecialchars($product['category']) ?></a>
  <span class="sep">›</span>
  <span class="current"><?= htmlspecialchars($product['name']) ?></span>
</div>

<!-- MESSAGES -->
<?php if (isset($_GET['added'])): ?>
  <div class="msg-bar msg-success">✓ &nbsp; Produit ajouté au panier avec succès.</div>
<?php endif; ?>
<?php if (isset($_GET['error'])): ?>
  <div class="msg-bar msg-error">Vous devez être connecté pour ajouter un article au panier.</div>
<?php endif; ?>

<!-- DETAIL -->
<div class="detail-wrapper">

  <!-- Image -->
  <div class="detail-image-col">
    <?php if (!empty($product['image'])): ?>
      <div class="detail-image-frame">
        <div class="detail-image-pattern"></div>
        <?php if ($product['stock'] <= 0): ?>
          <span class="detail-badge badge-rupture">Rupture</span>
        <?php endif; ?>
        <img src="../../public/images/<?= htmlspecialchars($product['image']) ?>"
             alt="<?= htmlspecialchars($product['name']) ?>">
      </div>
    <?php else: ?>
      <div class="detail-no-image">
        <svg viewBox="0 0 24 24"><path d="M12 2C9 2 7 5 7 8c0 4 5 12 5 12s5-8 5-12c0-3-2-6-5-6z"/></svg>
      </div>
    <?php endif; ?>
  </div>

  <!-- Infos -->
  <div class="detail-info-col">
    <p class="detail-eyebrow"><?= htmlspecialchars($product['category']) ?></p>
    <h1 class="detail-title"><?= htmlspecialchars($product['name']) ?></h1>

    <p class="detail-price"><?= number_format($product['price'], 2) ?> DH</p>
    <p class="detail-price-note">Toutes taxes comprises</p>

    <div class="detail-divider"></div>

    <?php if (!empty($product['description'])): ?>
      <p class="detail-description"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
    <?php endif; ?>

    <div class="detail-specs">
      <p class="detail-specs-title">Caractéristiques</p>
      <div class="spec-row">
        <span class="spec-key">Catégorie</span>
        <span class="spec-val"><?= htmlspecialchars($product['category']) ?></span>
      </div>
      <?php if (!empty($product['occasion'])): ?>
      <div class="spec-row">
        <span class="spec-key">Occasion</span>
        <span class="spec-val"><?= htmlspecialchars($product['occasion']) ?></span>
      </div>
      <?php endif; ?>
      <?php if (!empty($product['region'])): ?>
      <div class="spec-row">
        <span class="spec-key">Région</span>
        <span class="spec-val"><?= htmlspecialchars($product['region']) ?></span>
      </div>
      <?php endif; ?>
      <?php if (!empty($product['couleur'])): ?>
      <div class="spec-row">
        <span class="spec-key">Couleur</span>
        <span class="spec-val"><?= htmlspecialchars($product['couleur']) ?></span>
      </div>
      <?php endif; ?>
      <?php if (!empty($product['taille'])): ?>
      <div class="spec-row">
        <span class="spec-key">Taille</span>
        <span class="spec-val"><?= htmlspecialchars($product['taille']) ?></span>
      </div>
      <?php endif; ?>
    </div>

    <?php if ($product['stock'] > 0): ?>
      <div class="detail-stock stock-ok">
        <span class="stock-dot"></span>
        En stock — <?= $product['stock'] ?> pièce<?= $product['stock'] > 1 ? 's' : '' ?> disponible<?= $product['stock'] > 1 ? 's' : '' ?>
      </div>
    <?php else: ?>
      <div class="detail-stock stock-out">
        <span class="stock-dot"></span>
        Rupture de stock
      </div>
    <?php endif; ?>

    <div class="detail-actions">
      <?php if ($product['stock'] > 0): ?>
        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="../../controllers/CartController.php?action=add&product_id=<?= $product['id'] ?>" class="btn-primary">
            Ajouter au panier
          </a>
        <?php else: ?>
          <a href="../auth/login.php" class="btn-primary">
            Connexion pour acheter
          </a>
        <?php endif; ?>
      <?php else: ?>
        <span class="btn-disabled-detail">Indisponible</span>
      <?php endif; ?>
      <a href="index.php" class="btn-secondary-detail">← Retour aux collections</a>
    </div>
  </div>

</div>

<!-- FOOTER -->
<footer>
  <span class="footer-logo">DAR AL CAFTAN</span>
  <span class="footer-copy">© 2026 — Haute Couture Marocaine</span>
</footer>

</body>
</html>
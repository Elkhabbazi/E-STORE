<?php
session_start();
require_once '../../config/database.php';

$conn = getConnection();

// ── Recherche & filtres ──────────────────────────────────────────
$search   = trim($_GET['search']   ?? '');
$category = trim($_GET['category'] ?? '');
$sort     = trim($_GET['sort']     ?? '');

$where  = ["1=1"];
$params = [];

if ($search !== '') {
    $where[]  = "(name LIKE ? OR category LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category !== '') {
    $where[]  = "category = ?";
    $params[] = $category;
}

// Tri (compatible PHP 7)
switch ($sort) {
    case 'price_asc':  $orderBy = 'price ASC';       break;
    case 'price_desc': $orderBy = 'price DESC';      break;
    case 'newest':     $orderBy = 'created_at DESC'; break;
    default:           $orderBy = 'created_at DESC'; break;
}

$sql  = "SELECT * FROM products WHERE " . implode(" AND ", $where) . " ORDER BY $orderBy";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Catégories disponibles pour le filtre
$catStmt = $conn->query("SELECT DISTINCT category FROM products ORDER BY category");
$categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nos Produits — Dar Al Caftan</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Cinzel:wght@400;500&family=Raleway:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../../public/css/acceuil.css">

</head>
<body>

<!-- ── NAV ── -->
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

<!-- ── PAGE HEADER ── -->
<div class="page-header">
  <div class="page-header-pattern"></div>
  <div class="page-header-inner">
    <p class="page-eyebrow">Notre Boutique</p>
    <h1 class="page-title">
      <?php if ($search !== ''): ?>
        Résultats pour <em>"<?= htmlspecialchars($search) ?>"</em>
      <?php elseif ($category !== ''): ?>
        Collection <em><?= htmlspecialchars($category) ?></em>
      <?php else: ?>
        Toutes nos <em>créations</em>
      <?php endif; ?>
    </h1>
    <p class="page-count"><?= count($products) ?> pièce<?= count($products) > 1 ? 's' : '' ?> disponible<?= count($products) > 1 ? 's' : '' ?></p>
  </div>
</div>

<!-- ── SEARCH & FILTERS ── -->
<div class="filters-bar">
  <form method="GET" action="index.php" style="display:flex; gap:1rem; align-items:center; flex-wrap:wrap; flex:1;">

    <div class="search-wrap">
      <input type="text" name="search" class="search-input"
        placeholder="Rechercher un caftan, takchita…"
        value="<?= htmlspecialchars($search) ?>">
      <button type="submit" class="search-btn">Chercher</button>
    </div>

    <select name="category" class="filter-select" onchange="this.form.submit()">
      <option value="">Toutes catégories</option>
      <?php foreach ($categories as $cat): ?>
        <option value="<?= htmlspecialchars($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>>
          <?= htmlspecialchars($cat) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <select name="sort" class="filter-select" onchange="this.form.submit()">
      <option value="">Trier par</option>
      <option value="newest"     <?= $sort === 'newest'     ? 'selected' : '' ?>>Nouveautés</option>
      <option value="price_asc"  <?= $sort === 'price_asc'  ? 'selected' : '' ?>>Prix croissant</option>
      <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Prix décroissant</option>
    </select>

    <?php if ($search !== '' || $category !== '' || $sort !== ''): ?>
      <a href="index.php" class="clear-link">✕ Effacer les filtres</a>
    <?php endif; ?>

  </form>
</div>

<!-- ── RESULTS ── -->
<div class="results-area">

  <!-- Pills filtres actifs -->
  <?php if ($search !== '' || $category !== ''): ?>
  <div class="active-filters">
    <?php if ($search !== ''): ?>
      <span class="filter-pill">Recherche : "<?= htmlspecialchars($search) ?>" <a href="index.php?category=<?= urlencode($category) ?>">✕</a></span>
    <?php endif; ?>
    <?php if ($category !== ''): ?>
      <span class="filter-pill">Catégorie : <?= htmlspecialchars($category) ?> <a href="index.php?search=<?= urlencode($search) ?>">✕</a></span>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <div class="products-grid">

    <?php if (empty($products)): ?>
      <div class="empty-state">
        <div class="empty-ornament">✦</div>
        <h2 class="empty-title">Aucune pièce trouvée</h2>
        <p class="empty-sub">Essayez un autre terme de recherche ou explorez toute notre collection.</p>
        <a href="index.php" class="empty-link">Voir toutes les créations</a>
      </div>

    <?php else: ?>
      <?php foreach ($products as $product): ?>
<div class="product-card">
        <div class="product-img">
          <div class="product-img-pattern"></div>

          <?php if (!empty($product['image'])): ?>
            <img src="../../public/images/<?= htmlspecialchars($product['image']) ?>"
                 alt="<?= htmlspecialchars($product['name']) ?>">
          <?php else: ?>
            <div class="product-img-icon">
              <svg viewBox="0 0 24 24"><path d="M12 2C9 2 7 5 7 8c0 4 5 12 5 12s5-8 5-12c0-3-2-6-5-6z"/></svg>
            </div>
          <?php endif; ?>

          <?php if ($product['stock'] <= 0): ?>
            <span class="product-badge badge-rupture">Rupture</span>
          <?php endif; ?>
        </div>

        <div class="product-info">
          <p class="product-category"><?= htmlspecialchars($product['category']) ?></p>
          <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
          <p class="product-price">
            <?= number_format($product['price'], 2) ?> DH
            <small>TTC</small>
          </p>

<div class="product-actions" onclick="event.stopPropagation()">
              <a href="detail.php?id=<?= $product['id'] ?>" class="btn-detail">Voir détail</a>

            <?php if (isset($_SESSION['user_id']) && $product['stock'] > 0): ?>
              <a href="../../controllers/CartController.php?action=add&product_id=<?= $product['id'] ?>"
                 class="btn-cart">+ Panier</a>
            <?php elseif ($product['stock'] <= 0): ?>
              <span class="btn-cart-disabled">Indisponible</span>
            <?php else: ?>
              <a href="../auth/login.php" class="btn-detail" style="background:rgba(201,168,76,0.1); border-color:var(--gold); color:var(--gold-dark);">Connexion</a>
            <?php endif; ?>
          </div>
        </div>

        </div>
      <?php endforeach; ?>
    <?php endif; ?>

  </div>
</div>

<!-- ── FOOTER ── -->
<footer>
  <span class="footer-logo">DAR AL CAFTAN</span>
  <span class="footer-copy">© 2026 — Haute Couture Marocaine</span>
</footer>

</body>
</html>
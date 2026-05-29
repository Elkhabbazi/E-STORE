<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/Visite.php';
(new Visite())->enregistrer('Collection Hommes');

$conn = getConnection();

// Récupérer les filtres depuis l'URL
$category  = $_GET['category']  ?? '';
$sort      = $_GET['sort']      ?? '';
$prix_min  = $_GET['prix_min']  ?? '';
$prix_max  = $_GET['prix_max']  ?? '';

// Catégories femmes uniquement
$categoriesHommes = ['Jabador', 'Jellaba Homme'];

// Construction de la requête avec filtres
$where  = ["category IN ('Jabador', 'Jellaba Homme')", "stock > 0"];
$params = [];

if ($category !== '') {
    $where[]  = "category = ?";
    $params[] = $category;
}
if ($prix_min !== '') {
    $where[]  = "price >= ?";
    $params[] = (float) $prix_min;
}
if ($prix_max !== '') {
    $where[]  = "price <= ?";
    $params[] = (float) $prix_max;
}

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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Collection Hommes — Dar Al Caftan</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;1,400&family=Cinzel:wght@400&family=Raleway:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../../public/css/hommes.css">
</head>
<body>

<!-- NAV -->
<nav>
  <a href="../../index.php" class="nav-brand">DAR AL CAFTAN</a>
  <div class="nav-links">
    <a href="index.php">Tous les produits</a>
<a href="femmes.php">Collection Femmes</a>
<a href="hommes.php" class="active">Collection Hommes</a>  ← CORRECT
  </div>
  <div class="nav-actions">
    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="../cart/index.php" class="btn-nav btn-outline">🛒 Panier</a>
      <a href="../../controllers/AuthController.php?action=logout" class="btn-nav btn-outline">Déconnexion</a>
    <?php else: ?>
      <a href="../auth/login.php" class="btn-nav btn-outline">Connexion</a>
      <a href="../auth/register.php" class="btn-nav btn-gold">S'inscrire</a>
    <?php endif; ?>
  </div>
</nav>

<!-- PAGE HEADER -->
<div class="page-header">
  <div class="page-header-pattern"></div>
  <div class="page-header-inner">
    <p class="page-eyebrow">Dar Al Caftan</p>
    <h1 class="page-title">Collection <em>Hommes</em></h1>
    <p class="page-sub">Jabador · Jellaba Homme</p>
  </div>
</div>

<!-- CATEGORY TABS -->
<div class="cat-tabs">
  <a href="hommes.php" class="cat-tab <?= $category===''?'active':'' ?>">Tout</a>
  <?php foreach ($categoriesHommes as $cat): ?>
    <a href="?category=<?= urlencode($cat) ?>&sort=<?= $sort ?>"
       class="cat-tab <?= $category===$cat?'active':'' ?>">
      <?= htmlspecialchars($cat) ?>
    </a>
  <?php endforeach; ?>
</div>

<!-- LAYOUT -->
<div class="page-layout">

  <!-- FILTRES -->
  <aside class="filters-sidebar">
    <p class="filter-title">Filtrer</p>
    <form method="GET" action="hommes.php">
      <?php if ($category): ?>
        <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
      <?php endif; ?>

      <div class="filter-group">
        <label class="filter-label">Trier par</label>
        <select name="sort" class="filter-select">
          <option value="">Par défaut</option>
          <option value="newest"     <?= $sort==='newest'    ?'selected':'' ?>>Nouveautés</option>
          <option value="price_asc"  <?= $sort==='price_asc' ?'selected':'' ?>>Prix croissant</option>
          <option value="price_desc" <?= $sort==='price_desc'?'selected':'' ?>>Prix décroissant</option>
        </select>
      </div>

      <div class="filter-group">
        <label class="filter-label">Budget (DH)</label>
        <div class="price-row">
          <input type="number" name="prix_min" class="price-input"
                 placeholder="Min" value="<?= htmlspecialchars($prix_min) ?>">
          <input type="number" name="prix_max" class="price-input"
                 placeholder="Max" value="<?= htmlspecialchars($prix_max) ?>">
        </div>
      </div>

      <button type="submit" class="btn-filter">Appliquer</button>
     <a href="hommes.php" class="btn-reset">Réinitialiser</a>
    </form>
  </aside>

  <!-- PRODUITS -->
  <div class="products-area">
    <div class="results-bar">
      <p class="results-count">
        <strong><?= count($products) ?></strong> création<?= count($products) > 1 ? 's' : '' ?> trouvée<?= count($products) > 1 ? 's' : '' ?>
      </p>
    </div>

    <div class="products-grid">
      <?php if (empty($products)): ?>
        <div class="empty">
          <div class="empty-icon">👘</div>
          <p>Aucun produit trouvé avec ces critères.</p>
          <a href="hommes.php" style="display:inline-block;margin-top:1rem;font-size:0.65rem;letter-spacing:0.15em;text-transform:uppercase;color:var(--gold);">
            Voir toute la collection →
          </a>
        </div>
      <?php else: ?>
        <?php foreach ($products as $p): ?>
        <div class="product-card">
          <div class="product-img">
            <?php if (!empty($p['image'])): ?>
              <img src="../../public/images/<?= htmlspecialchars($p['image']) ?>"
                   alt="<?= htmlspecialchars($p['name']) ?>">
            <?php else: ?>
              <div class="product-img-icon">
                <svg viewBox="0 0 24 24"><path d="M12 2C9 2 7 5 7 8c0 4 5 12 5 12s5-8 5-12c0-3-2-6-5-6z"/></svg>
              </div>
            <?php endif; ?>
            <?php if (!empty($p['is_new'])): ?>
              <span class="product-badge">Nouveau</span>
            <?php endif; ?>
            <div class="product-overlay">
              <a href="detail.php?id=<?= $p['id'] ?>" class="overlay-btn">Voir le détail</a>
              <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'client'): ?>
                <a href="../../controllers/CartController.php?action=add&product_id=<?= $p['id'] ?>"
                   class="overlay-btn overlay-btn-gold">+ Panier</a>
              <?php else: ?>
                <a href="../auth/login.php" class="overlay-btn overlay-btn-gold">+ Panier</a>
              <?php endif; ?>
            </div>
          </div>
          <div class="product-info">
            <p class="product-category"><?= htmlspecialchars($p['category']) ?></p>
            <h3 class="product-name">
              <a href="detail.php?id=<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></a>
            </h3>
            <p class="product-price"><?= number_format($p['price'], 2) ?> DH <span>TTC</span></p>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

</div>
</body>
</html>
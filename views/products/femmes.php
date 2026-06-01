<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/Visite.php';
(new Visite())->enregistrer('Collection Femmes');

$conn = getConnection();

$category  = trim($_GET['category']  ?? '');
$sort      = trim($_GET['sort']      ?? '');
$prix_min  = trim($_GET['prix_min']  ?? '');
$prix_max  = trim($_GET['prix_max']  ?? '');
$occasion  = trim($_GET['occasion']  ?? '');
$couleur   = trim($_GET['couleur']   ?? '');

$categoriesFemmes = ['Caftan', 'Takchita', 'Jellaba Femme', 'Chaussures Femme'];

// Requête principale — produits femmes
// Chaussures Ceremonie inclus MAIS on filtre par occasion pour exclure les hommes
$where  = ["category IN ('Caftan','Takchita','Jellaba Femme','Chaussures Femme','Babouche Femme','Sandales')", "stock > 0"];
$params = [];

if ($category !== '') {
    if ($category === 'Chaussures Femme') {
        // Chaussures femmes = Chaussures Femme + Babouche Femme + Sandales
        // On exclut Chaussures Homme et Chaussures Ceremonie pour hommes
        $where  = ["category IN ('Chaussures Femme','Babouche Femme','Sandales')", "stock > 0"];
        $params = [];
    } else {
        $where  = ["category = ?", "stock > 0"];
        $params = [$category];
    }
}

if ($prix_min !== '')  { $where[] = "price >= ?";      $params[] = (float)$prix_min; }
if ($prix_max !== '')  { $where[] = "price <= ?";      $params[] = (float)$prix_max; }
if ($occasion !== '')  { $where[] = "occasion LIKE ?"; $params[] = "%$occasion%"; }
if ($couleur  !== '')  { $where[] = "couleur LIKE ?";  $params[] = "%$couleur%"; }

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

$occasions = $conn->query("
    SELECT DISTINCT occasion FROM products
    WHERE occasion IS NOT NULL AND occasion != ''
    AND category IN ('Caftan','Takchita','Jellaba Femme','Chaussures Femme','Babouche Femme','Sandales')
    ORDER BY occasion
")->fetchAll(PDO::FETCH_COLUMN);

$couleurs = $conn->query("
    SELECT DISTINCT couleur FROM products
    WHERE couleur IS NOT NULL AND couleur != ''
    AND category IN ('Caftan','Takchita','Jellaba Femme','Chaussures Femme','Babouche Femme','Sandales')
    ORDER BY couleur
")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Collection Femmes — Dar Al Caftan</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;1,400&family=Cinzel:wght@400&family=Raleway:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../../public/css/acceuil.css">
<link rel="stylesheet" href="../../public/css/femmes.css">

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
    <a href="index.php">Toutes les Collections</a>
    <a href="femmes.php" class="active">Collection Femmes</a>
    <a href="hommes.php">Collection Hommes</a>
    <a href="chaussures.php">Chaussures</a>
  </div>
  <div class="nav-actions">
    <?php if (isset($_SESSION['user_id'])): ?>
      <span class="nav-user">Bonjour, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong></span>
      <a href="../cart/index.php" class="btn-outline-sm">🛒 Panier</a>
      <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
        <a href="../admin/dashboard.php" class="btn-gold-sm">Dashboard</a>
      <?php endif; ?>
      <a href="../../controllers/AuthController.php?action=logout" class="btn-outline-sm">Déconnexion</a>
    <?php else: ?>
      <a href="../auth/login.php" class="btn-outline-sm">Connexion</a>
      <a href="../auth/register.php" class="btn-gold-sm">S'inscrire</a>
    <?php endif; ?>
</div>
</nav>

<div class="page-header">
  <div class="page-header-pattern"></div>
  <div class="page-header-inner">
    <p class="page-eyebrow">Dar Al Caftan</p>
    <h1 class="page-title">Collection <em>Femmes</em></h1>
    <p class="page-sub">Caftan · Takchita · Jellaba Femme · Chaussures Femme</p>
  </div>
</div>

<div class="cat-tabs">
  <a href="femmes.php" class="cat-tab <?= $category===''?'active':'' ?>">Tout</a>
  <?php foreach ($categoriesFemmes as $cat): ?>
    <a href="?category=<?= urlencode($cat) ?>&sort=<?= urlencode($sort) ?>"
       class="cat-tab <?= $category===$cat?'active':'' ?>">
      <?= htmlspecialchars($cat) ?>
    </a>
  <?php endforeach; ?>
</div>

<div class="page-layout">
  <aside class="filters-sidebar">
    <p class="filter-title">Filtrer</p>
    <form method="GET" action="femmes.php">
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
          <input type="number" name="prix_min" class="price-input" placeholder="Min" value="<?= htmlspecialchars($prix_min) ?>">
          <input type="number" name="prix_max" class="price-input" placeholder="Max" value="<?= htmlspecialchars($prix_max) ?>">
        </div>
      </div>

      <?php if (!empty($occasions)): ?>
      <div class="filter-group">
        <label class="filter-label">Occasion</label>
        <select name="occasion" class="filter-select">
          <option value="">Toutes occasions</option>
          <?php foreach ($occasions as $occ): ?>
            <option value="<?= htmlspecialchars($occ) ?>" <?= $occasion===$occ?'selected':'' ?>><?= htmlspecialchars($occ) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <?php endif; ?>

      <?php if (!empty($couleurs)): ?>
      <div class="filter-group">
        <label class="filter-label">Couleur</label>
        <select name="couleur" class="filter-select">
          <option value="">Toutes couleurs</option>
          <?php foreach ($couleurs as $coul): ?>
            <option value="<?= htmlspecialchars($coul) ?>" <?= $couleur===$coul?'selected':'' ?>><?= htmlspecialchars($coul) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <?php endif; ?>

      <button type="submit" class="btn-filter">Appliquer</button>
      <a href="femmes.php" class="btn-reset">Réinitialiser</a>
    </form>
  </aside>

  <div class="products-area">
    <div class="results-bar">
      <p class="results-count">
        <strong><?= count($products) ?></strong> article<?= count($products)>1?'s':'' ?> trouvé<?= count($products)>1?'s':'' ?>
      </p>
    </div>
    <div class="products-grid">
      <?php if (empty($products)): ?>
        <div class="empty">
          <div class="empty-icon">👘</div>
          <p>Aucun produit trouvé avec ces critères.</p>
          <a href="femmes.php" style="display:inline-block;margin-top:1rem;font-size:.65rem;letter-spacing:.15em;text-transform:uppercase;color:var(--gold);">
            Voir toute la collection →
          </a>
        </div>
      <?php else: ?>
        <?php foreach ($products as $p): ?>
        <div class="product-card">
          <div class="product-img">
            <?php if (!empty($p['image'])): ?>
<img src="../../public/images/<?= rawurlencode($p['image']) ?>" 
     alt="<?= htmlspecialchars($p['name']) ?>">
                 <?php else: ?>
              <div class="product-img-icon"><svg viewBox="0 0 24 24"><path d="M12 2C9 2 7 5 7 8c0 4 5 12 5 12s5-8 5-12c0-3-2-6-5-6z"/></svg></div>
            <?php endif; ?>
            <div class="product-overlay">
              <a href="detail.php?id=<?= $p['id'] ?>" class="overlay-btn">Voir le détail</a>
              <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role']==='client'): ?>
                <a href="../../controllers/CartController.php?action=add&product_id=<?= $p['id'] ?>" class="overlay-btn overlay-btn-gold">+ Panier</a>
              <?php else: ?>
                <a href="../auth/login.php" class="overlay-btn overlay-btn-gold">+ Panier</a>
              <?php endif; ?>
            </div>
          </div>
          <div class="product-info">
            <p class="product-category"><?= htmlspecialchars($p['category']) ?></p>
            <h3 class="product-name"><a href="detail.php?id=<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></a></h3>
            <?php if (!empty($p['couleur'])): ?>
              <p style="font-size:.62rem;color:#8B7355;margin:.2rem 0;">🎨 <?= htmlspecialchars($p['couleur']) ?></p>
            <?php endif; ?>
            <?php if (!empty($p['taille'])): ?>
              <p style="font-size:.62rem;color:#8B7355;margin:.2rem 0;">📐 <?= htmlspecialchars($p['taille']) ?></p>
            <?php endif; ?>
            <p class="product-price"><?= number_format($p['price'],2) ?> DH <span>TTC</span></p>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>
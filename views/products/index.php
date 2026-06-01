<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/Product.php';
require_once '../../models/Visite.php';
(new Visite())->enregistrer('Catalogue');

// ✅ CORRIGÉ : utilise le modèle (MVC propre), plus de $conn direct
$productModel = new Product();

$search   = trim($_GET['search']   ?? '');
$category = trim($_GET['category'] ?? '');
$sort     = trim($_GET['sort']     ?? '');
$occasion = trim($_GET['occasion'] ?? '');
$couleur  = trim($_GET['couleur']  ?? '');
$region   = trim($_GET['region']   ?? '');
$prix_min = trim($_GET['prix_min'] ?? '');
$prix_max = trim($_GET['prix_max'] ?? '');

$products   = $productModel->getTous($search, $category, $sort, $occasion, $couleur, $region, $prix_min, $prix_max);
$categories = $productModel->getCategories();
$occasions  = $productModel->getOccasions();
$couleurs   = $productModel->getCouleurs();
$regions    = $productModel->getRegions();

$filtresActifs = ($search !== '' || $category !== '' || $occasion !== '' || $couleur !== '' || $region !== '' || $prix_min !== '' || $prix_max !== '' || $sort !== '');
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
<style>
.filters-bar{padding:1.2rem 4%;background:#FAF7F0;border-bottom:1px solid rgba(196,149,106,.2)}
.filters-bar form{display:flex;flex-wrap:wrap;gap:.75rem;align-items:flex-end}
.filter-group{display:flex;flex-direction:column;gap:.3rem}
.filter-group label{font-family:'Raleway',sans-serif;font-size:.58rem;letter-spacing:.15em;text-transform:uppercase;color:#8B7355}
.filter-select,.filter-input{font-family:'Raleway',sans-serif;font-size:.73rem;padding:.45rem .7rem;border:1px solid rgba(196,149,106,.4);background:#fff;color:#1A1714;border-radius:2px;min-width:130px}
.filter-select:focus,.filter-input:focus{outline:none;border-color:#C4956A}
.search-wrap{display:flex}
.search-input{font-family:'Raleway',sans-serif;font-size:.73rem;padding:.45rem 1rem;border:1px solid rgba(196,149,106,.4);border-right:none;background:#fff;color:#1A1714;border-radius:2px 0 0 2px;width:200px}
.search-input:focus{outline:none;border-color:#C4956A}
.search-btn{font-family:'Raleway',sans-serif;font-size:.63rem;letter-spacing:.12em;text-transform:uppercase;padding:.45rem .9rem;background:#C4956A;color:#fff;border:none;border-radius:0 2px 2px 0;cursor:pointer}
.price-row{display:flex;gap:.4rem}.price-row .filter-input{min-width:75px;width:80px}
.btn-filter{font-family:'Raleway',sans-serif;font-size:.63rem;letter-spacing:.12em;text-transform:uppercase;padding:.45rem 1.1rem;background:#1A1714;color:#FAF7F0;border:none;border-radius:2px;cursor:pointer;align-self:flex-end}
.btn-filter:hover{background:#C4956A}
.clear-link{font-family:'Raleway',sans-serif;font-size:.63rem;letter-spacing:.1em;color:#8B7355;text-decoration:none;align-self:flex-end;padding-bottom:.4rem;border-bottom:1px solid currentColor}
.clear-link:hover{color:#c0392b}
.active-filters{display:flex;flex-wrap:wrap;gap:.4rem;padding:.6rem 4%;background:#FAF7F0}
.filter-pill{display:inline-flex;align-items:center;gap:.3rem;font-family:'Raleway',sans-serif;font-size:.62rem;letter-spacing:.08em;padding:.25rem .65rem;background:rgba(196,149,106,.1);border:1px solid rgba(196,149,106,.3);border-radius:20px;color:#8B6914}
.filter-pill a{color:inherit;text-decoration:none;font-weight:bold;margin-left:.2rem}.filter-pill a:hover{color:#c0392b}
.product-meta{font-size:.6rem;color:#8B7355;margin:.25rem 0;line-height:1.6}
</style>
</head>
<body>

<nav>
  <a href="../../index.php" class="nav-logo">
    <svg width="40" height="40" viewBox="0 0 86 86">
      <polygon points="43,4 80,23 80,63 43,82 6,63 6,23" fill="#FFF8F0" stroke="#C4956A" stroke-width="1"/>
      <polygon points="43,13 70,28 70,58 43,73 16,58 16,28" fill="none" stroke="rgba(196,149,106,0.28)" stroke-width="0.6"/>
      <text x="43" y="39" text-anchor="middle" font-family="serif" font-size="15" fill="#8B4513" letter-spacing="2">DAC</text>
      <line x1="28" y1="46" x2="58" y2="46" stroke="#C4956A" stroke-width="0.7"/>
      <text x="43" y="58" text-anchor="middle" font-family="sans-serif" font-size="6" fill="#C4956A" letter-spacing="3">MAROC</text>
      <circle cx="43" cy="4"  r="2.5" fill="#C4956A"/><circle cx="80" cy="23" r="2.5" fill="#C4956A"/>
      <circle cx="80" cy="63" r="2.5" fill="#C4956A"/><circle cx="43" cy="82" r="2.5" fill="#C4956A"/>
      <circle cx="6"  cy="63" r="2.5" fill="#C4956A"/><circle cx="6"  cy="23" r="2.5" fill="#C4956A"/>
    </svg>
    <div class="nav-logo-text">DAR AL CAFTAN<span>Haute Couture Marocaine</span></div>
  </a>
  <div class="nav-links">
    <a href="index.php" class="active">Tous les Collections</a>
    <a href="femmes.php">Collection Femmes</a>
    <a href="hommes.php">Collection Hommes</a>
    <a href="chaussures.php">Chaussures</a>
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
<!-- ── RECHERCHE PAR IMAGE ── -->
<div style="text-align:center; padding:1.5rem 4%; background:#FAF7F0; border-bottom:1px solid rgba(196,149,106,0.2);">
  <p style="font-size:0.55rem; letter-spacing:0.4em; text-transform:uppercase; color:#C4956A; margin-bottom:0.8rem;">
    ✨ Recherche intelligente
  </p>
  <p style="font-size:0.75rem; color:#6B6560; margin-bottom:1.2rem;">
    Prenez une photo de votre tenue et trouvez des créations similaires
  </p>

  <label for="image-upload" style="display:inline-block; padding:0.8rem 2rem;
    background:#1A1714; color:#FAF7F0; font-family:'Raleway',sans-serif;
    font-size:0.65rem; letter-spacing:0.2em; text-transform:uppercase;
    cursor:pointer; transition:background 0.2s;">
    📷 Rechercher par photo
  </label>
  <input type="file" id="image-upload" accept="image/*" capture="environment"
    style="display:none;" onchange="rechercherParImage(this)">

  <div id="img-preview" style="display:none; margin-top:1rem;">
    <img id="img-preview-src" src="" alt="Preview"
      style="max-width:180px; max-height:180px; object-fit:cover; border:1px solid rgba(196,149,106,0.3);">
  </div>

  <div id="img-loading" style="display:none; margin-top:1rem; font-size:0.8rem; color:#6B6560;">
    ⏳ Analyse de votre photo en cours...
  </div>

  <div id="img-detected" style="display:none; margin-top:1rem; padding:1rem;
    background:rgba(201,168,76,0.08); border:0.5px solid #C9A84C;
    font-size:0.75rem; max-width:400px; margin-left:auto; margin-right:auto; text-align:left; line-height:1.8;">
  </div>
</div>

<!-- Résultats produits similaires -->
<div id="similar-results" style="display:none; padding:2rem 4%;">
  <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.5rem; font-weight:300; margin-bottom:1.5rem; padding-bottom:0.8rem; border-bottom:0.5px solid rgba(196,149,106,0.25);">
    Créations <em style="color:#C9A84C; font-style:italic;">similaires</em>
  </h2>
  <div id="similar-grid" class="products-grid"></div>
  <div style="text-align:center; margin-top:1.5rem;">
    <button onclick="document.getElementById('similar-results').style.display='none'"
      style="font-family:'Raleway',sans-serif; font-size:0.65rem; letter-spacing:0.2em; text-transform:uppercase;
      padding:0.7rem 2rem; background:transparent; border:0.5px solid #1A1714; color:#1A1714; cursor:pointer;">
      ✕ Fermer les résultats
    </button>
  </div>
</div>
<div class="page-header">
  <div class="page-header-pattern"></div>
  <div class="page-header-inner">
    <p class="page-eyebrow">Notre Boutique</p>
    <h1 class="page-title">
      <?php if ($search !== ''): ?>Résultats pour <em>"<?= htmlspecialchars($search) ?>"</em>
      <?php elseif ($category !== ''): ?>Collection <em><?= htmlspecialchars($category) ?></em>
      <?php else: ?>Toutes nos <em>créations</em><?php endif; ?>
    </h1>
    <p class="page-count"><?= count($products) ?> pièce<?= count($products)>1?'s':'' ?> disponible<?= count($products)>1?'s':'' ?></p>
  </div>
</div>

<!-- FILTRES -->
<div class="filters-bar">
  <form method="GET" action="index.php">
    <div class="filter-group">
      <label>Recherche</label>
      <div class="search-wrap">
        <input type="text" name="search" class="search-input" placeholder="Caftan, takchita…" value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="search-btn">Chercher</button>
      </div>
    </div>
    <div class="filter-group">
      <label>Catégorie</label>
      <select name="category" class="filter-select">
        <option value="">Toutes</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= htmlspecialchars($cat) ?>" <?= $category===$cat?'selected':'' ?>><?= htmlspecialchars($cat) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="filter-group">
      <label>Occasion</label>
      <select name="occasion" class="filter-select">
        <option value="">Toutes</option>
        <?php foreach ($occasions as $occ): ?>
          <option value="<?= htmlspecialchars($occ) ?>" <?= $occasion===$occ?'selected':'' ?>><?= htmlspecialchars($occ) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="filter-group">
      <label>Région</label>
      <select name="region" class="filter-select">
        <option value="">Toutes</option>
        <?php foreach ($regions as $r): ?>
          <option value="<?= htmlspecialchars($r) ?>" <?= $region===$r?'selected':'' ?>><?= htmlspecialchars($r) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="filter-group">
      <label>Budget (DH)</label>
      <div class="price-row">
        <input type="number" name="prix_min" class="filter-input" placeholder="Min" min="0" value="<?= htmlspecialchars($prix_min) ?>">
        <input type="number" name="prix_max" class="filter-input" placeholder="Max" min="0" value="<?= htmlspecialchars($prix_max) ?>">
      </div>
    </div>
    <div class="filter-group">
      <label>Trier par</label>
      <select name="sort" class="filter-select">
        <option value="">Par défaut</option>
        <option value="newest"     <?= $sort==='newest'    ?'selected':'' ?>>Nouveautés</option>
        <option value="price_asc"  <?= $sort==='price_asc' ?'selected':'' ?>>Prix ↑</option>
        <option value="price_desc" <?= $sort==='price_desc'?'selected':'' ?>>Prix ↓</option>
      </select>
    </div>
    <button type="submit" class="btn-filter">Filtrer</button>
    <?php if ($filtresActifs): ?><a href="index.php" class="clear-link">✕ Effacer tout</a><?php endif; ?>
  </form>
</div>

<?php if ($filtresActifs): ?>
<div class="active-filters">
  <?php if ($search   !== ''): ?><span class="filter-pill">🔍 <?= htmlspecialchars($search) ?><a href="index.php?<?= http_build_query(array_merge($_GET,['search'=>''])) ?>">✕</a></span><?php endif; ?>
  <?php if ($category !== ''): ?><span class="filter-pill">📂 <?= htmlspecialchars($category) ?><a href="index.php?<?= http_build_query(array_merge($_GET,['category'=>''])) ?>">✕</a></span><?php endif; ?>
  <?php if ($occasion !== ''): ?><span class="filter-pill">🎉 <?= htmlspecialchars($occasion) ?><a href="index.php?<?= http_build_query(array_merge($_GET,['occasion'=>''])) ?>">✕</a></span><?php endif; ?>
  <?php if ($region   !== ''): ?><span class="filter-pill">📍 <?= htmlspecialchars($region) ?><a href="index.php?<?= http_build_query(array_merge($_GET,['region'=>''])) ?>">✕</a></span><?php endif; ?>
  <?php if ($prix_min !== ''): ?><span class="filter-pill">Min <?= htmlspecialchars($prix_min) ?> DH<a href="index.php?<?= http_build_query(array_merge($_GET,['prix_min'=>''])) ?>">✕</a></span><?php endif; ?>
  <?php if ($prix_max !== ''): ?><span class="filter-pill">Max <?= htmlspecialchars($prix_max) ?> DH<a href="index.php?<?= http_build_query(array_merge($_GET,['prix_max'=>''])) ?>">✕</a></span><?php endif; ?>
</div>
<?php endif; ?>

<div class="results-area">
  <div class="products-grid">
    <?php if (empty($products)): ?>
      <div class="empty-state">
        <div class="empty-ornament">✦</div>
        <h2 class="empty-title">Aucune pièce trouvée</h2>
        <p class="empty-sub">Essayez d'autres critères ou explorez toute notre collection.</p>
        <a href="index.php" class="empty-link">Voir toutes les créations</a>
      </div>
    <?php else: ?>
      <?php foreach ($products as $product): ?>
      <div class="product-card">
        <div class="product-img">
          <div class="product-img-pattern"></div>
          <?php if (!empty($product['image'])): ?>
            <img src="../../public/images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
          <?php else: ?>
            <div class="product-img-icon"><svg viewBox="0 0 24 24"><path d="M12 2C9 2 7 5 7 8c0 4 5 12 5 12s5-8 5-12c0-3-2-6-5-6z"/></svg></div>
          <?php endif; ?>
          <?php if ($product['stock'] <= 0): ?><span class="product-badge badge-rupture">Rupture</span><?php endif; ?>
        </div>
        <div class="product-info">
          <p class="product-category"><?= htmlspecialchars($product['category']) ?></p>
          <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
          <p class="product-meta">
            <?php if (!empty($product['occasion'])): ?>🎉 <?= htmlspecialchars($product['occasion']) ?><br><?php endif; ?>
            <?php if (!empty($product['region'])): ?>📍 <?= htmlspecialchars($product['region']) ?><?php endif; ?>
          </p>
          <p class="product-price"><?= number_format($product['price'],2) ?> DH <small>TTC</small></p>
          <div class="product-actions" onclick="event.stopPropagation()">
            <a href="detail.php?id=<?= $product['id'] ?>" class="btn-detail">Voir détail</a>
            <?php if (isset($_SESSION['user_id']) && $product['stock'] > 0): ?>
              <a href="../../controllers/CartController.php?action=add&product_id=<?= $product['id'] ?>" class="btn-cart">+ Panier</a>
            <?php elseif ($product['stock'] <= 0): ?>
              <span class="btn-cart-disabled">Indisponible</span>
            <?php else: ?>
              <a href="../auth/login.php" class="btn-detail" style="background:rgba(201,168,76,.1);border-color:var(--gold);color:var(--gold-dark);">Connexion</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<footer>
  <span class="footer-logo">DAR AL CAFTAN</span>
  <span class="footer-copy">© 2026 — Haute Couture Marocaine</span>
</footer>
<script>
function rechercherParImage(input) {
    if (!input.files || !input.files[0]) return;

    const file       = input.files[0];
    const preview    = document.getElementById('img-preview');
    const previewSrc = document.getElementById('img-preview-src');
    const loading    = document.getElementById('img-loading');
    const detected   = document.getElementById('img-detected');
    const similarResults = document.getElementById('similar-results');
    const similarGrid    = document.getElementById('similar-grid');

    // Afficher preview
    const reader = new FileReader();
    reader.onload = (e) => {
        previewSrc.src = e.target.result;
        preview.style.display = 'block';
    };
    reader.readAsDataURL(file);

    // Afficher loading
    loading.style.display    = 'block';
    detected.style.display   = 'none';
    similarResults.style.display = 'none';

    const formData = new FormData();
    formData.append('image', file);

    fetch('/E-STORE/controllers/ImageSearchController.php', {
        method: 'POST',
        body:   formData
    })
    .then(async r => {
        const text = await r.text();
        try { return JSON.parse(text); }
        catch(e) { throw new Error('Réponse invalide : ' + text); }
    })
    .then(res => {
        loading.style.display = 'none';

        if (!res.success) {
            detected.style.display = 'block';
            detected.innerHTML = `❌ ${res.error}`;
            return;
        }

        const d = res.detected;

        // Afficher la détection
        detected.style.display = 'block';
        detected.innerHTML = `
            <strong>✅ Tenue détectée :</strong><br>
            👘 Type : <strong>${d.category}</strong><br>
            👤 Genre : <strong>${d.genre}</strong><br>
            🎨 Couleur : <strong>${d.couleur}</strong><br>
            🎉 Occasion : <strong>${d.occasion}</strong><br>
            💬 <em>${d.description}</em>
        `;

        // Afficher produits similaires
        if (res.products && res.products.length > 0) {
            similarResults.style.display = 'block';
            similarGrid.innerHTML = res.products.map(p => {
                const imgPath = p.image ? `/E-STORE/public/images/${p.image}` : '';
                return `
                <div class="product-card">
                    <div class="product-img">
                        <div class="product-img-pattern"></div>
                        ${imgPath
                            ? `<img src="${imgPath}" alt="${p.name}">`
                            : `<div class="product-img-icon"><svg viewBox="0 0 24 24"><path d="M12 2C9 2 7 5 7 8c0 4 5 12 5 12s5-8 5-12c0-3-2-6-5-6z"/></svg></div>`
                        }
                    </div>
                    <div class="product-info">
                        <p class="product-category">${p.category}</p>
                        <h3 class="product-name">${p.name}</h3>
                        <p class="product-price">${parseFloat(p.price).toFixed(2)} DH <small>TTC</small></p>
                        <div class="product-actions" onclick="event.stopPropagation()">
                            <a href="detail.php?id=${p.id}" class="btn-detail">Voir détail</a>
                            <a href="/E-STORE/controllers/CartController.php?action=add&product_id=${p.id}"
                               class="btn-cart">+ Panier</a>
                        </div>
                    </div>
                </div>`;
            }).join('');

            // Scroll vers résultats
            similarResults.scrollIntoView({ behavior: 'smooth' });
        }
    })
    .catch(err => {
        loading.style.display  = 'none';
        detected.style.display = 'block';
        detected.innerHTML = `❌ Erreur : ${err.message}`;
    });
}
</script>
</body>
</html>
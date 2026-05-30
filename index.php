<?php
session_start();
require_once 'models/Visite.php';     
(new Visite())->enregistrer('Accueil'); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dar Al Caftan – Haute Couture Marocaine</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Cinzel:wght@400;500&family=Raleway:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="public/css/style.css">
</head>
<body>

<!-- NAV -->
<nav>
  <a href="index.php" class="nav-logo">
    <svg width="48" height="48" viewBox="0 0 86 86">
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
  <a href="views/products/index.php">Toutes les collections</a>
  <a href="views/products/femmes.php">Collection Femmes</a>
  <a href="views/products/hommes.php">Collection Hommes</a>
</div>
  <div class="nav-actions">
    <a href="views/auth/login.php" class="btn-outline">Connexion</a>
    <a href="views/auth/register.php" class="btn-gold">Créer un compte</a>
  </div>
</nav>

<!-- HERO -->
<section class="hero">
  <div class="hero-left">
    <p class="hero-eyebrow">Collection Printemps 2025</p>
    <h1 class="hero-title">
      L'élégance de la<br>
      couture <em>marocaine</em><br>
      réinventée
    </h1>
    <p class="hero-sub">
      Des créations d'exception, brodées à la main, qui honorent l'art ancestral du caftan marocain tout en l'inscrivant dans la modernité.
    </p>
    <div class="hero-cta">
      <a href="views/products/index.php" class="btn-primary">Découvrir la collection</a>
      <a href="#categories" class="link-discover">Explorer les catégories</a>
    </div>
  </div>
 <div class="hero-right">
  <div class="hero-pattern"></div>

  <div class="hero-slider">
    <div class="hero-slide active">
      <img src="public/images/hero1.jfif" alt="Caftan Royal">
    </div>
    <div class="hero-slide">
      <img src="public/images/hero2.jfif" alt="Jabador">
    </div>
    <div class="hero-slide">
      <img src="public/images/hero3.jfif" alt="Jellaba Élégante">
    </div>
    <div class="hero-slide">
      <img src="public/images/hero4.jfif" alt="Jellaba Homme">
    </div>
    <div class="hero-slide">
      <img src="public/images/hero5.jfif" alt="Takchita ">
    </div>

    <div class="slider-dots">
      <span class="dot active" onclick="goToSlide(0)"></span>
      <span class="dot" onclick="goToSlide(1)"></span>
      <span class="dot" onclick="goToSlide(2)"></span>
      <span class="dot" onclick="goToSlide(3)"></span>
      <span class="dot" onclick="goToSlide(4)"></span>
    </div>
  </div>
</div>

  <div class="hero-scroll-hint">
    <span class="scroll-line"></span>
    Défiler
  </div>
</section>

<!-- ORNAMENTAL DIVIDER -->
<div class="divider">
  <div class="divider-line"></div>
  <div class="divider-ornament">
    <svg viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
      <path d="M20 2 L22 18 L38 20 L22 22 L20 38 L18 22 L2 20 L18 18 Z"/>
    </svg>
  </div>
  <div class="divider-line"></div>
</div>

<!-- SEARCH -->
<div class="search-section">
  <p class="section-eyebrow" style="margin-bottom:1.5rem;">Trouver votre tenue</p>
  <form action="views/products/index.php" method="GET">
    <div class="search-wrap">
      <input type="text" name="search" class="search-input" placeholder="Rechercher un caftan, une takchita, une jellaba…">
      <button type="submit" class="search-btn">Rechercher</button>
    </div>
  </form>
</div>

<!-- CATEGORIES -->
<section id="categories" class="section" style="padding-top:2rem;">
  <div class="section-header">
    <span class="section-eyebrow">Nos Univers</span>
    <h2 class="section-title">Explorer par catégorie</h2>
  </div>
  <div class="categories-grid">

    <div class="cat-card">
      <div class="cat-bg"><div class="cat-bg-pattern"></div></div>
      <div class="cat-icon">
        <svg viewBox="0 0 24 24"><path d="M12 2C9 2 6.5 5 6.5 8.5c0 4 5.5 13.5 5.5 13.5s5.5-9.5 5.5-13.5C17.5 5 15 2 12 2z"/></svg>
      </div>
      <div class="cat-overlay">
        <p class="cat-label">Pour elle</p>
        <h3 class="cat-title">Caftan Royal</h3>
        <p class="cat-count">24 créations disponibles</p>
        <a href="views/products/index.php?category=caftan" class="cat-arrow">
          Voir la sélection →
        </a>
      </div>
    </div>

    <div class="cat-card">
      <div class="cat-bg" style="background: linear-gradient(160deg, #1a1a2c 0%, #12121c 100%);"><div class="cat-bg-pattern"></div></div>
      <div class="cat-icon">
        <svg viewBox="0 0 24 24"><path d="M12 2l-2 4-4 .5 3 2.8-.7 4.2L12 11l3.7 2.5-.7-4.2 3-2.8-4-.5z"/></svg>
      </div>
      <div class="cat-overlay">
        <p class="cat-label">Pour elle</p>
        <h3 class="cat-title">Takchita</h3>
        <p class="cat-count">18 créations disponibles</p>
        <a href="views/products/index.php?category=takchita" class="cat-arrow">
          Voir la sélection →
        </a>
      </div>
    </div>

    <div class="cat-card">
      <div class="cat-bg" style="background: linear-gradient(160deg, #0d2015 0%, #081510 100%);"><div class="cat-bg-pattern"></div></div>
      <div class="cat-icon">
        <svg viewBox="0 0 24 24"><ellipse cx="12" cy="12" rx="5" ry="9"/></svg>
      </div>
      <div class="cat-overlay">
        <p class="cat-label">Pour tous</p>
        <h3 class="cat-title">Jellaba</h3>
        <p class="cat-count">31 créations disponibles</p>
        <a href="views/products/index.php?category=jellaba" class="cat-arrow">
          Voir la sélection →
        </a>
      </div>
    </div>

  </div>
</section>

<!-- FEATURED PRODUCTS -->
<section class="section products-section">
  <div class="section-header">
    <span class="section-eyebrow">Coup de cœur</span>
    <h2 class="section-title">Pièces d'exception</h2>
  </div>
  <div class="products-grid">

    <?php
    // Cherche database.php dans plusieurs emplacements possibles
    $possiblePaths = [
        __DIR__ . '/config/database.php',
        dirname(__DIR__) . '/config/database.php',
        dirname(__DIR__, 2) . '/config/database.php',
        'C:/xampp/htdocs/E-STORE/config/database.php',
    ];
    foreach ($possiblePaths as $p) {
        if (file_exists($p)) { require_once $p; break; }
    }

    $products = [];
    try {
      // Utilise la fonction getConnection() définie dans database.php
      $db = getConnection();
      $stmt = $db->query("SELECT * FROM products WHERE stock > 0 ORDER BY created_at DESC LIMIT 3");
      $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      $products = [];
    }

    // Demo products if DB empty
    if (empty($products)) {
      $products = [
        ['id'=>1,'name'=>'Caftan Royal Bleu','category'=>'Caftan','price'=>1500,'stock'=>5,'image'=>'public/images/CaftanRoyal.jfif','is_new'=>true],
        ['id'=>2,'name'=>'Takchita Dorée','category'=>'Takchita','price'=>2500,'stock'=>3,'image'=>'public/images/takchitaDoreeRouge.jfif','is_new'=>false],
        ['id'=>3,'name'=>'Jellaba Femme Rose','category'=>'Jellaba','price'=>800,'stock'=>8,'image'=>null,'is_new'=>true],
      ];
    }

    foreach ($products as $product):
    ?>
    <div class="product-card">
      <div class="product-img">
        <div class="product-img-inner"></div>

        
        <?php if (!empty($product['image']) && file_exists($product['image'])): ?>

        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:0.9;">

        <?php else: ?>

          <div class="product-img-icon">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C9 2 7 5 7 8c0 4 5 12 5 12s5-8 5-12c0-3-2-6-5-6z"/></svg>
          </div>

        <?php endif; ?>

        <?php if (!empty($product['is_new'])): ?>

          <span class="product-badge">Nouveau</span>

        <?php endif; ?>
        <!-- Boutons overlay -->
        <div class="product-overlay">
          <a href="views/products/detail.php?id=<?= $product['id'] ?>" class="overlay-btn">Voir le détail</a>

          <?php if (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'client'): ?>

            <a href="controllers/CartController.php?action=add&product_id=<?= $product['id'] ?>" class="overlay-btn overlay-btn-gold">+ Panier</a>
            
          <?php else: ?>
            <a href="views/auth/login.php" class="overlay-btn overlay-btn-gold">+ Panier</a>
          <?php endif; ?>
        </div>
      </div>
      <div class="product-info">
        <p class="product-category"><?= htmlspecialchars($product['category']) ?></p>
        <a href="views/products/detail.php?id=<?= $product['id'] ?>" class="product-name-link">
          <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
        </a>
        <p class="product-price"><?= number_format($product['price'], 2) ?> DH <span>TTC</span></p>
        <div class="product-footer">
          <span class="product-stock">✦ En stock</span>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <div style="text-align:center; margin-top:3rem;">
    <a href="views/products/index.php" class="btn-outline" style="display:inline-block; padding:1rem 3rem; font-size:0.7rem; letter-spacing:0.2em; border-color:var(--charcoal);">
      Voir toutes les créations
    </a>
  </div>
</section>

<!-- HERITAGE BAND -->
<section class="heritage">
  <div class="heritage-left">
    <span class="heritage-eyebrow">Notre Savoir-Faire</span>
    <h2 class="heritage-title">Un héritage brodé<br>fil à fil, <em>génération après génération</em></h2>
    <p class="heritage-text">
      Dar Al Caftan perpétue l'art millénaire de la broderie marocaine. Chaque pièce est confectionnée à la main par nos artisans, utilisant des techniques transmises depuis des siècles dans les ateliers de Fès et de Marrakech.
    </p>
    <div class="heritage-stats">
      <div class="stat">
        <span class="stat-num">200+</span>
        <span class="stat-label">Créations uniques</span>
      </div>
      <div class="stat">
        <span class="stat-num">15</span>
        <span class="stat-label">Artisans experts</span>
      </div>
      <div class="stat">
        <span class="stat-num">100%</span>
        <span class="stat-label">Fait main</span>
      </div>
    </div>
  </div>
  <div class="heritage-right">
    <div class="heritage-ornament">
      <svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">
        <path d="M40 5 L43 37 L75 40 L43 43 L40 75 L37 43 L5 40 L37 37 Z"/>
        <circle cx="40" cy="40" r="8" fill="none" stroke="rgba(201,168,76,0.3)" stroke-width="0.5"/>
        <circle cx="40" cy="40" r="20" fill="none" stroke="rgba(201,168,76,0.15)" stroke-width="0.5"/>
      </svg>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="footer-grid">
    <div class="footer-brand">
      <span class="footer-logo">DAR AL CAFTAN</span>
      <p class="footer-tagline">L'art de la haute couture marocaine,<br>au service de votre élégance.</p>
    </div>
    <div>
      <span class="footer-col-title">Collections</span>
      <ul class="footer-links">
        <li><a href="#">Caftans</a></li>
        <li><a href="#">Takchitas</a></li>
        <li><a href="#">Jellabas</a></li>
        <li><a href="#">Nouveautés</a></li>
      </ul>
    </div>
    <div>
      <span class="footer-col-title">Espace client</span>
      <ul class="footer-links">
        <li><a href="views/auth/login.php">Se connecter</a></li>
        <li><a href="views/auth/register.php">Créer un compte</a></li>
        <li><a href="views/cart/index.php">Mon panier</a></li>
        <li><a href="#">Mes commandes</a></li>
      </ul>
    </div>
    <div>
      <span class="footer-col-title">Informations</span>
      <ul class="footer-links">
        <li><a href="#">À propos</a></li>
        <li><a href="#">Livraison</a></li>
        <li><a href="#">Retours</a></li>
        <li><a href="#">Contact</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <p class="footer-copy">© 2025 Dar Al Caftan. Tous droits réservés.</p>
    <p class="footer-copy">Fait avec soin au Maroc 🇲🇦</p>
  </div>
</footer>
<script>
  let currentSlide = 0;
  const slides = document.querySelectorAll('.hero-slide');
  const dots   = document.querySelectorAll('.dot');

  function goToSlide(n) {
    slides[currentSlide].classList.remove('active');
    dots[currentSlide].classList.remove('active');
    currentSlide = n;
    slides[currentSlide].classList.add('active');
    dots[currentSlide].classList.add('active');
  }

  setInterval(() => {
    goToSlide((currentSlide + 1) % slides.length);
  }, 4000);
</script>
</body>
</html>
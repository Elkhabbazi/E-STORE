rd · PHP
<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/Product.php';
require_once '../../models/User.php';
require_once '../../models/Order.php';
require_once '../../models/Visite.php';
 
// Vérifier que c'est bien un ADMIN
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}
 
// Section active (onglet)
$section = $_GET['section'] ?? 'stats';
 
// Instancier les models
$productModel = new Product();
$userModel    = new User();
$orderModel   = new Order();
 
// ── Données pour chaque section ──
$products  = $productModel->getTous();
$clients   = $userModel->getTousLesClients();
$commandes = $orderModel->getToutesCommandes();
 
// ── Statistiques ──
$stats = [
    'nb_produits'  => $productModel->compter(),
    'nb_clients'   => $userModel->compterClients(),
    'nb_commandes' => $orderModel->compter(),
    'chiffre'      => $orderModel->getChiffreAffaires(),
    'populaires'        => $productModel->getPopulaires(5),
    'nb_visites'        => (new Visite())->compterTotal(),
    'visites_today'     => (new Visite())->compterAujourdhui(),
    'pages_populaires'  => (new Visite())->getPagesPopulaires(),
    'visites_pays'      => (new Visite())->getParPays(),
];
 
// Statuts possibles pour les commandes
$statutsCommande = ['En attente','Confirmée','Expédiée','Livrée','Annulée'];
$statusColors    = [
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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Admin — Dar Al Caftan</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,400&family=Cinzel:wght@400;500&family=Raleway:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
:root {
  --gold:#C9A84C; --gold-dark:#8B6914; --ivory:#FAF7F0;
  --charcoal:#1A1714; --warm-gray:#6B6560;
  --border:rgba(201,168,76,0.25); --bg:#F4F0E8;
  --sidebar:220px;
}
body { font-family:'Raleway',sans-serif; background:var(--bg); color:var(--charcoal); display:flex; min-height:100vh; }
 
/* ── SIDEBAR ── */
.sidebar {
  width:var(--sidebar); background:var(--charcoal);
  display:flex; flex-direction:column;
  position:fixed; top:0; left:0; height:100vh;
  border-right:0.5px solid rgba(201,168,76,0.15);
  z-index:50;
}
.sidebar-brand {
  padding:1.8rem 1.5rem; border-bottom:0.5px solid rgba(201,168,76,0.15);
}
.sidebar-brand-name { font-family:'Cinzel',serif; font-size:0.85rem; letter-spacing:0.2em; color:var(--ivory); display:block; }
.sidebar-brand-sub  { font-size:0.5rem; letter-spacing:0.3em; text-transform:uppercase; color:var(--gold); margin-top:3px; display:block; }
 
.sidebar-nav { flex:1; padding:1.5rem 0; }
.nav-label { font-size:0.5rem; letter-spacing:0.3em; text-transform:uppercase; color:rgba(250,247,240,0.3); padding:0.5rem 1.5rem; margin-top:1rem; }
 
.nav-item {
  display:flex; align-items:center; gap:0.8rem;
  padding:0.75rem 1.5rem; font-size:0.7rem; letter-spacing:0.1em;
  text-decoration:none; color:rgba(250,247,240,0.55);
  transition:all 0.2s; border-left:2px solid transparent;
}
.nav-item:hover { color:var(--ivory); background:rgba(201,168,76,0.08); }
.nav-item.active { color:var(--gold); border-left-color:var(--gold); background:rgba(201,168,76,0.1); }
.nav-icon { font-size:1rem; width:20px; text-align:center; }
 
.sidebar-footer {
  padding:1.2rem 1.5rem; border-top:0.5px solid rgba(201,168,76,0.15);
}
.admin-info { font-size:0.65rem; color:rgba(250,247,240,0.5); margin-bottom:0.6rem; }
.admin-info strong { color:var(--ivory); display:block; font-size:0.7rem; }
.logout-link { font-size:0.6rem; letter-spacing:0.15em; text-transform:uppercase; color:rgba(250,247,240,0.4); text-decoration:none; transition:color 0.2s; }
.logout-link:hover { color:#c0392b; }
 
/* ── MAIN ── */
.main { margin-left:var(--sidebar); flex:1; min-height:100vh; }
 
/* Top bar */
.topbar {
  background:var(--ivory); border-bottom:0.5px solid var(--border);
  padding:1rem 2.5rem; display:flex; justify-content:space-between; align-items:center;
  position:sticky; top:0; z-index:40;
}
.topbar-title { font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:300; }
.topbar-title em { font-style:italic; color:var(--gold); }
.topbar-actions { display:flex; gap:0.8rem; }
.btn-sm { font-size:0.6rem; letter-spacing:0.15em; text-transform:uppercase; padding:0.5rem 1.2rem; text-decoration:none; transition:all 0.2s; cursor:pointer; font-family:'Raleway',sans-serif; border:none; }
.btn-dark { background:var(--charcoal); color:var(--ivory); }
.btn-dark:hover { background:var(--gold); }
.btn-gold { background:var(--gold); color:var(--ivory); }
.btn-gold:hover { background:var(--gold-dark); }
.btn-outline { border:0.5px solid var(--charcoal); color:var(--charcoal); background:transparent; }
.btn-outline:hover { background:var(--charcoal); color:var(--ivory); }
 
/* Content */
.content { padding:2rem 2.5rem; }
 
/* ── ALERTS ── */
.alert { padding:0.8rem 1.2rem; margin-bottom:1.5rem; font-size:0.75rem; border-radius:2px; }
.alert-success { background:#F0F7EE; border-left:3px solid #6B8F5E; color:#3A6A31; }
 
/* ── STAT CARDS ── */
.stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1.2rem; margin-bottom:2.5rem; }
.stat-card { background:var(--ivory); border:0.5px solid var(--border); padding:1.5rem; position:relative; overflow:hidden; }
.stat-card::after { content:''; position:absolute; bottom:0; left:0; right:0; height:2px; background:var(--gold); transform:scaleX(0); transition:transform 0.3s; transform-origin:left; }
.stat-card:hover::after { transform:scaleX(1); }
.stat-label { font-size:0.55rem; letter-spacing:0.3em; text-transform:uppercase; color:var(--warm-gray); margin-bottom:0.8rem; display:block; }
.stat-value { font-family:'Cormorant Garamond',serif; font-size:2.2rem; font-weight:300; color:var(--charcoal); }
.stat-value small { font-family:'Raleway',sans-serif; font-size:0.7rem; color:var(--warm-gray); }
.stat-icon { position:absolute; top:1rem; right:1.2rem; font-size:1.5rem; opacity:0.15; }
 
/* ── TABLES ── */
.section-title { font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:300; margin-bottom:1.2rem; padding-bottom:0.8rem; border-bottom:0.5px solid var(--border); }
.section-title em { color:var(--gold); font-style:italic; }
 
.table-wrap { background:var(--ivory); border:0.5px solid var(--border); overflow:hidden; margin-bottom:2rem; }
table { width:100%; border-collapse:collapse; }
thead { background:var(--charcoal); }
thead th { font-size:0.55rem; letter-spacing:0.2em; text-transform:uppercase; color:rgba(250,247,240,0.7); padding:0.9rem 1.2rem; text-align:left; font-weight:400; }
tbody tr { border-bottom:0.5px solid var(--border); transition:background 0.15s; }
tbody tr:last-child { border-bottom:none; }
tbody tr:hover { background:rgba(201,168,76,0.04); }
td { font-size:0.75rem; padding:0.9rem 1.2rem; vertical-align:middle; }
.badge { font-size:0.55rem; letter-spacing:0.15em; text-transform:uppercase; padding:0.25rem 0.6rem; border-radius:2px; color:white; }
.td-actions { display:flex; gap:0.5rem; flex-wrap:wrap; }
.btn-action { font-size:0.55rem; letter-spacing:0.1em; text-transform:uppercase; padding:0.3rem 0.7rem; text-decoration:none; transition:all 0.15s; cursor:pointer; font-family:'Raleway',sans-serif; border:none; }
.btn-edit  { border:0.5px solid var(--charcoal); color:var(--charcoal); background:transparent; }
.btn-edit:hover  { background:var(--charcoal); color:var(--ivory); }
.btn-delete { border:0.5px solid #c0392b; color:#c0392b; background:transparent; }
.btn-delete:hover { background:#c0392b; color:white; }
select.status-select { font-family:'Raleway',sans-serif; font-size:0.65rem; padding:0.3rem 0.6rem; border:0.5px solid var(--border); background:transparent; color:var(--charcoal); cursor:pointer; }
 
/* ── PRODUITS POPULAIRES ── */
.pop-item { display:flex; justify-content:space-between; align-items:center; padding:0.7rem 0; border-bottom:0.5px solid var(--border); font-size:0.75rem; }
.pop-item:last-child { border-bottom:none; }
.pop-bar-wrap { flex:1; margin:0 1rem; height:4px; background:rgba(201,168,76,0.15); border-radius:2px; }
.pop-bar { height:100%; background:var(--gold); border-radius:2px; }
 
/* ── MODAL AJOUT/MODIF PRODUIT ── */
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(26,23,20,0.7); z-index:200; align-items:center; justify-content:center; }
.modal-overlay.open { display:flex; }
.modal { background:var(--ivory); width:90%; max-width:520px; max-height:90vh; overflow-y:auto; padding:2rem; position:relative; }
.modal-title { font-family:'Cormorant Garamond',serif; font-size:1.5rem; font-weight:300; margin-bottom:1.5rem; }
.modal-title em { color:var(--gold); font-style:italic; }
.modal-close { position:absolute; top:1rem; right:1.2rem; font-size:1.2rem; cursor:pointer; color:var(--warm-gray); background:none; border:none; }
.field { margin-bottom:1.2rem; }
.field label { display:block; font-size:0.6rem; letter-spacing:0.2em; text-transform:uppercase; color:var(--charcoal); margin-bottom:0.4rem; font-weight:500; }
.field input, .field textarea, .field select {
  width:100%; padding:0.7rem 0.9rem; font-family:'Raleway',sans-serif; font-size:0.8rem;
  border:0.5px solid var(--border); background:var(--bg); color:var(--charcoal);
  outline:none; transition:border-color 0.2s;
}
.field input:focus, .field textarea:focus, .field select:focus { border-color:var(--gold); }
.field textarea { resize:vertical; min-height:80px; }
.field-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
</style>
</head>
<body>
 
<!-- ── SIDEBAR ── -->
<aside class="sidebar">
  <div class="sidebar-brand">
    <span class="sidebar-brand-name">DAR AL CAFTAN</span>
    <span class="sidebar-brand-sub">Administration</span>
  </div>
 
  <nav class="sidebar-nav">
    <span class="nav-label">Tableau de bord</span>
    <a href="?section=stats"     class="nav-item <?= $section==='stats'?'active':'' ?>">
      <span class="nav-icon">📊</span> Statistiques
    </a>
 
    <span class="nav-label">Gestion</span>
    <a href="?section=produits"  class="nav-item <?= $section==='produits'?'active':'' ?>">
      <span class="nav-icon">👘</span> Produits
    </a>
    <a href="?section=commandes" class="nav-item <?= $section==='commandes'?'active':'' ?>">
      <span class="nav-icon">📦</span> Commandes
    </a>
    <a href="?section=clients"   class="nav-item <?= $section==='clients'?'active':'' ?>">
      <span class="nav-icon">👤</span> Clients
    </a>
 
    <span class="nav-label">Site</span>
    <a href="../products/index.php" class="nav-item" target="_blank">
      <span class="nav-icon">🌐</span> Voir le site
    </a>
  </nav>
 
  <div class="sidebar-footer">
    <p class="admin-info">
      <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>
      Administrateur
    </p>
    <a href="../../controllers/AuthController.php?action=logout" class="logout-link">
      ⊗ Déconnexion
    </a>
  </div>
</aside>
 
<!-- ── MAIN ── -->
<main class="main">
 
  <!-- Top bar -->
  <div class="topbar">
    <h1 class="topbar-title">
      <?php
        $titles = [
          'stats'     => 'Tableau de <em>bord</em>',
          'produits'  => 'Gestion des <em>produits</em>',
          'commandes' => 'Suivi des <em>commandes</em>',
          'clients'   => 'Gestion des <em>clients</em>',
        ];
        echo $titles[$section] ?? 'Dashboard';
      ?>
    </h1>
    <div class="topbar-actions">
      <?php if ($section === 'produits'): ?>
        <button class="btn-sm btn-gold" onclick="openModal('modal-add')">+ Ajouter un produit</button>
      <?php endif; ?>
    </div>
  </div>
 
  <div class="content">
 
    <!-- Messages flash -->
    <?php if (isset($_GET['added'])):   ?><div class="alert alert-success">✓ Produit ajouté avec succès.</div><?php endif; ?>
    <?php if (isset($_GET['updated'])): ?><div class="alert alert-success">✓ Modification enregistrée.</div><?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?><div class="alert alert-success">✓ Élément supprimé.</div><?php endif; ?>
 
    <!-- ════════════════════════════════
         SECTION : STATISTIQUES
    ════════════════════════════════ -->
    <?php if ($section === 'stats'): ?>
 
      <div class="stats-grid">
        <div class="stat-card">
          <span class="nav-icon stat-icon">👘</span>
          <span class="stat-label">Total produits</span>
          <div class="stat-value"><?= $stats['nb_produits'] ?></div>
        </div>
        <div class="stat-card">
          <span class="nav-icon stat-icon">👤</span>
          <span class="stat-label">Clients inscrits</span>
          <div class="stat-value"><?= $stats['nb_clients'] ?></div>
        </div>
        <div class="stat-card">
          <span class="nav-icon stat-icon">📦</span>
          <span class="stat-label">Commandes totales</span>
          <div class="stat-value"><?= $stats['nb_commandes'] ?></div>
        </div>
        <div class="stat-card">
          <span class="nav-icon stat-icon">💰</span>
          <span class="stat-label">Chiffre d'affaires</span>
          <div class="stat-value">
            <?= number_format($stats['chiffre'], 0) ?>
            <small>DH</small>
          </div>
        </div>
        <div class="stat-card">
          <span class="nav-icon stat-icon">👁️</span>
          <span class="stat-label">Visites totales</span>
          <div class="stat-value"><?= $stats['nb_visites'] ?></div>
        </div>
        <div class="stat-card">
          <span class="nav-icon stat-icon">📅</span>
          <span class="stat-label">Visites aujourd'hui</span>
          <div class="stat-value"><?= $stats['visites_today'] ?></div>
        </div>
      </div>
 
      <!-- Produits populaires -->
      <h2 class="section-title">Produits les plus <em>commandés</em></h2>
      <div class="table-wrap" style="padding:1.5rem;">
        <?php if (empty($stats['populaires'])): ?>
          <p style="font-size:0.75rem;color:var(--warm-gray);text-align:center;padding:1rem;">
            Aucune commande enregistrée pour le moment.
          </p>
        <?php else: ?>
          <?php
            $maxVente = max(array_column($stats['populaires'], 'total_vendu'));
            foreach ($stats['populaires'] as $pop):
              $pct = $maxVente > 0 ? ($pop['total_vendu'] / $maxVente) * 100 : 0;
          ?>
          <div class="pop-item">
            <span style="min-width:180px;"><?= htmlspecialchars($pop['name']) ?></span>
            <div class="pop-bar-wrap">
              <div class="pop-bar" style="width:<?= $pct ?>%"></div>
            </div>
            <span style="min-width:80px;text-align:right;color:var(--gold);font-weight:500;">
              <?= $pop['total_vendu'] ?> vendus
            </span>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
 
      <!-- Pages populaires -->
      <h2 class="section-title" style="margin-top:2rem;">Pages les plus <em>visitées</em></h2>
      <div class="table-wrap" style="padding:1.5rem; margin-bottom:2rem;">
        <?php if (empty($stats['pages_populaires'])): ?>
          <p style="font-size:0.75rem;color:var(--warm-gray);text-align:center;">
            Aucune visite enregistrée.
          </p>
        <?php else: ?>
          <?php
            $maxPage = max(array_column($stats['pages_populaires'], 'nb'));
            foreach ($stats['pages_populaires'] as $pg):
              $pct = $maxPage > 0 ? ($pg['nb'] / $maxPage) * 100 : 0;
          ?>
          <div class="pop-item">
            <span style="min-width:160px;"><?= htmlspecialchars($pg['page']) ?></span>
            <div class="pop-bar-wrap">
              <div class="pop-bar" style="width:<?= $pct ?>%"></div>
            </div>
            <span style="min-width:80px;text-align:right;color:var(--gold);font-weight:500;">
              <?= $pg['nb'] ?> visites
            </span>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
 
      <!-- Géolocalisation par pays -->
      <h2 class="section-title">Visiteurs par <em>pays</em></h2>
      <div class="table-wrap" style="padding:1.5rem;">
        <?php if (empty($stats['visites_pays'])): ?>
          <p style="font-size:0.75rem;color:var(--warm-gray);text-align:center;">
            Aucune donnée géographique.
          </p>
        <?php else: ?>
          <?php
            $maxPays = max(array_column($stats['visites_pays'], 'nb'));
            foreach ($stats['visites_pays'] as $pys):
              $pct = $maxPays > 0 ? ($pys['nb'] / $maxPays) * 100 : 0;
          ?>
          <div class="pop-item">
            <span style="min-width:160px;">🌍 <?= htmlspecialchars($pys['pays']) ?></span>
            <div class="pop-bar-wrap">
              <div class="pop-bar" style="width:<?= $pct ?>%; background:#4A90D9;"></div>
            </div>
            <span style="min-width:80px;text-align:right;color:#4A90D9;font-weight:500;">
              <?= $pys['nb'] ?> visiteurs
            </span>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
 
    <!-- ════════════════════════════════
         SECTION : PRODUITS
    ════════════════════════════════ -->
    <?php elseif ($section === 'produits'): ?>
 
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Produit</th>
              <th>Catégorie</th>
              <th>Prix</th>
              <th>Stock</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($products)): ?>
              <tr><td colspan="6" style="text-align:center;color:var(--warm-gray);padding:2rem;">
                Aucun produit. Cliquez sur "Ajouter" pour commencer.
              </td></tr>
            <?php else: ?>
              <?php foreach ($products as $p): ?>
              <tr>
                <td style="color:var(--warm-gray);">#<?= $p['id'] ?></td>
                <td>
                  <strong><?= htmlspecialchars($p['name']) ?></strong>
                  <?php if (!empty($p['description'])): ?>
                    <br><small style="color:var(--warm-gray);"><?= mb_substr(htmlspecialchars($p['description']), 0, 50) ?>…</small>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($p['category']) ?></td>
                <td><?= number_format($p['price'], 2) ?> DH</td>
                <td>
                  <?php if ($p['stock'] <= 0): ?>
                    <span class="badge" style="background:#c0392b;">Rupture</span>
                  <?php elseif ($p['stock'] <= 3): ?>
                    <span class="badge" style="background:#C9A84C;"><?= $p['stock'] ?></span>
                  <?php else: ?>
                    <span><?= $p['stock'] ?></span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="td-actions">
                    <button class="btn-action btn-edit"
                      onclick="openEdit(<?= htmlspecialchars(json_encode($p)) ?>)">
                      Modifier
                    </button>
                    <a href="../../controllers/ProductController.php?action=supprimer&id=<?= $p['id'] ?>"
                       class="btn-action btn-delete"
                       onclick="return confirm('Supprimer ce produit ?')">
                      Supprimer
                    </a>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
 
    <!-- ════════════════════════════════
         SECTION : COMMANDES
    ════════════════════════════════ -->
    <?php elseif ($section === 'commandes'): ?>
 
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>N° Commande</th>
              <th>Client</th>
              <th>Date</th>
              <th>Total</th>
              <th>Statut</th>
              <th>Changer statut</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($commandes)): ?>
              <tr><td colspan="6" style="text-align:center;color:var(--warm-gray);padding:2rem;">
                Aucune commande pour le moment.
              </td></tr>
            <?php else: ?>
              <?php foreach ($commandes as $cmd): ?>
              <tr>
                <td>#<?= str_pad($cmd['id'], 5, '0', STR_PAD_LEFT) ?></td>
                <td>
                  <strong><?= htmlspecialchars($cmd['client_name']) ?></strong>
                  <br><small style="color:var(--warm-gray);"><?= htmlspecialchars($cmd['email']) ?></small>
                </td>
                <td><?= date('d/m/Y', strtotime($cmd['created_at'])) ?></td>
                <td><strong><?= number_format($cmd['total_price'], 2) ?> DH</strong></td>
                <td>
                  <span class="badge" style="background:<?= $statusColors[$cmd['status']] ?? '#888' ?>">
                    <?= htmlspecialchars($cmd['status']) ?>
                  </span>
                </td>
                <td>
                  <select class="status-select"
                    onchange="window.location='../../controllers/OrderController.php?action=changer_statut&order_id=<?= $cmd['id'] ?>&statut='+this.value">
                    <?php foreach ($statutsCommande as $s): ?>
                      <option value="<?= $s ?>" <?= $cmd['status'] === $s ? 'selected' : '' ?>>
                        <?= $s ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
 
    <!-- ════════════════════════════════
         SECTION : CLIENTS
    ════════════════════════════════ -->
    <?php elseif ($section === 'clients'): ?>
 
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Nom</th>
              <th>Email</th>
              <th>Inscrit le</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($clients)): ?>
              <tr><td colspan="5" style="text-align:center;color:var(--warm-gray);padding:2rem;">
                Aucun client inscrit.
              </td></tr>
            <?php else: ?>
              <?php foreach ($clients as $client): ?>
              <tr>
                <td style="color:var(--warm-gray);">#<?= $client['id'] ?></td>
                <td><strong><?= htmlspecialchars($client['name']) ?></strong></td>
                <td><?= htmlspecialchars($client['email']) ?></td>
                <td><?= date('d/m/Y', strtotime($client['created_at'])) ?></td>
                <td>
                  <a href="?action=supprimer_client&id=<?= $client['id'] ?>"
                     class="btn-action btn-delete"
                     onclick="return confirm('Supprimer ce client ?')">
                    Supprimer
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
 
    <?php endif; ?>
 
  </div><!-- /content -->
</main>
 
<!-- ══ MODAL : AJOUTER PRODUIT ══ -->
<div class="modal-overlay" id="modal-add">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('modal-add')">✕</button>
    <h2 class="modal-title">Ajouter un <em>produit</em></h2>
    <form method="POST" action="../../controllers/ProductController.php?action=ajouter" enctype="multipart/form-data">
      <div class="field">
        <label>Nom du produit *</label>
        <input type="text" name="name" required placeholder="Ex: Caftan Royal Brodé">
      </div>
      <div class="field-row">
        <div class="field">
          <label>Catégorie *</label>
          <select name="category" required>
            <option value="">Choisir…</option>
            <option>Caftan</option>
            <option>Takchita</option>
            <option>Jellaba Femme</option>
            <option>Jabador</option>
            <option>Jellaba Homme</option>
            <option>Chaussures</option>
          </select>
        </div>
        <div class="field">
          <label>Prix (DH) *</label>
          <input type="number" name="price" step="0.01" min="0" required placeholder="1200">
        </div>
      </div>
      <div class="field">
        <label>Stock *</label>
        <input type="number" name="stock" min="0" required placeholder="10">
      </div>
      <div class="field">
        <label>Description</label>
        <textarea name="description" placeholder="Description du produit…"></textarea>
        <button type="button" id="btn-ia" onclick="classifierIA()" 
  style="margin-top:8px; padding:8px 16px; background:#C9A84C; color:white; border:none; cursor:pointer; font-size:13px; border-radius:4px;">
  ✨ Classifier avec IA
</button>
<div id="ia-result" style="display:none; margin-top:10px; padding:12px; 
  background:#f0f8e8; border:1px solid #4a7c59; font-size:13px; border-radius:4px; line-height:1.8;">
</div>
      </div>
      <div class="field">
        <label>Image</label>
        <input type="file" name="image" accept="image/*">
      </div>
      <div style="display:flex;gap:1rem;justify-content:flex-end;margin-top:1rem;">
        <button type="button" class="btn-sm btn-outline" onclick="closeModal('modal-add')">Annuler</button>
        <button type="submit" class="btn-sm btn-gold">Ajouter</button>
      </div>
    </form>
  </div>
</div>
 
<!-- ══ MODAL : MODIFIER PRODUIT ══ -->
<div class="modal-overlay" id="modal-edit">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('modal-edit')">✕</button>
    <h2 class="modal-title">Modifier le <em>produit</em></h2>
    <form method="POST" action="../../controllers/ProductController.php?action=modifier" enctype="multipart/form-data">
      <input type="hidden" name="id" id="edit-id">
      <div class="field">
        <label>Nom du produit *</label>
        <input type="text" name="name" id="edit-name" required>
      </div>
      <div class="field-row">
        <div class="field">
          <label>Catégorie *</label>
          <select name="category" id="edit-category" required>
            <option value="">Choisir…</option>
            <option>Caftan</option>
            <option>Takchita</option>
            <option>Jellaba Femme</option>
            <option>Jabador</option>
            <option>Jellaba Homme</option>
            <option>Chaussures</option>
          </select>
        </div>
        <div class="field">
          <label>Prix (DH) *</label>
          <input type="number" name="price" id="edit-price" step="0.01" min="0" required>
        </div>
      </div>
      <div class="field">
        <label>Stock *</label>
        <input type="number" name="stock" id="edit-stock" min="0" required>
      </div>
      <div class="field">
        <label>Description</label>
        <textarea name="description" id="edit-description"></textarea>
      </div>
      <div class="field">
        <label>Nouvelle image (optionnel)</label>
        <input type="file" name="image" accept="image/*">
      </div>
      <div style="display:flex;gap:1rem;justify-content:flex-end;margin-top:1rem;">
        <button type="button" class="btn-sm btn-outline" onclick="closeModal('modal-edit')">Annuler</button>
        <button type="submit" class="btn-sm btn-gold">Enregistrer</button>
      </div>
    </form>
  </div>
</div>
 
<script>
// Ouvrir/fermer les modals
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
 
// Fermer en cliquant sur l'overlay
document.querySelectorAll('.modal-overlay').forEach(overlay => {
  overlay.addEventListener('click', function(e) {
    if (e.target === this) closeModal(this.id);
  });
});
 
// Remplir le modal de modification avec les données du produit
function openEdit(product) {
  document.getElementById('edit-id').value          = product.id;
  document.getElementById('edit-name').value        = product.name;
  document.getElementById('edit-price').value       = product.price;
  document.getElementById('edit-stock').value       = product.stock;
  document.getElementById('edit-description').value = product.description || '';
 
  // Sélectionner la bonne catégorie dans le select
  const catSelect = document.getElementById('edit-category');
  for (let opt of catSelect.options) {
    opt.selected = (opt.value === product.category);
  }
 
  openModal('modal-edit');
}
</script>
 <script>
function classifierIA() {
    const nom         = document.querySelector('#modal-add input[name="name"]')?.value.trim() || '';
    const description = document.querySelector('#modal-add textarea[name="description"]')?.value.trim() || '';
    const btn         = document.getElementById('btn-ia');
    const result      = document.getElementById('ia-result');

    if (!nom && !description) {
        alert('Remplis au moins le nom ou la description du produit.');
        return;
    }

    btn.textContent = '⏳ Analyse en cours...';
    btn.disabled    = true;
    result.style.display = 'none';

    const formData = new FormData();
    formData.append('nom', nom);
    formData.append('description', description);

    fetch('/E-STORE/controllers/AIControllers.php', {
        method: 'POST',
        body:   formData
    })
    .then(async (response) => {
        const text = await response.text();
        try {
            return JSON.parse(text);
        } catch (e) {
            throw new Error('Réponse serveur invalide : ' + text);
        }
    })
    .then(res => {
        btn.textContent = '✨ Classifier avec IA';
        btn.disabled    = false;

        if (!res || res.success !== true || !res.data) {
            throw new Error(res?.error || 'Réponse IA invalide');
        }

        const d = res.data;

        // Remplir automatiquement la catégorie
        const catSelect = document.querySelector('#modal-add select[name="category"]');
        if (catSelect && d.category) catSelect.value = d.category;

        // Afficher le résultat
        result.style.display = 'block';
        result.innerHTML = `
            <strong>✅ Classification IA terminée</strong><br>
            📦 Catégorie : <strong>${d.category ?? 'N/A'}</strong><br>
            👤 Genre : <strong>${d.genre ?? 'N/A'}</strong><br>
            🎉 Occasion : <strong>${d.occasion ?? 'N/A'}</strong><br>
            📊 Confiance : <strong>${d.confidence ?? 'N/A'}</strong><br>
            💬 <em>${d.explication ?? ''}</em>
        `;
    })
    .catch(err => {
        btn.textContent = '✨ Classifier avec IA';
        btn.disabled    = false;
        alert('Erreur IA : ' + err.message);
    });
}
</script>
</body>
</html>
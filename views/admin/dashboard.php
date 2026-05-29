<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/Product.php';
require_once '../../models/User.php';
require_once '../../models/Order.php';

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
    'populaires'   => $productModel->getPopulaires(5),
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
<link rel="stylesheet" href="../../public/css/dashboard.css">
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

</body>
</html>

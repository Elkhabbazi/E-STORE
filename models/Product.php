<?php
require_once __DIR__ . '/../config/database.php';

class Product {
    private $conn;

    public function __construct() {
        $this->conn = getConnection();
    }

    // ─────────────────────────────────────────
    // Récupérer tous les produits (filtres avancés)
    // ─────────────────────────────────────────
    public function getTous($search = '', $category = '', $sort = '', $occasion = '', $couleur = '', $region = '', $prix_min = '', $prix_max = '') {
        $where  = ["1=1"];
        $params = [];

        if ($search !== '') {
            $where[]  = "(name LIKE ? OR description LIKE ? OR category LIKE ?)";
            $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%";
        }
        if ($category !== '') { $where[] = "category LIKE ?";    $params[] = "%$category%"; }
        if ($occasion !== '') { $where[] = "occasion LIKE ?";    $params[] = "%$occasion%"; }
        if ($couleur  !== '') { $where[] = "couleur LIKE ?";     $params[] = "%$couleur%";  }
        if ($region   !== '') { $where[] = "region LIKE ?";      $params[] = "%$region%";   }
        if ($prix_min !== '') { $where[] = "price >= ?";         $params[] = (float)$prix_min; }
        if ($prix_max !== '') { $where[] = "price <= ?";         $params[] = (float)$prix_max; }

        $orderBy = $this->buildOrderBy($sort);
        $sql  = "SELECT * FROM products WHERE " . implode(" AND ", $where) . " ORDER BY $orderBy";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ─────────────────────────────────────────
    // Produits par genre avec filtres
    // ─────────────────────────────────────────
    public function getTousParGenre(array $cats, $category = '', $sort = '', $occasion = '', $couleur = '', $region = '', $prix_min = '', $prix_max = '') {
        $placeholders = implode(',', array_fill(0, count($cats), '?'));
        $where  = ["category IN ($placeholders)", "stock > 0"];
        $params = $cats;

        if ($category !== '') { $where[] = "category = ?";    $params[] = $category;          }
        if ($occasion !== '') { $where[] = "occasion LIKE ?"; $params[] = "%$occasion%";       }
        if ($couleur  !== '') { $where[] = "couleur LIKE ?";  $params[] = "%$couleur%";        }
        if ($region   !== '') { $where[] = "region LIKE ?";   $params[] = "%$region%";         }
        if ($prix_min !== '') { $where[] = "price >= ?";      $params[] = (float)$prix_min;   }
        if ($prix_max !== '') { $where[] = "price <= ?";      $params[] = (float)$prix_max;   }

        $orderBy = $this->buildOrderBy($sort);
        $sql  = "SELECT * FROM products WHERE " . implode(" AND ", $where) . " ORDER BY $orderBy";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ─────────────────────────────────────────
    // Produits Chaussures avec filtres
    // ─────────────────────────────────────────
    public function getChaussures($sous_cat = '', $sort = '', $couleur = '', $taille = '', $prix_min = '', $prix_max = '') {
        $where  = ["category = 'Chaussures'", "stock > 0"];
        $params = [];

        // sous_cat filtre sur le nom (Babouche Femme, Sandales, etc.)
        if ($sous_cat !== '') { $where[] = "name LIKE ?"; $params[] = "%$sous_cat%"; }
        if ($couleur  !== '') { $where[] = "couleur LIKE ?"; $params[] = "%$couleur%"; }
        if ($taille   !== '') { $where[] = "taille LIKE ?";  $params[] = "%$taille%";  }
        if ($prix_min !== '') { $where[] = "price >= ?";     $params[] = (float)$prix_min; }
        if ($prix_max !== '') { $where[] = "price <= ?";     $params[] = (float)$prix_max; }

        $orderBy = $this->buildOrderBy($sort);
        $sql  = "SELECT * FROM products WHERE " . implode(" AND ", $where) . " ORDER BY $orderBy";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ─────────────────────────────────────────
    // Helpers : valeurs distinctes pour les filtres
    // ─────────────────────────────────────────
    public function getCategories() {
        return $this->conn->query("SELECT DISTINCT category FROM products ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getOccasions() {
        // Valeurs normalisées (les données BDD sont composites, on propose une liste fixe)
        return ['Mariage', 'Fiançailles', 'Cérémonie', 'Henné', 'Soirée', 'Aïd', 'Ramadan', 'Quotidien', 'Vendredi'];
    }

    public function getCouleurs($category = null) {
        $sql = "SELECT DISTINCT couleur FROM products WHERE couleur IS NOT NULL AND couleur != ''";
        if ($category) { $sql .= " AND category LIKE " . $this->conn->quote("%$category%"); }
        $sql .= " ORDER BY couleur";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getTailles() {
        return ['S', 'M', 'L', 'XL', 'XXL'];
    }

    public function getRegions() {
        return $this->conn->query("SELECT DISTINCT region FROM products WHERE region IS NOT NULL AND region != '' ORDER BY region")->fetchAll(PDO::FETCH_COLUMN);
    }

    // ─────────────────────────────────────────
    // Autres requêtes
    // ─────────────────────────────────────────
    public function getParId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDerniers($limite = 3) {
        $limite = (int) $limite;
        $stmt   = $this->conn->prepare("SELECT * FROM products WHERE stock > 0 ORDER BY created_at DESC LIMIT $limite");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function compter() {
        return $this->conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
    }

    public function getPopulaires($limite = 5) {
        $limite = (int) $limite;
        $stmt   = $this->conn->prepare(
            "SELECT p.name, p.category, SUM(oi.quantity) as total_vendu
             FROM order_items oi JOIN products p ON oi.product_id = p.id
             GROUP BY p.id ORDER BY total_vendu DESC LIMIT $limite"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ─────────────────────────────────────────
    // CRUD Admin (avec couleur, taille, occasion, region)
    // ─────────────────────────────────────────
    public function ajouter($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO products (name, description, price, image, category, stock, couleur, taille, occasion, region)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['name'], $data['description'], $data['price'], $data['image'],
            $data['category'], $data['stock'],
            $data['couleur']  ?? null, $data['taille']   ?? null,
            $data['occasion'] ?? null, $data['region']   ?? null,
        ]);
        return $this->conn->lastInsertId();
    }

    public function modifier($id, $data) {
        $stmt = $this->conn->prepare(
            "UPDATE products SET name=?, description=?, price=?, image=?, category=?, stock=?,
             couleur=?, taille=?, occasion=?, region=? WHERE id=?"
        );
        $stmt->execute([
            $data['name'], $data['description'], $data['price'], $data['image'],
            $data['category'], $data['stock'],
            $data['couleur']  ?? null, $data['taille']   ?? null,
            $data['occasion'] ?? null, $data['region']   ?? null,
            $id
        ]);
    }

    public function supprimer($id) {
        $stmt = $this->conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
    }

    // ─────────────────────────────────────────
    // Helper privé : ORDER BY
    // ─────────────────────────────────────────
    private function buildOrderBy($sort) {
        switch ($sort) {
            case 'price_asc':  return 'price ASC';
            case 'price_desc': return 'price DESC';
            case 'newest':     return 'created_at DESC';
            default:           return 'created_at DESC';
        }
    }
}
?>
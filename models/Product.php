<?php
require_once __DIR__ . '/../config/database.php';

class Product {
    private $conn;

    public function __construct() {
        $this->conn = getConnection();
    }

    // ─────────────────────────────────────────
    // Récupérer tous les produits (avec filtres)
    // ─────────────────────────────────────────
    public function getTous($search = '', $category = '', $sort = '') {
        $where  = ["1=1"];
        $params = [];

        // Filtre recherche par mot-clé
        if ($search !== '') {
            $where[]  = "(name LIKE ? OR description LIKE ? OR category LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        // Filtre par catégorie
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
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ─────────────────────────────────────────
    // Récupérer un produit par son ID
    // ─────────────────────────────────────────
    public function getParId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ─────────────────────────────────────────
    // Récupérer les derniers produits (accueil)
    // ─────────────────────────────────────────
    public function getDerniers($limite = 3) {
        $limite = (int) $limite;
        $stmt = $this->conn->prepare(
            "SELECT * FROM products WHERE stock > 0 ORDER BY created_at DESC LIMIT $limite"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ─────────────────────────────────────────
    // Récupérer toutes les catégories distinctes
    // ─────────────────────────────────────────
    public function getCategories() {
        $stmt = $this->conn->query("SELECT DISTINCT category FROM products ORDER BY category");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // ─────────────────────────────────────────
    // Ajouter un produit (admin)
    // ─────────────────────────────────────────
    public function ajouter($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO products (name, description, price, image, category, stock)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['name'],
            $data['description'],
            $data['price'],
            $data['image'],
            $data['category'],
            $data['stock']
        ]);
        return $this->conn->lastInsertId();
    }

    // ─────────────────────────────────────────
    // Modifier un produit (admin)
    // ─────────────────────────────────────────
    public function modifier($id, $data) {
        $stmt = $this->conn->prepare(
            "UPDATE products SET name=?, description=?, price=?, image=?, category=?, stock=?
             WHERE id=?"
        );
        $stmt->execute([
            $data['name'],
            $data['description'],
            $data['price'],
            $data['image'],
            $data['category'],
            $data['stock'],
            $id
        ]);
    }

    // ─────────────────────────────────────────
    // Supprimer un produit (admin)
    // ─────────────────────────────────────────
    public function supprimer($id) {
        $stmt = $this->conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
    }

    // ─────────────────────────────────────────
    // Compter le total des produits
    // ─────────────────────────────────────────
    public function compter() {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM products");
        return $stmt->fetchColumn();
    }

    // ─────────────────────────────────────────
    // Produits les plus ajoutés au panier (stats admin)
    // ─────────────────────────────────────────
    public function getPopulaires($limite = 5) {
        $limite = (int) $limite;
        $stmt = $this->conn->prepare(
            "SELECT p.name, p.category, SUM(oi.quantity) as total_vendu
             FROM order_items oi
             JOIN products p ON oi.product_id = p.id
             GROUP BY p.id
             ORDER BY total_vendu DESC
             LIMIT $limite"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

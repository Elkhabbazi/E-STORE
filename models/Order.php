<?php
require_once __DIR__ . '/../config/database.php';

class Order {
    private $conn;

    public function __construct() {
        $this->conn = getConnection();
    }

    // ─────────────────────────────────────────
    // Créer une commande depuis le panier
    // ─────────────────────────────────────────
    public function creer($user_id, $total, $address) {
        // 1. Insérer la commande principale
        $stmt = $this->conn->prepare(
            "INSERT INTO orders (user_id, total_price, status, address)
             VALUES (?, ?, 'En attente', ?)"
        );
        $stmt->execute([$user_id, $total, $address]);
        $order_id = $this->conn->lastInsertId();

        // 2. Récupérer les articles du panier
        $stmt = $this->conn->prepare(
            "SELECT c.product_id, c.quantity, p.price
             FROM cart c
             JOIN products p ON c.product_id = p.id
             WHERE c.user_id = ?"
        );
        $stmt->execute([$user_id]);
        $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. Insérer chaque article dans order_items
        foreach ($cartItems as $item) {
            $stmt = $this->conn->prepare(
                "INSERT INTO order_items (order_id, product_id, quantity, price)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([
                $order_id,
                $item['product_id'],
                $item['quantity'],
                $item['price']
            ]);

            // 4. Décrémenter le stock du produit
            $stmt = $this->conn->prepare(
                "UPDATE products SET stock = stock - ? WHERE id = ?"
            );
            $stmt->execute([$item['quantity'], $item['product_id']]);
        }

        return $order_id;
    }

    // ─────────────────────────────────────────
    // Historique des commandes d'un client
    // ─────────────────────────────────────────
    public function getCommandesClient($user_id) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC"
        );
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ─────────────────────────────────────────
    // Détail d'une commande (les articles)
    // ─────────────────────────────────────────
    public function getDetailCommande($order_id) {
        $stmt = $this->conn->prepare(
            "SELECT oi.quantity, oi.price,
                    p.name, p.image, p.category,
                    (oi.price * oi.quantity) as sous_total
             FROM order_items oi
             JOIN products p ON oi.product_id = p.id
             WHERE oi.order_id = ?"
        );
        $stmt->execute([$order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ─────────────────────────────────────────
    // Toutes les commandes (admin)
    // ─────────────────────────────────────────
    public function getToutesCommandes() {
        $stmt = $this->conn->query(
            "SELECT o.*, u.name as client_name, u.email
             FROM orders o
             JOIN users u ON o.user_id = u.id
             ORDER BY o.created_at DESC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ─────────────────────────────────────────
    // Changer le statut d'une commande (admin)
    // ─────────────────────────────────────────
    public function changerStatut($order_id, $statut) {
        $statutsValides = ['En attente', 'Confirmée', 'Expédiée', 'Livrée', 'Annulée'];
        if (!in_array($statut, $statutsValides)) return false;

        $stmt = $this->conn->prepare(
            "UPDATE orders SET status = ? WHERE id = ?"
        );
        $stmt->execute([$statut, $order_id]);
        return true;
    }

    // ─────────────────────────────────────────
    // Compter toutes les commandes (stats)
    // ─────────────────────────────────────────
    public function compter() {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM orders");
        return $stmt->fetchColumn();
    }

    // ─────────────────────────────────────────
    // Chiffre d'affaires total (stats)
    // ─────────────────────────────────────────
    public function getChiffreAffaires() {
        $stmt = $this->conn->query(
            "SELECT SUM(total_price) FROM orders WHERE status != 'Annulée'"
        );
        return $stmt->fetchColumn() ?? 0;
    }
}
?>

<?php
require_once __DIR__ . '/../config/database.php';

class Cart {
    private $conn;

    public function __construct() {
        $this->conn = getConnection();
    }

    // ─────────────────────────────────────────
    // Récupérer le panier d'un utilisateur
    // On fait un JOIN pour avoir les infos du produit
    // ─────────────────────────────────────────
    public function getPanier($user_id) {
        $stmt = $this->conn->prepare(
            "SELECT c.id as cart_id,
                    c.quantity,
                    p.id as product_id,
                    p.name,
                    p.price,
                    p.image,
                    p.category,
                    p.stock,
                    (p.price * c.quantity) as sous_total
             FROM cart c
             JOIN products p ON c.product_id = p.id
             WHERE c.user_id = ?
             ORDER BY c.id DESC"
        );
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ─────────────────────────────────────────
    // Calculer le total du panier
    // ─────────────────────────────────────────
    public function getTotal($user_id) {
        $stmt = $this->conn->prepare(
            "SELECT SUM(p.price * c.quantity) as total
             FROM cart c
             JOIN products p ON c.product_id = p.id
             WHERE c.user_id = ?"
        );
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    // ─────────────────────────────────────────
    // Compter le nombre d'articles dans le panier
    // ─────────────────────────────────────────
    public function compterArticles($user_id) {
        $stmt = $this->conn->prepare(
            "SELECT SUM(quantity) FROM cart WHERE user_id = ?"
        );
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn() ?? 0;
    }

    // ─────────────────────────────────────────
    // Ajouter un produit au panier
    // ─────────────────────────────────────────
    public function ajouter($user_id, $product_id) {
        // Vérifier si le produit est déjà dans le panier
        $stmt = $this->conn->prepare(
            "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?"
        );
        $stmt->execute([$user_id, $product_id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            // Déjà dans le panier → augmenter la quantité de 1
            $stmt = $this->conn->prepare(
                "UPDATE cart SET quantity = quantity + 1 WHERE id = ?"
            );
            $stmt->execute([$item['id']]);
        } else {
            // Pas encore dans le panier → insérer
            $stmt = $this->conn->prepare(
                "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)"
            );
            $stmt->execute([$user_id, $product_id]);
        }
    }

    // ─────────────────────────────────────────
    // Modifier la quantité d'un article
    // ─────────────────────────────────────────
    public function modifierQuantite($user_id, $product_id, $quantity) {
        if ($quantity <= 0) {
            // Si quantité = 0 ou moins → supprimer l'article
            $this->supprimer($user_id, $product_id);
        } else {
            $stmt = $this->conn->prepare(
                "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?"
            );
            $stmt->execute([$quantity, $user_id, $product_id]);
        }
    }

    // ─────────────────────────────────────────
    // Supprimer un article du panier
    // ─────────────────────────────────────────
    public function supprimer($user_id, $product_id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM cart WHERE user_id = ? AND product_id = ?"
        );
        $stmt->execute([$user_id, $product_id]);
    }

    // ─────────────────────────────────────────
    // Vider tout le panier (après commande)
    // ─────────────────────────────────────────
    public function vider($user_id) {
        $stmt = $this->conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);
    }
}
?>

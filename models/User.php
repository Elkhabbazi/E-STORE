<?php
require_once __DIR__ . '/../config/database.php';

class User {
    private $conn;

    // Le constructeur récupère la connexion à la BDD
    public function __construct() {
        $this->conn = getConnection();
    }

    // ─────────────────────────────────────────
    // Inscrire un nouvel utilisateur
    // ─────────────────────────────────────────
    public function inscrire($name, $email, $password) {
        // Vérifier si l'email existe déjà
        if ($this->emailExiste($email)) {
            return ['succes' => false, 'message' => 'Cet email est déjà utilisé.'];
        }

        // Hasher le mot de passe (ne jamais stocker en clair !)
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare(
            "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'client')"
        );
        $stmt->execute([$name, $email, $hashedPassword]);

        return ['succes' => true, 'message' => 'Inscription réussie !'];
    }

    // ─────────────────────────────────────────
    // Connecter un utilisateur
    // ─────────────────────────────────────────
    public function connecter($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérifier que l'utilisateur existe ET que le mot de passe est correct
        if (!$user || !password_verify($password, $user['password'])) {
            return ['succes' => false, 'message' => 'Email ou mot de passe incorrect.'];
        }

        return ['succes' => true, 'user' => $user];
    }

    // ─────────────────────────────────────────
    // Vérifier si un email existe déjà
    // ─────────────────────────────────────────
    public function emailExiste($email) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    }

    // ─────────────────────────────────────────
    // Récupérer tous les clients (pour l'admin)
    // ─────────────────────────────────────────
    public function getTousLesClients() {
        $stmt = $this->conn->query(
            "SELECT id, name, email, created_at FROM users WHERE role = 'client' ORDER BY created_at DESC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ─────────────────────────────────────────
    // Compter le nombre total de clients
    // ─────────────────────────────────────────
    public function compterClients() {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM users WHERE role = 'client'");
        return $stmt->fetchColumn();
    }

    // ─────────────────────────────────────────
    // Supprimer un client (admin uniquement)
    // ─────────────────────────────────────────
    public function supprimerClient($id) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ? AND role = 'client'");
        $stmt->execute([$id]);
    }
}
?>

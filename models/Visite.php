<?php
require_once __DIR__ . '/../config/database.php';

class Visite {
    private $conn;

    public function __construct() {
        $this->conn = getConnection();
    }

    // ─────────────────────────────────────────
    // Enregistrer une visite
    // ─────────────────────────────────────────
    public function enregistrer($page) {
        // Récupérer l'IP du visiteur
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] 
              ?? $_SERVER['REMOTE_ADDR'] 
              ?? '0.0.0.0';

        // Prendre seulement la première IP si plusieurs
        $ip = explode(',', $ip)[0];
        $ip = trim($ip);

        // Géolocalisation via ip-api.com (gratuit, sans clé API)
        $pays = 'Inconnu';
        $ville = 'Inconnue';

        try {
            $json = @file_get_contents("http://ip-api.com/json/{$ip}?lang=fr&fields=country,city,status");
            if ($json) {
                $data = json_decode($json, true);
                if ($data && $data['status'] === 'success') {
                    $pays  = $data['country'] ?? 'Inconnu';
                    $ville = $data['city']    ?? 'Inconnue';
                }
            }
        } catch (Exception $e) {
            // Si l'API échoue, on continue sans géoloc
        }

        // Insérer en BDD
        $stmt = $this->conn->prepare(
            "INSERT INTO visites (ip, pays, ville, page) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$ip, $pays, $ville, $page]);
    }

    // ─────────────────────────────────────────
    // Compter le total des visites
    // ─────────────────────────────────────────
    public function compterTotal() {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM visites");
        return $stmt->fetchColumn();
    }

    // ─────────────────────────────────────────
    // Visites d'aujourd'hui
    // ─────────────────────────────────────────
    public function compterAujourdhui() {
        $stmt = $this->conn->query(
            "SELECT COUNT(*) FROM visites WHERE DATE(created_at) = CURDATE()"
        );
        return $stmt->fetchColumn();
    }

    // ─────────────────────────────────────────
    // Pages les plus visitées
    // ─────────────────────────────────────────
    public function getPagesPopulaires() {
        $stmt = $this->conn->query(
            "SELECT page, COUNT(*) as nb
             FROM visites
             GROUP BY page
             ORDER BY nb DESC
             LIMIT 5"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ─────────────────────────────────────────
    // Répartition par pays (géolocalisation)
    // ─────────────────────────────────────────
    public function getParPays() {
        $stmt = $this->conn->query(
            "SELECT pays, COUNT(*) as nb
             FROM visites
             GROUP BY pays
             ORDER BY nb DESC
             LIMIT 10"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ─────────────────────────────────────────
    // Visites des 7 derniers jours (pour graphique)
    // ─────────────────────────────────────────
    public function getVisitesSemaine() {
        $stmt = $this->conn->query(
            "SELECT DATE(created_at) as jour, COUNT(*) as nb
             FROM visites
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
             GROUP BY DATE(created_at)
             ORDER BY jour ASC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
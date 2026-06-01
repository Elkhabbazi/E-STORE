<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

define('GROQ_API_KEY', '');
define('GROQ_MODEL',   'meta-llama/llama-4-scout-17b-16e-instruct');

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['error' => 'Aucune image reçue']);
    exit;
}

$allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
if (!in_array($_FILES['image']['type'], $allowedTypes)) {
    echo json_encode(['error' => 'Format non supporté.']);
    exit;
}

$imageData   = file_get_contents($_FILES['image']['tmp_name']);
$base64Image = base64_encode($imageData);
$mimeType    = $_FILES['image']['type'];

$prompt = "Tu es un expert en mode marocaine traditionnelle.
Analyse cette photo et réponds UNIQUEMENT en JSON valide, sans texte avant ou après, sans markdown.

Voici comment distinguer les catégories :
- Caftan : robe longue une pièce, brodée, portée par les femmes
- Takchita : ensemble DEUX pièces superposées, avec une robe en dessous et un manteau par dessus, portée par les femmes
- Jellaba : vêtement long avec capuche (pour homme OU femme)
- Jabador : ensemble deux pièces (veste + pantalon) pour homme
- Chaussures : babouches, sandales, chaussures marocaines
- Autre : si aucune catégorie ne correspond

Réponds avec ce format exact :
{
  \"category\": \"Caftan\",
  \"genre\": \"Femme\",
  \"couleur\": \"rouge\",
  \"occasion\": \"Mariage\",
  \"description\": \"Une phrase décrivant la tenue\"
}

Valeurs possibles :
- category : Caftan, Takchita, Jellaba, Jabador, Chaussures, Autre
- genre : Femme, Homme, Mixte
- occasion : Mariage, Fête, Quotidien, Cérémonie, Casual
- couleur : la couleur dominante en français";

$body = json_encode([
    'model'    => GROQ_MODEL,
    'messages' => [[
        'role'    => 'user',
        'content' => [
            [
                'type'      => 'image_url',
                'image_url' => ['url' => "data:{$mimeType};base64,{$base64Image}"]
            ],
            ['type' => 'text', 'text' => $prompt]
        ]
    ]],
    'max_tokens'  => 300,
    'temperature' => 0.1
]);

$ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $body,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . GROQ_API_KEY
    ],
    CURLOPT_TIMEOUT => 20
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr  = curl_error($ch);
curl_close($ch);

if ($curlErr) {
    echo json_encode(['error' => 'Erreur réseau : ' . $curlErr]);
    exit;
}

if ($httpCode !== 200) {
    $errData = json_decode($response, true);
    $errMsg  = $errData['error']['message'] ?? 'Erreur API ' . $httpCode;
    echo json_encode(['error' => $errMsg]);
    exit;
}

$result  = json_decode($response, true);
$content = $result['choices'][0]['message']['content'] ?? '';
$content = preg_replace('/```json|```/', '', $content);
$content = trim($content);

$parsed = json_decode($content, true);

if (!$parsed) {
    echo json_encode(['error' => 'IA invalide', 'raw' => $content]);
    exit;
}

$category = $parsed['category'] ?? '';
$genre    = $parsed['genre']    ?? '';
$couleur  = $parsed['couleur']  ?? '';

// ── Recherche dans la BDD ──
$conn     = getConnection();
$products = [];

// Étape 1 : chercher par catégorie ET genre dans le nom
if ($category && $category !== 'Autre' && $genre && $genre !== 'Mixte') {
    $stmt = $conn->prepare(
        "SELECT * FROM products 
         WHERE stock > 0 
         AND category LIKE ? 
         AND name LIKE ?
         ORDER BY created_at DESC LIMIT 6"
    );
    $stmt->execute(["%$category%", "%$genre%"]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Étape 2 : si rien trouvé → catégorie seule
if (empty($products) && $category && $category !== 'Autre') {
    $stmt = $conn->prepare(
        "SELECT * FROM products 
         WHERE stock > 0 
         AND category LIKE ?
         ORDER BY created_at DESC LIMIT 6"
    );
    $stmt->execute(["%$category%"]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Étape 3 : si rien trouvé → couleur
if (empty($products) && $couleur) {
    $stmt = $conn->prepare(
        "SELECT * FROM products 
         WHERE stock > 0 
         AND (couleur LIKE ? OR name LIKE ?)
         ORDER BY created_at DESC LIMIT 6"
    );
    $stmt->execute(["%$couleur%", "%$couleur%"]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Étape 4 : fallback final → derniers produits
if (empty($products)) {
    $stmt     = $conn->query(
        "SELECT * FROM products WHERE stock > 0 ORDER BY created_at DESC LIMIT 6"
    );
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode([
    'success'  => true,
    'detected' => [
        'category'    => $category,
        'genre'       => $genre,
        'couleur'     => $couleur,
        'occasion'    => $parsed['occasion']    ?? '',
        'description' => $parsed['description'] ?? ''
    ],
    'products' => $products
]);
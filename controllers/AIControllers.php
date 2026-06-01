<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['error' => 'Non autorisé']); exit;
}

$nom         = strtolower(trim($_POST['nom']         ?? ''));
$description = strtolower(trim($_POST['description'] ?? ''));
$texte       = $nom . ' ' . $description;

if (empty(trim($texte))) {
    echo json_encode(['error' => 'Nom ou description requis']); exit;
}

function classifier($texte) {

    // ── 1. CATÉGORIE (scores pondérés) ───────────────────────
    $scoresCat = [
        'Caftan'        => 0, 'Takchita'      => 0,
        'Jellaba Femme' => 0, 'Jellaba Homme' => 0,
        'Jabador'       => 0, 'Chaussures'    => 0,
        'Burnous'       => 0,
    ];
    $motsCat = [
        'Caftan'        => ['caftan'=>3,'kaftan'=>3,'ceinture'=>1,'brodé'=>1,'haute couture'=>2],
        'Takchita'      => ['takchita'=>3,'deux pièces'=>2,'dfol'=>2,'henna'=>1,'henné'=>1],
        'Jellaba Femme' => ['djellaba femme'=>3,'jellaba femme'=>3,'jellaba rose'=>2,'jellaba mauve'=>2],
        'Jellaba Homme' => ['djellaba homme'=>3,'jellaba homme'=>3,'gandoura'=>2],
        'Jabador'       => ['jabador'=>3,'qamis'=>2,'tunique homme'=>2],
        'Chaussures'    => ['babouche'=>3,'sandales'=>3,'escarpins'=>3,'mocassins'=>2,'ballerines'=>2,'mules'=>2,'chaussures'=>2],
        'Burnous'       => ['burnous'=>3,'bernous'=>3,'manteau traditionnel'=>2],
    ];
    foreach ($motsCat as $cat => $mots) {
        foreach ($mots as $mot => $poids) {
            if (strpos($texte, $mot) !== false) $scoresCat[$cat] += $poids;
        }
    }
    arsort($scoresCat);
    $topScore = reset($scoresCat);
    $category = $topScore > 0 ? array_key_first($scoresCat) : 'Autre';

    // ── 2. GENRE (scores pondérés) ───────────────────────────
    $scoreFemme = 0; $scoreHomme = 0;
    $motsFemme = ['femme'=>3,'féminin'=>2,'mariée'=>2,'caftan'=>2,'takchita'=>3,'djellaba femme'=>3,'sandales'=>2,'escarpins'=>3,'ballerines'=>3,'mules'=>2];
    $motsHomme = ['homme'=>3,'masculin'=>2,'marié'=>2,'jabador'=>3,'burnous'=>3,'djellaba homme'=>3,'jellaba homme'=>3,'mocassins'=>2];
    foreach ($motsFemme as $m => $p) { if (strpos($texte, $m) !== false) $scoreFemme += $p; }
    foreach ($motsHomme as $m => $p) { if (strpos($texte, $m) !== false) $scoreHomme += $p; }
    if (in_array($category, ['Takchita','Jellaba Femme'])) $scoreFemme += 5;
    if (in_array($category, ['Jabador','Jellaba Homme','Burnous'])) $scoreHomme += 5;
    if ($scoreFemme > $scoreHomme)      $genre = 'Femme';
    elseif ($scoreHomme > $scoreFemme)  $genre = 'Homme';
    else                                $genre = 'Mixte';

    // ── 3. OCCASION (scores pondérés) ────────────────────────
    $scoresOcc = ['Mariage'=>0,'Fiançailles'=>0,'Cérémonie'=>0,'Henné'=>0,'Soirée'=>0,'Aïd'=>0,'Ramadan'=>0,'Quotidien'=>0];
    $motsOcc = [
        'Mariage'    => ['mariage'=>3,'noces'=>2,'mariée'=>2,'marié'=>2],
        'Fiançailles'=> ['fiançailles'=>3,'fiancée'=>3,'khotoba'=>3],
        'Cérémonie'  => ['cérémonie'=>3,'ceremonie'=>3,'officiel'=>2,'gala'=>2,'prestige'=>1],
        'Henné'      => ['henné'=>3,'henna'=>3],
        'Soirée'     => ['soirée'=>3,'cocktail'=>2,'réception'=>2,'reception'=>2],
        'Aïd'        => ['aïd'=>3,'aid'=>3,'eid'=>3],
        'Ramadan'    => ['ramadan'=>3,'iftar'=>2],
        'Quotidien'  => ['quotidien'=>3,'casual'=>2,'jour'=>1,'promenade'=>2],
    ];
    foreach ($motsOcc as $occ => $mots) {
        foreach ($mots as $mot => $poids) {
            if (strpos($texte, $mot) !== false) $scoresOcc[$occ] += $poids;
        }
    }
    arsort($scoresOcc);
    $topOccScore = reset($scoresOcc);
    $occasion = $topOccScore > 0 ? array_key_first($scoresOcc) : 'Quotidien';

    // ── 4. RÉGION ────────────────────────────────────────────
    $region = null;
    $villes = ['fès'=>'Fès','marrakech'=>'Marrakech','rabat'=>'Rabat','casablanca'=>'Casablanca',
               'tanger'=>'Tanger','tétouan'=>'Tétouan','meknès'=>'Meknès','salé'=>'Salé',
               'chefchaouen'=>'Chefchaouen','oujda'=>'Oujda'];
    foreach ($villes as $mot => $val) {
        if (strpos($texte, $mot) !== false) { $region = $val; break; }
    }

    // ── 5. CONFIANCE ─────────────────────────────────────────
    $total = $topScore + $topOccScore + max($scoreFemme, $scoreHomme);
    if ($total >= 8)     $confidence = 'Très haute';
    elseif ($total >= 5) $confidence = 'Haute';
    elseif ($total >= 2) $confidence = 'Moyenne';
    else                 $confidence = 'Faible';

    // ── 6. EXPLICATION ───────────────────────────────────────
    $explications = [
        'Caftan'        => 'Le terme "caftan" ou des attributs typiques (broderies, ceinture) ont été détectés.',
        'Takchita'      => '"Takchita" ou "deux pièces" détecté — tenue féminine de cérémonie.',
        'Jellaba Femme' => '"Jellaba femme" ou équivalent féminin détecté.',
        'Jellaba Homme' => '"Jellaba homme" ou "gandoura" détecté.',
        'Jabador'       => '"Jabador" détecté — tenue masculine traditionnelle.',
        'Chaussures'    => 'Babouche, sandales, escarpins ou autre type de chaussure détecté.',
        'Burnous'       => '"Burnous" détecté — manteau traditionnel masculin.',
        'Autre'         => 'Aucun mot-clé dominant — vérifiez manuellement la catégorie.',
    ];

    return [
        'category'    => $category,
        'genre'       => $genre,
        'occasion'    => $occasion,
        'region'      => $region,
        'confidence'  => $confidence,
        'explication' => $explications[$category] ?? 'Classification automatique.',
    ];
}

$result = classifier($texte);
echo json_encode(['success' => true, 'data' => $result]);
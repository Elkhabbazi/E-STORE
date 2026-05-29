<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/csrf.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifierTokenCSRF();
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $errors[] = "Tous les champs sont obligatoires.";
    }

    if (empty($errors)) {
        $conn = getConnection();

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $errors[] = "Aucun compte trouvé avec cet email.";
        } else {
            if (!password_verify($password, $user['password'])) {
                $errors[] = "Mot de passe incorrect.";
            } else {
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    header("Location: ../admin/dashboard.php");
                } else {
                    header("Location: ../products/index.php");
                }
                exit();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Dar Al Caftan</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
        <link rel="stylesheet" href="../../public/css/login.css">

</head>
<body>

    <div class="card">

        <!-- Logo -->
        <div class="logo-block">
            <svg width="86" height="86" viewBox="0 0 86 86">
                <polygon points="43,4 80,23 80,63 43,82 6,63 6,23" fill="#FFF8F0" stroke="#C4956A" stroke-width="1"/>
                <polygon points="43,13 70,28 70,58 43,73 16,58 16,28" fill="none" stroke="rgba(196,149,106,0.28)" stroke-width="0.6"/>
                <text x="43" y="39" text-anchor="middle" font-family="'Playfair Display', serif" font-size="15" font-weight="500" fill="#8B4513" letter-spacing="2">DAC</text>
                <line x1="28" y1="46" x2="58" y2="46" stroke="#C4956A" stroke-width="0.7"/>
                <text x="43" y="58" text-anchor="middle" font-family="'DM Sans', sans-serif" font-size="6" font-weight="300" fill="#C4956A" letter-spacing="3">MAROC</text>
                <circle cx="43" cy="4"  r="2.5" fill="#C4956A"/>
                <circle cx="80" cy="23" r="2.5" fill="#C4956A"/>
                <circle cx="80" cy="63" r="2.5" fill="#C4956A"/>
                <circle cx="43" cy="82" r="2.5" fill="#C4956A"/>
                <circle cx="6"  cy="63" r="2.5" fill="#C4956A"/>
                <circle cx="6"  cy="23" r="2.5" fill="#C4956A"/>
            </svg>
            <span class="brand">Dar Al Caftan</span>
            <span class="tagline">L'élégance marocaine authentique</span>
        </div>

        <div class="divider"><div class="divider-gem"></div></div>

        <!-- Formulaire -->
        <div class="form-box">
            <div class="stripe"></div>
            <div class="corner tl"></div>
            <div class="corner tr"></div>
            <div class="corner bl"></div>
            <div class="corner br"></div>

            <p class="form-title">Bon retour</p>
            <p class="form-sub">Connectez-vous à votre espace</p>

            <?php if (!empty($errors)): ?>
                <div class="errors">
                    <?php foreach ($errors as $error): ?>
                        <p>⚠ <?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                 <?= champCSRF() ?>
                <div class="field">
                    <label for="email">Adresse email</label>
                    <div class="input-wrap">
                        <i class="ti ti-mail"></i>
                        <input type="email" id="email" name="email" placeholder="votre@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                </div>

                <div class="field">
                    <label for="password">Mot de passe</label>
                    <div class="input-wrap">
                        <i class="ti ti-lock"></i>
                        <input type="password" id="password" name="password" placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" class="btn-submit">Se connecter</button>

            </form>

            <div class="sep"><span>ou</span></div>

            <p class="link-line">Pas encore membre ? <a href="register.php">Créer un compte</a></p>
        </div>

        <p class="footer">© 2026 Dar Al Caftan — Tous droits réservés</p>
    </div>

</body>
</html>
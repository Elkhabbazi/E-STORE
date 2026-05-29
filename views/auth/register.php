<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/csrf.php';
$errors  = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifierTokenCSRF();
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm']);

    // Vérifications
    if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
        $errors[] = "Tous les champs sont obligatoires.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'adresse email n'est pas valide.";
    }

    if ($password !== $confirm) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
    }

    if (empty($errors)) {
        $conn = getConnection();

        // Vérifier si l'email existe déjà
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $errors[] = "Cet email est déjà utilisé.";
        } else {
            // Hasher le mot de passe et insérer
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'client')");
            $stmt->execute([$name, $email, $hashedPassword]);

            $success = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription — Dar Al Caftan</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="../../public/css/register.css">

        
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

            <p class="form-title">Créer un compte</p>
            <p class="form-sub">Rejoignez notre communauté</p>

            <!-- Message succès -->
            <?php if ($success): ?>
                <div class="success-box">
                    <p>✓ <?= htmlspecialchars($success) ?></p>
                    <a href="login.php">Se connecter maintenant</a>
                </div>
            <?php endif; ?>

            <!-- Erreurs -->
            <?php if (!empty($errors)): ?>
                <div class="errors">
                    <?php foreach ($errors as $error): ?>
                        <p>⚠ <?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!$success): ?>
            <form method="POST" action="">
                <?= champCSRF() ?>
                <!-- Nom complet -->
                <div class="field">
                    <label for="name">Nom complet</label>
                    <div class="input-wrap">
                        <i class="ti ti-user"></i>
                        <input type="text" id="name" name="name" placeholder="Votre nom et prénom" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                    </div>
                </div>

                <!-- Email -->
                <div class="field">
                    <label for="email">Adresse email</label>
                    <div class="input-wrap">
                        <i class="ti ti-mail"></i>
                        <input type="email" id="email" name="email" placeholder="votre@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                </div>

                <!-- Mot de passe -->
                <div class="field">
                    <label for="password">Mot de passe</label>
                    <div class="input-wrap">
                        <i class="ti ti-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Minimum 6 caractères" oninput="checkStrength(this.value)">
                    </div>
                    <!-- Indicateur de force -->
                    <div class="password-strength">
                        <div class="strength-bar" id="bar1"></div>
                        <div class="strength-bar" id="bar2"></div>
                        <div class="strength-bar" id="bar3"></div>
                    </div>
                    <p class="strength-text" id="strength-text"></p>
                </div>

                <!-- Confirmer mot de passe -->
                <div class="field">
                    <label for="confirm">Confirmer le mot de passe</label>
                    <div class="input-wrap">
                        <i class="ti ti-lock-check"></i>
                        <input type="password" id="confirm" name="confirm" placeholder="Répétez votre mot de passe">
                    </div>
                </div>

                <!-- Conditions -->
                <div class="terms">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">J'accepte les <a href="#">conditions d'utilisation</a> et la <a href="#">politique de confidentialité</a></label>
                </div>

                <button type="submit" class="btn-submit">Créer mon compte</button>

            </form>
            <?php endif; ?>

            <div class="sep"><span>ou</span></div>

            <p class="link-line">Déjà membre ? <a href="login.php">Se connecter</a></p>
        </div>

        <p class="footer">© 2026 Dar Al Caftan — Tous droits réservés</p>
    </div>

    <script>
        function checkStrength(password) {
            const bar1 = document.getElementById('bar1');
            const bar2 = document.getElementById('bar2');
            const bar3 = document.getElementById('bar3');
            const text = document.getElementById('strength-text');

            // Réinitialiser
            [bar1, bar2, bar3].forEach(b => b.className = 'strength-bar');
            text.textContent = '';

            if (password.length === 0) return;

            let score = 0;
            if (password.length >= 6)  score++;
            if (password.length >= 10) score++;
            if (/[A-Z]/.test(password) && /[0-9]/.test(password)) score++;

            if (score === 1) {
                bar1.classList.add('active-weak');
                text.textContent = 'Faible';
                text.style.color = '#D4845A';
            } else if (score === 2) {
                bar1.classList.add('active-medium');
                bar2.classList.add('active-medium');
                text.textContent = 'Moyen';
                text.style.color = '#C4956A';
            } else if (score === 3) {
                bar1.classList.add('active-strong');
                bar2.classList.add('active-strong');
                bar3.classList.add('active-strong');
                text.textContent = 'Fort';
                text.style.color = '#6B8F5E';
            }
        }
    </script>

</body>
</html>
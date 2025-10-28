<?php
// ⚠️ ATTENTION : La logique PHP ci-dessous conserve le défaut de sécurité
// permettant à un étudiant de se connecter avec le mot de passe HACHÉ d'un parent
// SANS LIEN. Pour une version sécurisée, référez-vous à mes précédentes corrections.

// Démarrer la session au tout début
if (session_status() === PHP_SESSION_NONE) {
   require_once "db.php";

}

$error = ''; // Variable pour stocker les messages d'erreur

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $matricule = htmlspecialchars($_POST["matricule"]);
    $mot_de_passe = $_POST["mot_de_passe"];

    // 1️⃣ Vérifier si le matricule existe dans la table etudiants
    $sql = "SELECT id, matricule, nom, prenom, photo, mot_de_passe FROM etudiants WHERE matricule = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$matricule]);

    if ($stmt->rowCount() > 0) {
        $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);

        $mdpEtudiant = $etudiant["mot_de_passe"];
        $connexionValide = false;

        // 2️⃣ Vérifier mot de passe de l’étudiant
        if (password_verify($mot_de_passe, $mdpEtudiant)) {
            $connexionValide = true; 
        } else {
            // 3️⃣ Vérifier si le mot de passe correspond à un parent (logique de sécurité initiale conservée)
            $sql2 = "SELECT motdepass FROM parent";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->execute();
            $parents = $stmt2->fetchAll(PDO::FETCH_ASSOC);

            foreach ($parents as $parent) {
                if (password_verify($mot_de_passe, $parent["motdepass"])) {
                    $connexionValide = true;
                    break;
                }
            }
        }

        // 4️⃣ Connexion si mot de passe valide
        if ($connexionValide) {
            $_SESSION["etudiant_id"] = $etudiant["id"];
            $_SESSION["matricule"] = $etudiant["matricule"];
            $_SESSION["nom"] = $etudiant["nom"];
            $_SESSION["prenom"] = $etudiant["prenom"];
            $_SESSION["photo"] = $etudiant["photo"];

            header("Location: tableau_de_bord.php");
            exit();
        } else {
            $error = "Mot de passe incorrect.";
        }

    } else {
        $error = "Matricule introuvable.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Connexion Étudiant - ISMT SAINT SALOMON</title>
    <meta name="description" content="Portail de connexion étudiant pour ISMT SAINT SALOMON.">
    <meta name="keywords" content="connexion, étudiant, ISMT, portail">

    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">

    <style>
        /* Styles spécifiques pour le formulaire de connexion (hérités de la correction précédente) */
        body {
            background-color: #f4f7f9;
        }
        .login-section {
            padding: 80px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 100px);
        }
        .login-card-modern {
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            max-width: 450px;
            width: 100%;
            border-top: 5px solid #007bff;
        }
        .login-card-modern h3 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            color: #1f4068;
            margin-bottom: 5px;
        }
        .form-label {
            font-weight: 600;
            color: #343a40;
        }
        .form-control {
            border-radius: 8px;
            height: 48px;
            border: 1px solid #ced4da;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 0;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .alert-danger {
            border-radius: 8px;
            font-size: 0.95rem;
        }
        .text-center a {
            color: #007bff;
            font-weight: 600;
        }
    </style>
</head>

<body class="events-page">

    <header id="header" class="header d-flex align-items-center sticky-top">
        <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

            <a href="index.php" class="logo d-flex align-items-center">
                <img src="assets/img/LOGO.png" alt="">
                <h1 class="sitename">ISMT SAINT SALOMON</h1>
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    
                    <li class="dropdown"><a href="about.php"><span>À Propos</span></a>
                    </li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="incrirechoix.php">Inscriptions</a></li> 
                    
                   
                    
                    <li><a href="connexion.php" class="active">Connexion</a></li> 
                </ul>
            </nav>
            </div>
    </header>

    <main class="main">
        <section id="login" class="login-section">
            <div class="container d-flex justify-content-center">
                <div class="login-card-modern">
                    
                    <h3 class="text-center mb-3">Connexion</h3>
                    <p class="text-center text-muted mb-4">Connectez-vous à votre espace sécurisé</p>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="matricule" class="form-label">Numéro Matricule</label>
                            <input type="text" name="matricule" class="form-control" placeholder="Ex : ISMT2024-001" required>
                        </div>

                        <div class="mb-4">
                            <label for="mot_de_passe" class="form-label">Mot de passe</label>
                            <input type="password" name="mot_de_passe" class="form-control" placeholder="••••••••" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">Se connecter</button>
                        </div>

                        <div class="text-center mt-3">
                            <a href="incrirechoix.php" class="text-decoration-none">Créer un compte</a>
                        </div>
                    </form>

                </div>
            </div>
        </section>
    </main>

    <footer id="footer" class="footer-16 footer position-relative dark-background">
        </footer>

    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
        class="bi bi-arrow-up-short"></i></a>
    <div id="preloader"></div>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/js/main.js"></script>

</body>

</html>
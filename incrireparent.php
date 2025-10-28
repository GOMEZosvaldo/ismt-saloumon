<?php
// Démarrer la session au tout début
if (session_status() === PHP_SESSION_NONE) {
    require_once "db.php"; 
}
// Assurez-vous que ce fichier contient la connexion PDO ($conn)

$error = '';
// $success = ''; // Cette variable n'est plus nécessaire car on redirige

// Tableau pour stocker les données du formulaire en cas d'erreur
$form_data = [
    'nom' => '', 'prenom' => '', 'email' => '', 'telephone' => ''
];

// Si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 1. Assainissement des entrées et récupération des données
    $form_data['nom'] = htmlspecialchars($_POST["nom"] ?? '');
    $form_data['prenom'] = htmlspecialchars($_POST["prenom"] ?? '');
    $form_data['email'] = htmlspecialchars($_POST["email"] ?? '');
    $form_data['telephone'] = htmlspecialchars($_POST["telephone"] ?? '');
    $mot_de_passe = $_POST["mot_de_passe"] ?? '';
    $mot_de_passe_conf = $_POST["mot_de_passe_conf"] ?? '';

    // 2. Vérification des mots de passe
    if (empty($mot_de_passe) || $mot_de_passe !== $mot_de_passe_conf) {
        $error = "Les mots de passe ne correspondent pas ou sont vides.";
    } else {
        // 3. Vérifier si l'email existe déjà (avec requête préparée pour prévenir SQL Injection)
        $check = $conn->prepare("SELECT id_par FROM parent WHERE email = ?");
        $check->execute([$form_data['email']]);
        
        if ($check->rowCount() > 0) {
            $error = "Cet email est déjà utilisé. Veuillez vous connecter.";
        } else {
            // 4. Hash du mot de passe (CRITIQUE pour la sécurité)
            $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

            try {
                // 5. Insertion dans la base
                $sql = "INSERT INTO parent (nom, prenom, email, tel, motdepass)
                         VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    $form_data['nom'], 
                    $form_data['prenom'], 
                    $form_data['email'], 
                    $form_data['telephone'], 
                    $hash
                ]);

                // 6. Succès et REDIRECTION vers la page de connexion
                // On ajoute un paramètre GET 'success' pour afficher un message sur la page de connexion
                header("Location: connexion.php?status=success");
                exit();

            } catch (PDOException $e) {
                // Gestion des erreurs PDO
                $error = "Erreur d'inscription: Impossible d'enregistrer les données. Veuillez réessayer.";
                // Pour le débogage, utiliser : $error = "Erreur... (" . $e->getMessage() . ")";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Inscription Parent - ISMT SAINT SALOMON</title>
    <meta name="description" content="Inscrivez-vous en tant que parent pour suivre le parcours de votre enfant à l'ISMT SAINT SALOMON.">
    <meta name="keywords" content="inscription parent, ISMT, suivi étudiant">

    <link href="assets/img/LOGO-removebg-preview.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">

    <link href="assets/css/main.css" rel="stylesheet">
    
    <style>
      /* Style spécifique pour la carte d'inscription */
      .register-card {
          max-width: 700px;
          margin: 0 auto;
          padding: 30px;
          border: 1px solid #e0e0e0;
          border-radius: 8px;
          box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
          background-color: #fff;
      }
    </style>
</head>

<body class="events-page">

<header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">
        <a href="index.php" class="logo d-flex align-items-center">
            <img src="assets/img/LOGO.png" alt="Logo ISMT Saint Salomon">
            <h1 class="sitename">ISMT SAINT SALOMON</h1>
        </a>

        <nav id="navmenu" class="navmenu">
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li class="dropdown text-capitalize"><a href="about.php"><span>À Propos</span></a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="incrirechoix.php" >Inscriptions</a></li>
                <li><a href="connexion.php">Connexion Parent</a></li> 
            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>
    </div>
</header>
<main class="main">
    <section id="inscription" class="contact section">
        <div class="container">
            <div class="section-header">
                <h2>Inscription Parent</h2>
                <p>Créez votre compte pour suivre la scolarité de votre enfant.</p>
            </div>

            <section class="container py-5">
                <div class="register-card">
                    <h3 class="text-center mb-4 text-primary">Création de Compte Parent</h3>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger text-center" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> **Erreur :** <?= $error ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action=""> <div class="row g-3">
                            
                            <div class="col-md-6">
                                <label for="nom" class="form-label">Nom</label>
                                <input type="text" id="nom" name="nom" class="form-control" required 
                                        value="<?= $form_data['nom'] ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="prenom" class="form-label">Prénom</label>
                                <input type="text" id="prenom" name="prenom" class="form-control" required 
                                        value="<?= $form_data['prenom'] ?>">
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="exemple@mail.com" required 
                                        value="<?= $form_data['email'] ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="tel" id="telephone" name="telephone" class="form-control" placeholder="98765432" required 
                                        value="<?= $form_data['telephone'] ?>">
                            </div>

                            <div class="col-md-6">
                                <label for="mot_de_passe" class="form-label">Mot de passe</label>
                                <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" required>
                                <div class="form-text">Choisissez un mot de passe sécurisé.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="mot_de_passe_conf" class="form-label">Confirmer mot de passe</label>
                                <input type="password" id="mot_de_passe_conf" name="mot_de_passe_conf" class="form-control" required>
                            </div>
                            
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-person-check-fill me-2"></i> Créer le Compte Parent</button>
                        </div>

                        <div class="text-center mt-3">
                            <a href="connexion_parent.php" class="text-decoration-none">Déjà inscrit ? Connectez-vous</a>
                        </div>
                    </form>
                </div>
            </section>

        </div>
    </section>
</main>

<footer id="footer" class="footer-16 footer position-relative dark-background">
    <div class="container">
        <div class="footer-main">
            <div class="row align-items-start">
                <div class="col-lg-5">
                    <div class="brand-section">
                        <a href="index.php" class="logo d-flex align-items-center mb-4">
                            <span class="sitename">ISMT SAINT SALOMON</span>
                        </a>
                        <p class="brand-description">L'Institut Supérieur des Métiers du Tertiaire (ISMT) Saint Salomon, votre partenaire pour l'excellence académique.</p>

                        <div class="contact-info mt-5">
                            <div class="contact-item">
                                <i class="bi bi-geo-alt"></i>
                                <span>**Godomey :** en face du marché de Godomey Hwlacomê</span>
                            </div>
                            <div class="contact-item">
                                <i class="bi bi-telephone"></i>
                                <span>**Infoline :** +229 0196004848</span>
                            </div>
                            <div class="contact-item">
                                <i class="bi bi-envelope"></i>
                                <span>ismtstsalomon55@gmail.com</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="footer-nav-wrapper">
                        <div class="row">
                            <div class="col-6 col-lg-3">
                                <div class="nav-column">
                                    <h6>Navigation</h6>
                                    <nav class="footer-nav">
                                        <a href="index.php">Accueil</a>
                                        <a href="about.php">À Propos</a>
                                        <a href="incrirechoix.php" >Inscriptions Étudiant</a>
                                        <a href="contact.php">Contact</a>
                                    </nav>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="nav-column">
                                    <h6>Formations</h6>
                                    <nav class="footer-nav">
                                        <a href="#">Informatique de Gestion</a>
                                        <a href="#">Commerce International</a>
                                        <a href="#">Sciences Juridiques</a>
                                        <a href="#">Réseaux</a>
                                    </nav>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="nav-column">
                                    <h6>Accès Rapide</h6>
                                    <nav class="footer-nav">
                                        <a href="connexion.php">Connexion Étudiant</a>
                                        <a href="connexion_parent.php">Connexion Parent</a>
                                    </nav>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="nav-column">
                                    <h6>Ressources</h6>
                                    <nav class="footer-nav">
                                        <a href="#">Politique de Confidentialité</a>
                                        <a href="#">Aide</a>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="container">
                <div class="bottom-content">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="copyright">
                                <p>© <span class="sitename">ISMT SAINT SALOMON</span>. Tous droits réservés.</p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="legal-links">
                                <div class="credits">
                                    Conçu et développé par <a href="https://bootstrapmade.com/" target="_blank">BootstrapMade</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
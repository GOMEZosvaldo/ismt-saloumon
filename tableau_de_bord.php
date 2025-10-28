<?php

// 3. Inclure la connexion à la base de données
require_once "db.php";

// 2. Vérification de la connexion
if (!isset($_SESSION["etudiant_id"])) {
    // Redirection vers la page de connexion si l'ID étudiant n'est pas dans la session
    header("Location: connexion.php");
    exit();
}


$etudiant_id = $_SESSION["etudiant_id"];
$etudiant_data = [];

try {
    // 4. Récupérer TOUTES les données de l'étudiant depuis la DB pour plus de sécurité
    // On récupère photo, nom, prénom, et matricule.
    $stmt = $conn->prepare("SELECT * FROM etudiants WHERE id = ?");
    $stmt->execute([$etudiant_id]);
    $etudiant_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$etudiant_data) {
        // Sécurité: si l'ID n'existe pas dans la DB, déconnecter l'utilisateur
        session_unset();
        session_destroy();
        header("Location: connexion.php");
        exit();
    }
} catch (PDOException $e) {
    // Gérer l'erreur de base de données (Déconnexion en cas de problème de DB)
    // En production, on pourrait logguer $e->getMessage()
    session_unset();
    session_destroy();
    header("Location: connexion.php");
    exit();
}

// 5. Initialiser les variables pour l'affichage (depuis les données de la DB)
$nom = $etudiant_data["nom"];
$prenom = $etudiant_data["prenom"];
$matricule = $etudiant_data["matricule"];
$filiere = $etudiant_data["filiere"];
$niveau = $etudiant_data["niveau"];

// Définir le chemin de la photo ou un chemin par défaut
$photo_path = !empty($etudiant_data["photo"]) ? "assets/img/person/" . $etudiant_data["photo"] : "assets/img/default-avatar.png";

// Variable pour le menu (utilisateur connecté)
$etudiant_connecte = true;
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Mon Suivi Étudiant - ISMT Saint Salomon</title>
    <meta name="description" content="Tableau de bord de suivi étudiant.">
    <meta name="keywords" content="étudiant, suivi, notes, paiement, profil">

    <link href="assets/img/favicon.png" rel="icon">
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
        .profile-img-lg {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 4px solid var(--color-primary); /* Utilise la couleur primaire du template */
            padding: 2px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .dashboard-card {
            min-height: 150px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
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
                    <li><a href="index.php">Acceuil</a></li>
                    <li class="dropdown text-capitalize"><a href="about.php"><span>à propos</span></a>
                    </li>

                    <li><a href="contact.php">Contact</a></li>

                    <?php if ($etudiant_connecte): ?>
                    <li><a href="tableau_de_bord.php" class="active">Mon Suivi</a></li>

                    <li class="dropdown">
                        <a href="#">
                            <img
                                src="<?= htmlspecialchars($photo_path) ?>"
                                alt="Photo de profil"
                                class="img-fluid rounded-circle me-1"
                                style="width: 30px; height: 30px; object-fit: cover; vertical-align: middle; border: 1px solid #eee;"
                            >
                            <span>Bonjour, <?= htmlspecialchars($prenom) ?></span>
                            <i class="bi bi-chevron-down toggle-dropdown"></i>
                        </a>
                        <ul>
                            <li><a href="tableau_de_bord.php">Mon Profil</a></li>
                            <li><a href="deconnexion.php">Déconnexion</a></li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <li><a href="incrirechoix.php" >Inscriptions</a></li>
                    <li><a href="connexion.php">Connexion</a></li>
                    <?php endif; ?>

                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

        </div>
    </header>

    <main class="main">

        <div class="page-title" data-aos="fade">
            <div class="container">
                <nav class="breadcrumbs">
                    <ol>
                        <li><a href="index.php">Accueil</a></li>
                        <li class="current">Mon Suivi Étudiant</li>
                    </ol>
                </nav>
                <h1>Tableau de Bord Étudiant</h1>
            </div>
        </div><section id="dashboard" class="dashboard section">
            <div class="container">

                <div class="row gy-4">

                    <div class="col-lg-4">

                        <div class="card p-4 text-center dashboard-card mb-4" data-aos="fade-up" data-aos-delay="100">
                            <div class="d-flex justify-content-center">
                                <img
                                    src="<?= htmlspecialchars($photo_path) ?>"
                                    alt="Photo de profil de <?= htmlspecialchars($prenom . " " . $nom) ?>"
                                    class="img-fluid rounded-circle profile-img-lg mb-3"
                                >
                            </div>
                            <h4 class="card-title text-primary"><?= htmlspecialchars($prenom . " " . $nom) ?></h4>
                            <p class="text-muted mb-0">Matricule : <strong class="text-secondary"><?= htmlspecialchars($matricule) ?></strong></p>
                            <hr>
                            <p class="text-muted mb-0">Filiere : <strong class="text-secondary"><?= htmlspecialchars($filiere) ?></strong></p>
                            <p class="text-muted mb-0">Niveau : <strong class="text-secondary"><?= htmlspecialchars($niveau) ?></strong></p>
                            
                        </div><div class="d-grid gap-4">
                            <a href="#" class="btn btn-primary btn-lg d-flex align-items-center justify-content-center dashboard-card shadow-sm" data-aos="fade-up" data-aos-delay="200">
                                <i class="bi bi-file-earmark-text me-2 fs-4"></i>
                                <span>Voir mon Bulletin de Notes</span>
                            </a>
                            <a href="paiement.php" class="btn btn-success btn-lg d-flex align-items-center justify-content-center dashboard-card shadow-sm" data-aos="fade-up" data-aos-delay="300">
                                <i class="bi bi-credit-card me-2 fs-4"></i>
                                <span>Effectuer un Paiement en Ligne</span>
                            </a>
                        </div>
                    </div><div class="col-lg-8">
                        <div class="row gy-4">

                            <div class="col-md-6" data-aos="fade-up" data-aos-delay="400">
                                <div class="card p-4 dashboard-card bg-light">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-graph-up text-info fs-1 me-3"></i>
                                        <div>
                                            <h5 class="card-title mb-0">Moyenne Générale</h5>
                                            <p class="display-6 fw-bold text-info">-- / 20</p>
                                            <span class="text-muted small">Moyenne du dernier semestre</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6" data-aos="fade-up" data-aos-delay="500">
                                <div class="card p-4 dashboard-card bg-light">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-wallet-fill text-warning fs-1 me-3"></i>
                                        <div>
                                            <h5 class="card-title mb-0">Statut des Frais</h5>
                                            <p class="display-6 fw-bold text-warning">... XOF</p>
                                            <span class="text-muted small">Solde restant à payer</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12" data-aos="fade-up" data-aos-delay="600">
                                <div class="card p-4 dashboard-card">
                                    <h5 class="card-title text-primary"><i class="bi bi-list-check me-1"></i> Dernières Notes Publiées</h5>
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Matière</th>
                                                <th>Note</th>
                                                <th>Coefficient</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Programmation Web</td>
                                                <td>16.50 / 20</td>
                                                <td>3</td>
                                                <td>15 Oct. 2025</td>
                                            </tr>
                                            <tr>
                                                <td>Bases de Données</td>
                                                <td>14.00 / 20</td>
                                                <td>2</td>
                                                <td>14 Oct. 2025</td>
                                            </tr>
                                            <tr>
                                                <td>Anglais Technique</td>
                                                <td>18.00 / 20</td>
                                                <td>1</td>
                                                <td>10 Oct. 2025</td>
                                            </tr>
                                            </tbody>
                                    </table>
                                    <div class="text-end mt-3">
                                        <a href="#" class="btn btn-sm btn-outline-primary">Voir tout l'historique <i class="bi bi-arrow-right"></i></a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div></div>

            </div>
        </section></main>
    <footer id="footer" class="footer-16 footer position-relative dark-background">
        <div class="container">

            <div class="footer-main">
                <div class="row align-items-start">

                    <div class="col-lg-5">
                        <div class="brand-section">
                            <a href="index.php" class="logo d-flex align-items-center mb-4">
                                <span class="sitename">MySchool</span>
                            </a>
                            <p class="brand-description">Crafting exceptional digital experiences through thoughtful design and
                                innovative solutions that elevate your brand presence.</p>

                            <div class="contact-info mt-5">
                                <div class="contact-item">
                                    <i class="bi bi-geo-alt"></i>
                                    <span>123 Creative Boulevard, Design District, NY 10012</span>
                                </div>
                                <div class="contact-item">
                                    <i class="bi bi-telephone"></i>
                                    <span>+1 (555) 987-6543</span>
                                </div>
                                <div class="contact-item">
                                    <i class="bi bi-envelope"></i>
                                    <span>hello@designstudio.com</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="footer-nav-wrapper">
                            <div class="row">

                                <div class="col-6 col-lg-3">
                                    <div class="nav-column">
                                        <h6>Studio</h6>
                                        <nav class="footer-nav">
                                            <a href="#">Our Story</a>
                                            <a href="#">Design Process</a>
                                            <a href="#">Portfolio</a>
                                            <a href="#">Case Studies</a>
                                            <a href="#">Awards</a>
                                        </nav>
                                    </div>
                                </div>

                                <div class="col-6 col-lg-3">
                                    <div class="nav-column">
                                        <h6>Services</h6>
                                        <nav class="footer-nav">
                                            <a href="#">Brand Identity</a>
                                            <a href="#">Web Design</a>
                                            <a href="#">Mobile Apps</a>
                                            <a href="#">Digital Strategy</a>
                                            <a href="#">Consultation</a>
                                        </nav>
                                    </div>
                                </div>

                                <div class="col-6 col-lg-3">
                                    <div class="nav-column">
                                        <h6>Resources</h6>
                                        <nav class="footer-nav">
                                            <a href="#">Design Blog</a>
                                            <a href="#">Style Guide</a>
                                            <a href="#">Free Assets</a>
                                            <a href="#">Tutorials</a>
                                            <a href="#">Inspiration</a>
                                        </nav>
                                    </div>
                                </div>

                                <div class="col-6 col-lg-3">
                                    <div class="nav-column">
                                        <h6>Connect</h6>
                                        <nav class="footer-nav">
                                            <a href="#">Start Project</a>
                                            <a href="#">Schedule Call</a>
                                            <a href="#">Join Newsletter</a>
                                            <a href="#">Follow Updates</a>
                                            <a href="#">Partnership</a>
                                        </nav>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="footer-social">
                <div class="row align-items-center">

                    <div class="col-lg-6">
                        <div class="newsletter-section">
                            <h5>Stay Inspired</h5>
                            <p>Subscribe to receive design insights and creative inspiration delivered monthly.</p>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="social-section">
                            <div class="social-links">
                                <a href="#" aria-label="Dribbble" class="social-link">
                                    <i class="bi bi-dribbble"></i>
                                    <span>Dribbble</span>
                                </a>
                                <a href="#" aria-label="Behance" class="social-link">
                                    <i class="bi bi-behance"></i>
                                    <span>Behance</span>
                                </a>
                                <a href="#" aria-label="Instagram" class="social-link">
                                    <i class="bi bi-instagram"></i>
                                    <span>Instagram</span>
                                </a>
                                <a href="#" aria-label="LinkedIn" class="social-link">
                                    <i class="bi bi-linkedin"></i>
                                    <span>LinkedIn</span>
                                </a>
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
                                <p>© <span class="sitename">MyWebsite</span>. All rights reserved.</p>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="legal-links">
                                <a href="#">Privacy Policy</a>
                                <a href="#">Terms of Service</a>
                                <a href="#">Cookie Policy</a>
                                <div class="credits">
                                    Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
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
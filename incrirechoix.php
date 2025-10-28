<?php
// Démarrer la session au tout début
session_start();
// Pas besoin de la connexion à la base de données ici, c'est une simple page de redirection.
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Choisir l'Inscription - ISMT SAINT SALOMON</title>
    <meta name="description" content="Choisissez votre type d'inscription : Étudiant ou Parent.">

    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
    
    <style>
      /* Styles pour rendre la carte interactive et agréable */
      .choice-card {
          transition: transform 0.3s ease, box-shadow 0.3s ease;
          border: none;
          cursor: pointer;
          min-height: 250px; /* Assure une taille minimale */
      }
      .choice-card:hover {
          transform: translateY(-5px);
          box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
      }
      .choice-card i {
          font-size: 3.5rem;
          margin-bottom: 15px;
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
                 <li><a href="incrirechoix.php" class="active">Inscriptions</a></li> 
                <li><a href="connexion.php">Connexion</a></li> 
                </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>
    </div>
</header>
<main class="main">
    <section id="choice" class="contact section">
        <div class="container py-5">
            <div class="section-header">
                <h2>Choisissez votre profil d'inscription</h2>
                <p>Veuillez sélectionner si vous vous inscrivez en tant qu'**Étudiant** ou en tant que **Parent**.</p>
            </div>

            <div class="row justify-content-center pt-4">
                
                <div class="col-md-6 col-lg-5 mb-4">
                    <a href="inscrire.php" class="text-decoration-none d-block">
                        <div class="card choice-card text-center h-100 shadow-sm">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <i class="bi bi-person-workspace text-primary"></i>
                                <h3 class="card-title text-primary mt-2">Inscription Étudiant</h3>
                                <p class="card-text text-muted">Je suis un nouvel étudiant ou j'inscris un enfant sous ma tutelle.</p>
                                <span class="btn btn-primary mt-3"><i class="bi bi-arrow-right-circle me-2"></i> Continuer</span>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-6 col-lg-5 mb-4">
                    <a href="incrireparent.php" class="text-decoration-none d-block">
                        <div class="card choice-card text-center h-100 shadow-sm">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <i class="bi bi-people-fill text-success"></i>
                                <h3 class="card-title text-success mt-2">Inscription Parent</h3>
                                <p class="card-text text-muted">Je suis un parent et je souhaite avoir un compte pour suivre mon enfant.</p>
                                <span class="btn btn-success mt-3"><i class="bi bi-arrow-right-circle me-2"></i> Continuer</span>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
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
                                    <a href="incrirechoix.php" >Inscriptions</a>
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
<script src="assets/js/main.js"></script>

</body>
</html>
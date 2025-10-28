<?php
// 1. Démarrer la session doit être la TOUTE PREMIÈRE instruction PHP
// Inclure la connexion à la base de données
// Assurez-vous que le chemin "db.php" est correct
require_once "db.php"; 

// 2. Initialiser les variables de données de l'étudiant
$etudiant_connecte = false;
$etudiant_data = [];

// 3. Si un étudiant est connecté, récupérer ses données
if (isset($_SESSION["etudiant_id"])) {
    $etudiant_connecte = true;
    
    $etudiant_id = $_SESSION["etudiant_id"];
    
    try {
        // CORRECTION MAJEURE : Récupérer les champs nécessaires : prenom, nom, photo, et matricule
        $stmt = $conn->prepare("SELECT prenom, nom, photo, matricule FROM etudiants WHERE id = ?");
        $stmt->execute([$etudiant_id]);
        $etudiant_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$etudiant_data) {
            // Si l'utilisateur n'est plus dans la DB, déconnecter
            session_unset();
            session_destroy();
            $etudiant_connecte = false;
        }
    } catch (PDOException $e) {
        // Gestion des erreurs de base de données
        session_unset();
        session_destroy();
        $etudiant_connecte = false;
        // Optionnel : enregistrer l'erreur : error_log("DB Error: " . $e->getMessage());
    }
}
// Note : Puisque c'est la page d'accueil, on n'ajoute PAS de header("location: connexion.php");
// ... (Bloc PHP existant jusqu'à la fin du try-catch)

// 4. Définir les variables pour le menu (après la vérification de connexion)
if ($etudiant_connecte) {
    $prenom = htmlspecialchars($etudiant_data["prenom"] ?? 'Étudiant');
    
    // Définir le chemin de la photo, en ajoutant le dossier 'assets/img/person/'
    // (J'ai fait l'hypothèse que les photos sont stockées dans ce dossier)
    if (!empty($etudiant_data["photo"])) {
        $photo_path = 'assets/img/person/' . htmlspecialchars($etudiant_data["photo"]);
    } else {
        $photo_path = 'assets/img/default-avatar.png'; // Chemin par défaut si aucune photo
    }
} else {
    // Si non connecté, initialiser pour éviter les erreurs
    $prenom = '';
    $photo_path = ''; 
}

// Note: session_start() doit être la toute première ligne du fichier!
// ... (le reste de votre code PHP)
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Index - MySchool Bootstrap Template</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="assets/img/LOGO-removebg-preview.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: MySchool
  * Template URL: https://bootstrapmade.com/myschool-bootstrap-school-template/
  * Updated: Jul 28 2025 with Bootstrap v5.3.7
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body class="index-page">

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

    <!-- Page Title -->
    <div class="page-title">
      <div class="heading">
        <div class="container">
          <div class="row d-flex justify-content-center text-center">
            <div class="col-lg-8">
              <h1 class="heading-title">Contactez-nous</h1>
              <p class="mb-0">
                Vous souhaitez obtenir des informations sur nos formations, nos inscriptions ou nos services ? L’équipe de l’ISMT ST SALOMON est à votre disposition pour vous accompagner et répondre à toutes vos questions.
              </p>
            </div>
          </div>
        </div>
      </div>
      <nav class="breadcrumbs">
        <div class="container">
          <ol>
            <li><a href="index.php">Acceuil</a></li>
            <li class="current">Contact</li>
          </ol>
        </div>
      </nav>
    </div><!-- End Page Title -->

    <!-- Contact Section -->
    <section id="contact" class="contact section">

      <div class="container">

        <div class="row gy-4 mb-5">
          <div class="col-lg-4">
            <div class="info-card">
              <div class="icon-box">
                <i class="bi bi-geo-alt"></i>
              </div>
              <h3>Nos adresse</h3>
              <p><b>Godomey :</b>en face du marché de godomey hwlacomê</p>
              <p><b>Hévier </b></p>
              <p><b>Akpakpa </b></p>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="info-card">
              <div class="icon-box">
                <i class="bi bi-telephone"></i>
              </div>
              <h3>Infoline</h3>
              <p>Téléphone: +229 0196004848<br>
                Email: ismtstsalomon55@gmail.com</p>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="info-card">
              <div class="icon-box">
                <i class="bi bi-clock"></i>
              </div>
              <h3>Heure d'ouverture</h3>
              <p>Lundi - Vendredi: 8:00 - 18:00<br>
                Samedi & Dimanche: Fermé</p>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-12">
            <div class="form-wrapper">
              <form action="forms/contact.php" method="post" role="form" class="php-email-form">
                <div class="row">
                  <div class="col-md-6 form-group">
                    <div class="input-group">
                      <span class="input-group-text"><i class="bi bi-person"></i></span>
                      <input type="text" name="name" class="form-control" placeholder="Votre nom*" required="">
                    </div>
                  </div>
                  <div class="col-md-6 form-group">
                    <div class="input-group">
                      <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                      <input type="email" class="form-control" name="email" placeholder="Email / Téléphone*" required="">
                    </div>
                  </div>
                </div>
                <div class="row mt-3">
                  <div class="form-group mt-3">
                    <div class="input-group">
                      <span class="input-group-text"><i class="bi bi-chat-dots"></i></span>
                      <textarea class="form-control" name="message" rows="6" placeholder="Ecrire un message*" required=""></textarea>
                    </div>
                  </div>
                  <div class="my-3">
                    <div class="loading">Chargement</div>
                    <div class="error-message"></div>
                    <div class="sent-message">Merci, message envoyé</div>
                  </div>
                  <div class="text-center">
                    <button type="submit">Envoyer un message</button>
                  </div>

                </div>
              </form>
            </div>
          </div>

        </div>

      </div>
    </section><!-- /Contact Section -->

  </main>

  <?php require_once "footer.php"; ?>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>
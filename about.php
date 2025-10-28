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
// 4. Définir les variables pour le menu (après la vérification de connexion)
if ($etudiant_connecte) {
    $prenom = htmlspecialchars($etudiant_data["prenom"] ?? 'Étudiant');
    
    // Définir le chemin de la photo
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

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>À Propos - ISMT ST SALOMON</title>
    <meta name="description" content="Découvrez l'histoire, la mission et les valeurs de l'ISMT ST SALOMON, une université privée bilingue au Bénin.">
    <meta name="keywords" content="ISMT, Saint Salomon, Université Bénin, Éducation Supérieure, Mission, Valeurs, Leadership">

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
        :root {
            --color-primary: #007bff; /* Garder pour le reste du site */
            --color-red-play: #dc3545; /* Nouveau : Couleur Rouge pour le bouton Play */
        }
        /* --- Styles pour les Sections Vidéo Dynamiques --- */

        .video-container {
            position: relative;
            cursor: pointer;
            background-size: cover; /* Assure que l'image couvre le conteneur */
            background-position: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        /* Effet de zoom léger au survol */
        .video-container:hover {
            transform: scale(1.01);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2) !important;
        }

        /* Overlay pour le bouton de lecture */
        .video-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4); /* Ombre légère */
            opacity: 1;
            transition: opacity 0.3s ease;
            z-index: 2;
        }

        .video-container:hover .video-overlay {
            background: rgba(0, 0, 0, 0.2); /* Ombre plus légère au survol */
        }

        .video-container.playing .video-overlay,
        .video-container.playing .play-button {
            display: none; /* Cache l'overlay et le bouton quand la lecture commence */
        }

        /* Style du bouton de lecture central */
        .play-button {
            /* COULEUR CHANGÉE EN ROUGE */
            background-color: var(--color-red-play); 
            color: white;
            border: none;
            border-radius: 50%;
            width: 80px; 
            height: 80px; 
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 2.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 0 0 10px rgba(255, 255, 255, 0.3);
            z-index: 3;
            cursor: pointer;
        }

        /* Animation et style au survol du bouton */
        .play-button:hover {
            /* Couleur plus foncée au survol */
            background-color: #c82333; /* Rouge foncé au survol */
            transform: scale(1.1);
            box-shadow: 0 0 0 15px rgba(255, 255, 255, 0.4);
        }

        /* Assure que la balise <video> s'adapte à la structure lorsqu'elle est injectée */
        .video-container video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
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
                    <li class="dropdown text-capitalize"><a href="about.php" class="active"><span>à propos</span></a>
                    </li>

                    <li><a href="contact.php">Contact</a></li>

                    <?php if ($etudiant_connecte): ?>
                    <li><a href="tableau_de_bord.php">Mon Suivi</a></li>

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

        <div class="page-title">
            <div class="heading">
                <div class="container">
                    <div class="row d-flex justify-content-center text-center">
                        <div class="col-lg-8">
                            <h1 class="heading-title">À propos de l’ISMT ST SALOMON</h1>
                            <p class="mb-0">
                                L’ISMT ST SALOMON est une université privée bilingue accréditée par le Ministère de l’Enseignement Supérieur du Bénin. Située à Cotonou, elle se distingue par l’excellence de ses formations, son innovation technologique et son engagement pour la réussite des étudiants.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <nav class="breadcrumbs">
                <div class="container">
                    <ol>
                        <li><a href="index.php">Acceuil</a></li>
                        <li class="current">A propos</li>
                    </ol>
                </div>
            </nav>
        </div><section id="history" class="history section">

            <div class="container">

                <div class="hero-content text-center mb-5">
                    <div class="row justify-content-center">
                        <div class="col-lg-12">
                            <span class="section-badge">Excellence en Éducation</span>
                            <h1 class="hero-title">Apprentissage progressif pour l'innovation.</h1>
                            <p class="hero-description">
                            L’ISMT ST SALOMON est une université privée bilingue accréditée par le Ministère de l’Enseignement Supérieur du Bénin. Elle offre un environnement académique moderne où l’innovation pédagogique, la recherche et la technologie se combinent pour préparer les étudiants à relever les défis du monde contemporain.</p>
                        </div>
                    </div>
                </div>

                <div class="values-section mb-5">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="values-header text-center mb-4">
                                <span class="section-badge">Notre Fondation</span>
                                <h2 class="section-heading">Valeurs & Principes</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accomplishments-section">
                    <div class="row align-items-center">
                        <div class="col-lg-5">
                            <div class="campus-visual">
                                <img src="assets/img/education/etab.jpg" alt="Campus Facilities" class="main-image img-fluid">
                                <div class="floating-stats">
                                    <div class="stat-card">
                                        <span class="stat-number">10+</span>
                                        <span class="stat-label">Année</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-7">
                            <div class="accomplishments-content">
                                <span class="section-badge">Impact et Réussite</span>
                                <h2 class="section-heading">Excellence Mesurable en Éducation</h2>
                                <p class="section-text">L’ISMT ST SALOMON est reconnue pour ses standards élevés, son innovation pédagogique et sa capacité à former des leaders compétents et responsables.</p>

                                <div class="achievements-grid">
                                    <div class="achievement-item">
                                        <span class="achievement-number">100+</span>
                                        <span class="achievement-desc">Diplômés formés avec succès </span>
                                    </div>
                                    <div class="achievement-item">
                                        <span class="achievement-number">98%</span>
                                        <span class="achievement-desc">De taux d’employabilité</span>
                                    </div>
                                    <div class="achievement-item">
                                        <span class="achievement-number">30+</span>
                                        <span class="achievement-desc">Enseignants experts</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </section><hr>
        
        <section class="py-5 bg-white" id="temoignage-unique">
            <div class="container py-4">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6">
                        <div class="ratio ratio-16x9 shadow-lg rounded-4 overflow-hidden video-container" data-video-src="assets/img/education/videoetu.mp4" data-poster-src="assets/img/education/temoin.png">
                            <div class="video-overlay d-flex justify-content-center align-items-center">
                                <button class="play-button" aria-label="Lancer la vidéo de témoignages">
                                    <i class="bi bi-play-fill"></i>
                                </button>
                            </div>
                        </div>
                        </div>

                    <div class="col-lg-6">
                        <span class="badge rounded-pill bg-primary text-white mb-3 fw-bold">Parole d'Étudiant(e)</span>
                        
                        <h2 class="display-5 fw-bold text-dark mb-4">
                            Vivons l'Expérience : <span class="text-primary">Ce Qu'ils en Disent.</span>
                        </h2>
                        
                        <p class="lead text-muted mb-4">
                            Regardez notre compilation exclusive de témoignages flash. En moins de 2 minutes, découvrez les moments forts, les défis surmontés et les raisons qui font de notre campus un lieu d'excellence et de passion.
                        </p>

                        <a href="connexion.php" class="btn btn-primary btn-lg me-3 shadow-sm fw-bold">
                            <i class="bi bi-person-fill-add me-2"></i> Rejoindre la Communauté
                        </a>
                        
                        <a href="contact.php" class="btn btn-outline-secondary btn-lg">
                            Contactez-nous
                        </a>
                    </div>
                </div>
            </div>
        </section>
        <hr>

        <section class="py-5 bg-dark text-white" id="visite-drone">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="display-3 fw-bolder text-warning">Visite ISMT Godomey en Vidéo</h2>
                    <p class="lead text-light-50">Immersion totale : découvrez nos infrastructures comme jamais auparavant en Hyperlapse 4K.</p>
                </div>

                <div class="ratio ratio-16x9 shadow-lg rounded-4 overflow-hidden video-container mb-4" data-video-src="assets/img/education/presen.mp4" data-poster-src="assets/img/education/etab.jpg">
                    <div class="video-overlay d-flex justify-content-center align-items-center">
                        <button class="play-button" aria-label="Lancer la vidéo de visite du campus">
                            <i class="bi bi-play-fill"></i>
                        </button>
                    </div>
                </div>
                <div class="row text-center mt-5">
                    <div class="col-md-4 mb-3">
                        <p class="fs-1 fw-bold text-success">15</p>
                        <p class="text-light-50">Bâtiments Modernes</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <p class="fs-1 fw-bold text-success">4K</p>
                        <p class="text-light-50">Qualité Cinématique</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <p class="fs-1 fw-bold text-success">60s</p>
                        <p class="text-light-50">Le Tour du Campus</p>
                    </div>
                </div>
            </div>
        </section>
        <hr>

        <section id="leadership" class="leadership section">

            <div class="container">

                <div class="intro-section">
                    <div class="content-wrapper">
                        <span class="intro-label">Excellence en Leadership</span>
                        <h2 class="intro-title">Notre Personnel</h2>
                        <p class="intro-description">Des leaders visionnaires pour façonner l’éducation de demain <br>
                            À l’ISMT ST SALOMON, notre équipe dirigeante combine expérience, vision et engagement pour garantir l’excellence académique et le développement global de nos étudiants. Chaque membre contribue à bâtir un environnement d’apprentissage stimulant et innovant.</p>
                    </div>
                </div>

                <div class="leadership-grid">
                    <div class="featured-leader">
                        <div class="leader-image-large">
                            <img src="assets/img/education/dg.jpg" alt="Principal" class="img-fluid">
                        </div>
                        <div class="leader-details">
                            <h3>M. Comlan Rémi LOKO</h3>
                            <span class="leader-title">Directeur General</span>
                            <div class="social-connect">
                                <a href="#" class="social-link"><i class="bi bi-linkedin"></i></a>
                                <a href="#" class="social-link"><i class="bi bi-envelope"></i></a>
                                <a href="#" class="social-link"><i class="bi bi-globe"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="leadership-team-grid">
                        <div class="team-member">
                            <div class="member-photo">
                                <img src="assets/img/education/eco.jpg" alt="Vice Principal" class="img-fluid">
                                <div class="member-overlay">
                                    <div class="member-social">
                                        <a href="#"><i class="bi bi-linkedin"></i></a>
                                        <a href="#"><i class="bi bi-envelope"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="member-info">
                                <h4>Mme LOKO</h4>
                                <span class="member-role">Econome</span>
                                <p class="member-description"></p>
                            </div>
                        </div>

                        <div class="team-member">
                            <div class="member-photo">
                                <img src="assets/img/education/de.jpg" alt="Academic Coordinator" class="img-fluid">
                                <div class="member-overlay">
                                    <div class="member-social">
                                        <a href="#"><i class="bi bi-linkedin"></i></a>
                                        <a href="#"><i class="bi bi-envelope"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="member-info">
                                <h4>M.HOUEZO Théophile</h4>
                                <span class="member-role">Directeur des Etudes</span>
                                <p class="member-description"></p>
                            </div>
                        </div>

                        <div class="team-member">
                            <div class="member-photo">
                                <img src="assets/img/education/pre.jpg" alt="Student Affairs" class="img-fluid">
                                <div class="member-overlay">
                                    <div class="member-social">
                                        <a href="#"><i class="bi bi-linkedin"></i></a>
                                        <a href="#"><i class="bi bi-envelope"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="member-info">
                                <h4>M.Ange ADEOTI</h4>
                                <span class="member-role">Prefect de Discipline</span>
                            </div>
                        </div>

                        <div class="team-member">
                            <div class="member-photo">
                                <img src="assets/img/education/aseco.jpg" alt="Curriculum Head" class="img-fluid">
                                <div class="member-overlay">
                                    <div class="member-social">
                                        <a href="#"><i class="bi bi-linkedin"></i></a>
                                        <a href="#"><i class="bi bi-envelope"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="member-info">
                                <h4>M.Noëllie</h4>
                                <span class="member-role">Assitante Econome</span>
                            </div>
                        </div>

                        <div class="team-member">
                            <div class="member-photo">
                                <img src="assets/img/education/secre.jpg" alt="Operations Manager" class="img-fluid">
                                <div class="member-overlay">
                                    <div class="member-social">
                                        <a href="#"><i class="bi bi-linkedin"></i></a>
                                        <a href="#"><i class="bi bi-envelope"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="member-info">
                                <h4>Mme SODJI Perle</h4>
                                <span class="member-role">Secretaire</span>
                            </div>
                        </div>

                        <div class="team-member">
                            <div class="member-photo">
                                <img src="assets/img/education/sec.jpg" alt="Admissions Director" class="img-fluid">
                                <div class="member-overlay">
                                    <div class="member-social">
                                        <a href="#"><i class="bi bi-linkedin"></i></a>
                                        <a href="#"><i class="bi bi-envelope"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="member-info">
                                <h4>Ferdinand TCHICO</h4>
                                <span class="member-role">Agent de sécurité</span>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="leadership-philosophy">
                    <div class="philosophy-content">
                        <h3>Mission Leadership</h3>
                        <p>Former des cadres compétents et responsables, capables de contribuer au développement durable et au progrès de la société, à travers des programmes de qualité et un encadrement personnalisé</p>
                        <div class="philosophy-points">
                            <div class="point">
                                <i class="bi bi-lightbulb"></i>
                                <span>Pratique et créativité encouragées.</span>
                            </div>
                            <div class="point">
                                <i class="bi bi-people"></i>
                                <span>Éthique et respect au cœur de l'action.</span>
                            </div>
                            <div class="point">
                                <i class="bi bi-graph-up"></i>
                                <span>Standards élevés, succès garanti.</span>
                            </div>
                        </div>
                    </div>
                </div> 
                </div>

        </section></main>

    <?php require_once "footer.php"; ?>

    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <div id="preloader"></div>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>

    <script src="assets/js/main.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const videoContainers = document.querySelectorAll('.video-container');

            // 1. Initialisation des images d'affiche (Poster)
            videoContainers.forEach(container => {
                const posterSrc = container.getAttribute('data-poster-src');
                // L'image d'affiche est définie comme image de fond du conteneur
                container.style.backgroundImage = `url(${posterSrc})`; 
            });


            // Fonction pour arrêter toutes les vidéos en cours de lecture
            function stopAllVideos() {
                videoContainers.forEach(container => {
                    const videoElement = container.querySelector('video');
                    
                    if (videoElement) {
                        videoElement.pause();
                        
                        // Retirer l'élément vidéo du conteneur
                        container.removeChild(videoElement);
                        
                        // Retirer la classe 'playing' pour réafficher l'overlay (bouton Play)
                        // et l'image de fond (le poster) qui n'est plus masquée par la balise <video>
                        container.classList.remove('playing');
                        
                        // Réactiver le gestionnaire de clic pour permettre de relancer la vidéo
                        container.addEventListener('click', launchVideo); 
                    }
                });
            }
            
            // Fonction de lancement unique pour chaque conteneur
            function launchVideo(event) {
                const container = event.currentTarget; // Le conteneur cliqué
                
                if (!container.classList.contains('playing')) {
                    
                    // 1. Arrêter toutes les autres vidéos avant de lancer celle-ci
                    stopAllVideos(); 
                    
                    const videoSrc = container.getAttribute('data-video-src');
                    
                    // Créer l'élément vidéo
                    const videoElement = document.createElement('video');
                    
                    // L'attribut 'controls' permet bien de faire PAUSE/PLAY/STOP avec la barre native
                    videoElement.setAttribute('controls', ''); 
                    
                    videoElement.setAttribute('autoplay', '');
                    videoElement.setAttribute('playsinline', '');
                    
                    // Styles pour que la vidéo prenne toute la place
                    videoElement.style.position = 'absolute';
                    videoElement.style.top = '0';
                    videoElement.style.left = '0';
                    videoElement.style.width = '100%';
                    videoElement.style.height = '100%';
                    videoElement.style.objectFit = 'cover';

                    videoElement.innerHTML = `<source src="${videoSrc}" type="video/mp4">Votre navigateur ne supporte pas la balise vidéo.`;
                    
                    // Ajouter la classe pour masquer l'overlay
                    container.classList.add('playing');
                    
                    // Ajouter la vidéo au conteneur
                    container.appendChild(videoElement);

                    // Supprimer le gestionnaire de clic pour éviter de relancer une vidéo déjà lancée
                    container.removeEventListener('click', launchVideo);
                    
                    // Lancer la lecture (avec gestion des blocages d'autoplay)
                    videoElement.play().catch(error => {
                        console.log("Autoplay bloqué. Veuillez cliquer sur le bouton Play de la barre de contrôle vidéo.");
                    });
                    
                    // Événement pour nettoyer le DOM lorsque la vidéo se termine (lecture complète)
                    videoElement.addEventListener('ended', function() {
                        // Utiliser la fonction d'arrêt général pour tout remettre à zéro
                        stopAllVideos(); 
                    });
                }
            }
            
            // 2. Attacher le gestionnaire de clic à chaque conteneur
            videoContainers.forEach(container => {
                container.addEventListener('click', launchVideo);
            });
        });
    </script>

</body>

</html>
<?php
// =======================================================================
// 0. VÉRIFICATION DE SESSION ET CONFIGURATION DE BASE
// =======================================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// SIMULATION DE VÉRIFICATION DE SESSION
// Remplacez par votre logique de session réelle en production
$parent_email = $_SESSION["parent_email"] ?? 'parent.connecte@ismt.com'; 
$parent_id = $_SESSION["parent_id"] ?? 'P001'; 


// =======================================================================
// 1. CONFIGURATION KKIAYAPAY
// =======================================================================
$KKIAPAY_PUBLIC_KEY = "95479fa0ab5e11f088425701ad7ad9d3"; // Clé SANDBOX
$CALLBACK_URL       = "http://localhost/ismt/callback.php"; // URL de rappel

$show_widget = false;
$error_message = '';
$montant_precedent = $_POST['montant'] ?? '1000';


// =======================================================================
// 2. TRAITEMENT DU FORMULAIRE
// =======================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email_parent'] ?? $parent_email, FILTER_VALIDATE_EMAIL); 
    $montant = max(100, floatval($_POST['montant'])); 
    $montant_precedent = $montant;
    
    if ($email && $montant >= 100) {
        $data_transaction = json_encode(['parent_id' => $parent_id, 'email' => $email, 'montant_initial' => $montant]);
        $show_widget = true;
    } else {
        $error_message = "Veuillez entrer une adresse e-mail valide et un montant supérieur ou égal à 100 XOF.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement Scolarité - ISMT Saint Salomon</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.kkiapay.me/k.js"></script>

    <style>
        /* Couleurs du thème */
        :root {
            --ismt-primary: #007bff; 
            --ismt-success: #28a745; 
            --ismt-final-btn: #4661b9; /* Votre couleur bleue demandée */
        }
        
        .payment-card {
            max-width: 550px;
            margin-top: 50px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: var(--ismt-primary);
            color: white;
            padding: 25px;
            border-radius: 15px 15px 0 0;
            border-bottom: none;
        }
        .input-group-text {
            background-color: var(--ismt-primary);
            color: white;
            border-color: var(--ismt-primary);
        }
        .btn-submit { /* Bouton de validation du formulaire */
            background-color: var(--ismt-success);
            border-color: var(--ismt-success);
            font-weight: bold;
        }
        
        /* Style du bouton "Payer Maintenant" (ouvre la modale) - Style demandé */
        .btn-confirm-modal { 
            background-color: var(--ismt-final-btn);
            color: white; 
            padding: 10px 20px;
            font-size: 1.25rem; /* Equivalent à btn-lg, ajusté pour la clarté */
            border: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .btn-confirm-modal:hover {
            background-color: #3b5097; /* Bleu légèrement plus foncé au survol */
            border-color: #3b5097;
        }
        /* Style du bouton FINAL dans la modale (pour lancer Kkiapay) */
        .btn-kkiapay-launch {
            background-color: var(--ismt-final-btn); 
            border-color: var(--ismt-final-btn);
            font-weight: bold;
        }
        .btn-kkiapay-launch:hover {
            background-color: #3b5097; 
            border-color: #3b5097;
        }
        .alert-info-custom {
            background-color: #e3f2fd;
            border-color: #bbdefb;
            color: #1976d2;
            font-weight: 500;
        }
        .modal-header .btn-close-white {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
    </style>
</head>
<body class="bg-light">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card payment-card mx-auto">
                    
                    <div class="card-header text-center">
                        <h3 class="mb-1"><i class="bi bi-shield-lock-fill me-2"></i> Paiement Scolarité</h3>
                        <p class="small opacity-75 mb-0">ISMT Saint Salomon - Plateforme Kkiapay Sandbox</p>
                    </div>
                    
                    <div class="card-body p-5">

                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger text-center" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i> **Erreur :** <?= $error_message ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="alert alert-info-custom text-center" role="alert">
                            <i class="bi bi-person-circle me-2"></i> **Compte Parent :** <?= htmlspecialchars($parent_email) ?>
                        </div>
                        
                        <form method="POST" action="paiement.php">
                            <div class="mb-4">
                                <label for="montant" class="form-label fw-bold">Montant à Payer :</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text"><i class="bi bi-cash-stack"></i></span>
                                    <input type="number" step="100" name="montant" id="montant" class="form-control" 
                                            placeholder="Ex: 50000" required min="100" 
                                            value="<?= htmlspecialchars($montant_precedent) ?>">
                                    <span class="input-group-text">XOF</span>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="email_parent" class="form-label fw-bold">Email de Contact :</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                    <input type="email" name="email_parent" id="email_parent" class="form-control" 
                                            placeholder="parent@exemple.com" required 
                                            value="<?= htmlspecialchars($parent_email) ?>">
                                </div>
                            </div>
                            
                            <?php if (!$show_widget): ?>
                                <div class="d-grid gap-2 mt-4">
                                    <button type="submit" class="btn btn-submit btn-lg shadow">
                                        <i class="bi bi-check-circle-fill me-2"></i> Valider et Continuer
                                    </button>
                                </div>
                            <?php endif; ?>
                        </form>
                        
                        <?php if ($show_widget): ?>
                            <div class="d-grid gap-2 mt-4">
                                <p class="text-center text-success fw-bold">Montant à régler : **<?= number_format($montant, 0, ',', ' ') ?> XOF**</p>
                                
                                <button type="button" class="btn btn-confirm-modal shadow" 
                                        data-bs-toggle="modal" data-bs-target="#confirmPaymentModal">
                                    <i class="bi bi-credit-card-2-front-fill me-2"></i> **Payer Maintenant**
                                </button>
                            </div>
                        <?php endif; ?>

                    </div>
                    
                    <div class="card-footer text-center text-muted small py-3">
                        Paiement sécurisé par Kkiapay
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="confirmPaymentModal" tabindex="-1" aria-labelledby="confirmPaymentModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="confirmPaymentModalLabel"><i class="bi bi-wallet-fill me-2"></i> Confirmation de Paiement</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Veuillez confirmer les détails de votre transaction :</p>
            <h4 class="text-center text-success fw-bold mb-4">Montant : <?= number_format($montant, 0, ',', ' ') ?> XOF</h4>
            <p class="small text-muted">En cliquant sur "Confirmer et Lancer Kkiapay", la fenêtre sécurisée de Kkiapay s'ouvrira.</p>
          </div>
          <div class="modal-footer justify-content-center">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler / Modifier</button>
            <button type="button" class="btn btn-kkiapay-launch" onclick="ouvrirKkiapayWidget()">
                <i class="bi bi-lock-fill me-2"></i> Confirmer et Lancer Kkiapay
            </button>
          </div>
        </div>
      </div>
    </div>

    <?php if ($show_widget): ?>
        <kkiapay-widget
            amount="<?= $montant ?>"
            key="<?= $KKIAPAY_PUBLIC_KEY ?>"
            url="URL_VERS_VOTRE_LOGO.png"
            position="center"
            sandbox="true" 
            data='<?= $data_transaction ?>'
            callback="<?= $CALLBACK_URL ?>"
        ></kkiapay-widget>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <?php if ($show_widget): ?>
        <script>
            // Fonction JavaScript pour ouvrir le widget en modale Kkiapay
            function ouvrirKkiapayWidget() {
                // 1. Masquer la modale Bootstrap.
                const bootstrapModalElement = document.getElementById('confirmPaymentModal');
                
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const bootstrapModal = bootstrap.Modal.getInstance(bootstrapModalElement);
                    if (bootstrapModal) {
                        bootstrapModal.hide();
                    }
                }

                // 2. Ouvrir le widget Kkiapay (avec un léger délai pour la fermeture de la modale Bootstrap).
                setTimeout(() => {
                    const kkiapay_widget = document.querySelector('kkiapay-widget');
                    if (kkiapay_widget) {
                        kkiapay_widget.openWidget();
                    } else {
                        console.error("Erreur: Le widget Kkiapay n'a pas été trouvé.");
                    }
                }, 100); 
            }
        </script>
    <?php endif; ?>
</body>
</html>
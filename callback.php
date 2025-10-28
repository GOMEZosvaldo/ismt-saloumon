<?php
// =======================================================================
// 1. CONFIGURATION (Utiliser VOS CLÉS Kkiapay Sandbox)
// =======================================================================

// Clés Kkiapay pour le mode SANDBOX - VÉRIFIEZ VOS VRAIES CLÉS SANDBOX
$KKIAPAY_PUBLIC_KEY  = "TPK_95479fa0ab5e11f088425701ad7ad9d3"; 
$KKIAPAY_PRIVATE_KEY = "TSK_9547c6b0ab5e11f088425701ad7ad9d3";
$KKIAPAY_SECRET_KEY  = "tsk_9547c6b1ab5e11f088425701ad7ad9d3"; // REMPLACEZ PAR VOTRE CLÉ RÉELLE 

// =======================================================================
// 2. INCLUSION DES LIBRAIRIES
// =======================================================================

require 'vendor/autoload.php'; 
use Kkiapay\Kkiapay;

// =======================================================================
// 3. VÉRIFICATION DE LA TRANSACTION (Logique objet corrigée)
// =======================================================================

$kkiapay = new Kkiapay($KKIAPAY_PUBLIC_KEY, $KKIAPAY_PRIVATE_KEY, $KKIAPAY_SECRET_KEY, true);

$transaction_id = $_GET['transaction_id'] ?? null;

// Initialisation des variables pour l'affichage HTML
$statut = 'UNKNOWN'; 
$montant = 0;
$email_parent = 'Non spécifié';
$error_api = '';

if ($transaction_id) {
    try {
        // 2. Vérifier la transaction auprès de Kkiapay
        $transaction = $kkiapay->verifyTransaction($transaction_id); 
        
        // CORRECTION DE L'ACCÈS : Utilisation de -> car $transaction est un objet
        $statut = $transaction->status ?? 'FAILED';
        $montant = $transaction->amount ?? 0;
        
        // Récupérer l'email du parent (la propriété 'data' est une chaîne JSON)
        $data_json_string = $transaction->data ?? '{}';

        // Décoder la chaîne JSON 'data' en tableau associatif PHP
        $data = json_decode($data_json_string, true);
        if (isset($data['email'])) {
            $email_parent = $data['email'];
        }

        // 3. TRAITEMENT DE SUCCÈS
        if ($statut === 'SUCCESS') {
            echo "cool";
        }

    } catch (Exception $e) {
        $statut = 'ERROR'; 
        $error_api = $e->getMessage();
    }
} else {
    $statut = 'NO_ID';
}

// =======================================================================
// 4. DÉBUT DE LA STRUCTURE HTML/BOOTSTRAP
// =======================================================================
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statut de la Transaction</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card shadow-lg">
                    <div class="card-body text-center">
                        
                        <?php 
                        // --- Logique d'affichage basée sur le résultat du paiement ---
                        if (isset($statut) && $statut === 'SUCCESS') {
                            $titre = "Paiement Réussi ! 🎉";
                            $classe = "alert-success";
                            // CORRECTION DE LA SYNTAXE (Utilisation de <b> au lieu de ** et suppression de tout backtick)
                            $contenu = "Votre paiement de <b>" . $montant . " XOF</b> a été traité avec succès (Transaction ID: <b>" . $transaction_id . "</b>). Un email de confirmation sera envoyé à <b>" . $email_parent . "</b>.";
                        } elseif (isset($statut) && $statut !== 'SUCCESS' && $statut !== 'ERROR' && $statut !== 'NO_ID') {
                            $titre = "Paiement Échoué ou Annulé ❌";
                            $classe = "alert-warning";
                            // LIGNE 92 APPROXIMATIVE - Le backtick a été supprimé
                            $contenu = "Le statut de la transaction est : <b>" . $statut . "</b>. Veuillez réessayer avec des numéros de test valides (en mode Sandbox).";
                        } else {
                            // Cas d'erreur technique (problème d'API, clés invalides, ou pas d'ID)
                            $titre = "Erreur Technique ⚠️";
                            $classe = "alert-danger";
                            
                            if ($statut === 'NO_ID') {
                                $contenu = "ID de transaction manquant dans l'URL. Le paiement n'a pas pu être vérifié.";
                            } else {
                                $contenu = "Une erreur est survenue lors de la vérification de la transaction. Veuillez contacter l'assistance.";
                                if (!empty($error_api)) {
                                    $contenu .= "<br>Détail API : " . htmlspecialchars($error_api) . ". <b>Vérifiez vos clés TSK_ et Secret Sandbox !</b>";
                                }
                            }
                        }
                        ?>

                        <div class="alert <?= $classe ?>" role="alert">
                            <h4 class="alert-heading"><?= $titre ?></h4>
                            <p class="mb-0"><?= $contenu ?></p>
                        </div>
                        
                        <a href="index.php" class="btn btn-primary mt-3">Retour à la page d'accueil</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
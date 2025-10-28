<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=ismt_portail;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!isset($_SESSION['matricule']) || !isset($_SESSION['paiement_montant']) || !isset($_SESSION['email_parent'])) {
  die("Session invalide");
}

$matricule = $_SESSION['matricule'];
$montant = $_SESSION['paiement_montant'];
$email_parent = $_SESSION['email_parent'];

// Récupération de l'étudiant
$stmt = $pdo->prepare("SELECT id, nom, prenom FROM etudiants WHERE matricule = ?");
$stmt->execute([$matricule]);
$etudiant = $stmt->fetch(PDO::FETCH_ASSOC);

if ($etudiant) {
  // Mise à jour du paiement
  $update = $pdo->prepare("UPDATE paiements SET statut='Payé' WHERE etudiant_id=? ORDER BY id DESC LIMIT 1");
  $update->execute([$etudiant['id']]);

  // Envoi d'email au parent
  $to = $email_parent;
  $subject = "Confirmation du paiement - ISMT ST SALOMON";
  $message = "Bonjour,\n\nLe paiement de la scolarité de l'étudiant(e) {$etudiant['nom']} {$etudiant['prenom']} a été effectué avec succès.\nMontant : {$montant} FCFA.\n\nMerci de votre confiance.\n\nISMT ST SALOMON";
  $headers = "From: noreply@ismt.bj";

  mail($to, $subject, $message, $headers);
}

session_destroy();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Paiement réussi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="text-center p-5">
  <div class="alert alert-success">
    ✅ Paiement effectué avec succès !  
    Un email de confirmation a été envoyé à <strong><?= htmlspecialchars($email_parent) ?></strong>.
  </div>
  <a href="paiement.php" class="btn btn-primary mt-3">Retour</a>
</body>
</html>

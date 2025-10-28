<?php
session_start();
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$pdo = new PDO("mysql:host=localhost;dbname=ismt_portail;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$transactionId = $_GET['transaction'] ?? null;
$montant = $_SESSION['paiement_montant'] ?? 0;
$email_parent = $_SESSION['email_parent'] ?? null;

if(!$transactionId || !$montant || !$email_parent){
    die("Données manquantes.");
}

// Met à jour la base
$update = $pdo->prepare("UPDATE paiements SET statut='validé', transaction_id=? WHERE montant=? AND statut='en attente'");
$update->execute([$transactionId, $montant]);

// Envoi email
$mail = new PHPMailer(true);
try{
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'tonemail@gmail.com';
    $mail->Password = 'ton_mot_de_passe_app';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('no-reply@ismt.com','ISMT Saint Salomon');
    $mail->addAddress($email_parent);

    $mail->isHTML(true);
    $mail->Subject = 'Confirmation de paiement';
    $mail->Body = "Bonjour,<br>Votre paiement de <b>{$montant} FCFA</b> pour la scolarité de votre enfant a été reçu.<br>ID transaction : <b>{$transactionId}</b><br>Merci.";

    $mail->send();
    echo "Paiement validé et email envoyé.";
}catch(Exception $e){
    echo "Paiement validé mais email non envoyé. Erreur: {$mail->ErrorInfo}";
}

// Nettoyage session
unset($_SESSION['paiement_montant'], $_SESSION['email_parent']);
?>

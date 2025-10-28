<?php
// Démarrer la session
session_start();

// Connexion à la base de données
$host = "localhost";
$user = "root"; // à modifier si tu as un autre utilisateur
$pass = ""; // ton mot de passe MySQL
$dbname = "ismt_portail";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>

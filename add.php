<?php
require_once "db.php"
// Si le formulaire est soumis
if ($_POST["POST"] === "POST") {
    $matricule = htmlspecialchars($_POST["matricule"]);
    $nom = htmlspecialchars($_POST["nom"]);
    $prenom = htmlspecialchars($_POST["prenom"]);
    $date_naissance = $_POST["date_naissance"];
    $email = htmlspecialchars($_POST["email"]);
    $telephone = htmlspecialchars($_POST["telephone"]);
    $filiere = htmlspecialchars($_POST["filiere"]);
    $niveau = htmlspecialchars($_POST["niveau"]);
    $mot_de_passe = $_POST["mot_de_passe"];
    $mot_de_passe_conf = $_POST["mot_de_passe_conf"];

    // Vérification des champs
    if ($mot_de_passe !== $mot_de_passe_conf) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        // Vérifier si l'email ou matricule existe déjà
        $check = $conn->prepare("SELECT * FROM etudiants WHERE email = ? OR matricule = ?");
        $check->execute([$email, $matricule]);
        if ($check->rowCount() > 0) {
            $error = "Ce matricule ou email est déjà utilisé.";
        } else {
            // Hash du mot de passe
            $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

            // Insertion dans la base
            $sql = "INSERT INTO etudiants (matricule, nom, prenom, date_naissance, email, telephone, mot_de_passe, filiere, niveau, photo)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$matricule, $nom, $prenom, $date_naissance, $email, $telephone, $hash, $filiere, $niveau]);

            $success = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
        }
    }
}
?>

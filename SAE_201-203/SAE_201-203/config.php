<?php
$host = 'localhost';
$dbname = 'sae_201-203';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

session_start();

define('ROLE_ETUDIANT', 'etudiant');
define('ROLE_PROFESSEUR', 'professeur');
define('ROLE_ENTREPRISE', 'entreprise');

function redirect($url) {
    header("Location: $url");
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
}

function hasRole($role) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

function findUserByEmail($pdo, $email) {
    $stmt = $pdo->prepare("SELECT id, nom, prenom, adresse_email AS email, mot_de_passe AS password, 'etudiant' AS role FROM etudiant WHERE adresse_email = ?");
    $stmt->execute([$email]);
    if ($user = $stmt->fetch()) {
        return $user;
    }
    
    $stmt = $pdo->prepare("SELECT id, nom, prenom, adresse_email AS email, mot_de_passe AS password, 'professeur' AS role FROM professeur WHERE adresse_email = ?");
    $stmt->execute([$email]);
    if ($user = $stmt->fetch()) {
        return $user;
    }
    
    $stmt = $pdo->prepare("SELECT id, nom AS nom, '' AS prenom, adresse_email AS email, mot_de_passe AS password, 'entreprise' AS role FROM entreprise WHERE adresse_email = ?");
    $stmt->execute([$email]);
    if ($user = $stmt->fetch()) {
        return $user;
    }
    
    return false;
}

function getUserFullInfo($pdo, $userId, $role) {
    switch ($role) {
        case ROLE_ETUDIANT:
            $stmt = $pdo->prepare("SELECT * FROM etudiant WHERE id = ?");
            break;
        case ROLE_PROFESSEUR:
            $stmt = $pdo->prepare("SELECT * FROM professeur WHERE id = ?");
            break;
        case ROLE_ENTREPRISE:
            $stmt = $pdo->prepare("SELECT * FROM entreprise WHERE id = ?");
            break;
        default:
            return null;
    }
    $stmt->execute([$userId]);
    return $stmt->fetch();
}
?>
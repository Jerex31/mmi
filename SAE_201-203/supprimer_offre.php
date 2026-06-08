<?php
require_once 'check_session.php';
requireRole(ROLE_ENTREPRISE);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('espace_entreprise.php');
}

$offreId = $_GET['id'];

$stmt = $pdo->prepare("SELECT id FROM offre_de_stage WHERE id = ? AND id_entreprise = ?");
$stmt->execute([$offreId, $_SESSION['user_id']]);
$offre = $stmt->fetch();

if (!$offre) {
    redirect('espace_entreprise.php');
}

try {
    $stmt = $pdo->prepare("DELETE FROM offre_de_stage WHERE id = ?");
    $stmt->execute([$offreId]);
    $_SESSION['message'] = 'Offre supprimée avec succès !';
} catch (PDOException $e) {
    $_SESSION['error'] = 'Erreur lors de la suppression : ' . $e->getMessage();
}

redirect('espace_entreprise.php');
?>
<?php
require_once 'check_session.php';
requireRole(ROLE_PROFESSEUR);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('espace_jury.php');
}

$rapportId = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM rapport_de_stage WHERE id = ?");
$stmt->execute([$rapportId]);
$rapport = $stmt->fetch();

if (!$rapport) {
    redirect('espace_jury.php');
}

header('Content-Type: text/plain; charset=utf-8');
echo $rapport['contenu'];
exit();
?>
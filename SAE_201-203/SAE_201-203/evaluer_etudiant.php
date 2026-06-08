<?php
require_once 'check_session.php';
requireRole(ROLE_PROFESSEUR);

if (!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['soutenance'])) {
    redirect('espace_jury.php');
}

$etudiantId = $_GET['id'];
$soutenanceId = $_GET['soutenance'];

$stmt = $pdo->prepare("
    SELECT e.*, s.date, s.lieu, r.contenu
    FROM etudiant e
    JOIN jury j ON j.id_etudiant = e.id
    JOIN soutenance s ON j.id_soutenance = s.id
    LEFT JOIN rapport_de_stage r ON j.id_rapport = r.id
    WHERE e.id = ? AND s.id = ?
");
$stmt->execute([$etudiantId, $soutenanceId]);
$etudiant = $stmt->fetch();

if (!$etudiant) {
    redirect('espace_jury.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note = $_POST['note'] ?? null;
    $commentaire = $_POST['commentaire'] ?? '';

    try {
        $stmt = $pdo->prepare("
            UPDATE jury
            SET note = ?, commentaire = ?
            WHERE id_etudiant = ? AND id_soutenance = ?
        ");
        $stmt->execute([$note, $commentaire, $etudiantId, $soutenanceId]);
        $_SESSION['message'] = 'Évaluation enregistrée avec succès !';
        redirect('espace_jury.php');
    } catch (PDOException $e) {
        $error = 'Erreur : ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Évaluer un étudiant</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8f9fa; }
        .container { max-width: 800px; margin: 2rem auto; padding: 2rem; background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h1 { color: #003366; margin-bottom: 1.5rem; }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.3rem; font-weight: 500; }
        input, textarea { width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 4px; }
        .btn { padding: 0.8rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; font-weight: 500; }
        .btn-primary { background: #003366; color: white; }
        .btn-primary:hover { background: #0056b3; }
        .error { color: #dc3545; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Évaluer : <?php echo htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']); ?></h1>
        <p><strong>Date de soutenance :</strong> <?php echo date('d/m/Y H:i', strtotime($etudiant['date'])); ?></p>
        <p><strong>Lieu :</strong> <?php echo htmlspecialchars($etudiant['lieu']); ?></p>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="note">Note /20</label>
                <input type="number" id="note" name="note" min="0" max="20" step="0.5" required>
            </div>

            <div class="form-group">
                <label for="commentaire">Commentaire</label>
                <textarea id="commentaire" name="commentaire" rows="5" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Enregistrer l'évaluation</button>
            <a href="espace_jury.php" class="btn" style="background: #6c757d; color: white; margin-left: 1rem;">Annuler</a>
        </form>
    </div>
</body>
</html>
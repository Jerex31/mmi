<?php
require_once 'check_session.php';
requireRole(ROLE_ENTREPRISE);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('espace_entreprise.php');
}

$offreId = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM offre_de_stage WHERE id = ? AND id_entreprise = ?");
$stmt->execute([$offreId, $_SESSION['user_id']]);
$offre = $stmt->fetch();

if (!$offre) {
    redirect('espace_entreprise.php');
}

$stmt = $pdo->query("SELECT * FROM maitre_de_stage");
$maitres = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM professeur");
$professeurs = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['description'] ?? '';
    $montant = $_POST['montant_remuneration'] ?? null;
    $idMaitre = $_POST['id_maitre_de_stage'] ?? null;
    $idProfesseur = $_POST['id_professeur_suivi'] ?? null;

    try {
        $stmt = $pdo->prepare("
            UPDATE offre_de_stage
            SET description = ?, montant_remuneration = ?, id_maitre_de_stage = ?, id_professeur_suivi = ?
            WHERE id = ?
        ");
        $stmt->execute([$description, $montant, $idMaitre, $idProfesseur, $offreId]);
        $_SESSION['message'] = 'Offre modifiée avec succès !';
        redirect('espace_entreprise.php');
    } catch (PDOException $e) {
        $error = 'Erreur : ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Éditer une offre</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8f9fa; }
        .container { max-width: 800px; margin: 2rem auto; padding: 2rem; background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.3rem; font-weight: 500; }
        input, textarea, select { width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 4px; }
        .btn { padding: 0.8rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; font-weight: 500; }
        .btn-primary { background: #003366; color: white; }
        .btn-primary:hover { background: #0056b3; }
        .btn-secondary { background: #6c757d; color: white; }
        .error { color: #dc3545; margin-bottom: 1rem; }
        .success { color: #28a745; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Éditer l'offre #<?php echo $offre['id']; ?></h1>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="5" required><?php echo htmlspecialchars($offre['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="montant_remuneration">Montant de la rémunération (€)</label>
                <input type="number" id="montant_remuneration" name="montant_remuneration" step="0.01" value="<?php echo htmlspecialchars($offre['montant_remuneration']); ?>">
            </div>

            <div class="form-group">
                <label for="id_maitre_de_stage">Maître de stage</label>
                <select id="id_maitre_de_stage" name="id_maitre_de_stage">
                    <option value="">Aucun</option>
                    <?php foreach ($maitres as $maitre): ?>
                        <option value="<?php echo $maitre['id']; ?>" <?php echo $maitre['id'] == $offre['id_maitre_de_stage'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($maitre['nom'] . ' ' . $maitre['prenom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_professeur_suivi">Professeur de suivi</label>
                <select id="id_professeur_suivi" name="id_professeur_suivi">
                    <option value="">Aucun</option>
                    <?php foreach ($professeurs as $prof): ?>
                        <option value="<?php echo $prof['id']; ?>" <?php echo $prof['id'] == $offre['id_professeur_suivi'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($prof['nom'] . ' ' . $prof['prenom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="espace_entreprise.php" class="btn btn-secondary" style="margin-left: 1rem;">Annuler</a>
        </form>
    </div>
</body>
</html>
<?php
require_once 'check_session.php';
requireRole(ROLE_ENTREPRISE);

$entrepriseId = $_SESSION['user_id'];

$stmt = $pdo->query("SELECT * FROM maitre_de_stage");
$maitres = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM professeur");
$professeurs = $stmt->fetchAll();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['description'] ?? '';
    $montant = $_POST['montant_remuneration'] ?? null;
    $idMaitre = $_POST['id_maitre_de_stage'] ?? null;
    $idProfesseur = $_POST['id_professeur_suivi'] ?? null;
    
    if (empty($description)) {
        $error = 'La description est obligatoire.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO offre_de_stage (id_entreprise, id_maitre_de_stage, date_publication, description, montant_remuneration, id_professeur_suivi) 
                VALUES (?, ?, NOW(), ?, ?, ?)");
            $stmt->execute([$entrepriseId, $idMaitre, $description, $montant, $idProfesseur]);
            $success = 'Offre de stage ajoutée avec succès!';
        } catch (PDOException $e) {
            $error = 'Erreur lors de l\'ajout: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une offre - SAE 201-203</title>
</head>
<body>
    <main style="max-width: 800px; margin: 2rem auto; padding: 2rem; background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h1 style="color: #003366; margin-bottom: 1.5rem;">Ajouter une offre de stage</h1>
        
        <?php if ($error): ?>
            <div style="color: #dc3545; margin-bottom: 1rem;"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div style="color: #28a745; margin-bottom: 1rem;"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div style="margin-bottom: 1rem;">
                <label for="description" style="display: block; margin-bottom: 0.3rem; font-weight: 500;">Description</label>
                <textarea id="description" name="description" rows="5" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;" required></textarea>
            </div>
            
            <div style="margin-bottom: 1rem;">
                <label for="montant_remuneration" style="display: block; margin-bottom: 0.3rem; font-weight: 500;">Montant de la rémunération (€)</label>
                <input type="number" id="montant_remuneration" name="montant_remuneration" step="0.01" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            
            <div style="margin-bottom: 1rem;">
                <label for="id_maitre_de_stage" style="display: block; margin-bottom: 0.3rem; font-weight: 500;">Maître de stage</label>
                <select id="id_maitre_de_stage" name="id_maitre_de_stage" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="">Aucun</option>
                    <?php foreach ($maitres as $maitre): ?>
                    <option value="<?php echo $maitre['id']; ?>"><?php echo htmlspecialchars($maitre['nom'] . ' ' . $maitre['prenom']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label for="id_professeur_suivi" style="display: block; margin-bottom: 0.3rem; font-weight: 500;">Professeur de suivi</label>
                <select id="id_professeur_suivi" name="id_professeur_suivi" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="">Aucun</option>
                    <?php foreach ($professeurs as $prof): ?>
                    <option value="<?php echo $prof['id']; ?>"><?php echo htmlspecialchars($prof['nom'] . ' ' . $prof['prenom']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" style="background: #003366; color: white; padding: 0.8rem 1.5rem; border: none; border-radius: 4px; cursor: pointer;">Ajouter l'offre</button>

            <?php if ($success): ?>
                <script>
                    setTimeout(() => { window.location.href = 'espace_entreprise.php'; }, 300);
                </script>
            <?php endif; ?>
            <a href="espace_entreprise.php" style="margin-left: 1rem; color: #0056b3; text-decoration: none;">Annuler</a>
        </form>
    </main>
</body>
</html>
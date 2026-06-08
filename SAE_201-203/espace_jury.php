<?php
require_once 'check_session.php';
requireRole(ROLE_PROFESSEUR);

$stmt = $pdo->prepare("
    SELECT
        s.id AS soutenance_id,
        s.date,
        s.lieu,
        j.id AS jury_id,
        e.id AS etudiant_id,
        e.nom AS etudiant_nom,
        e.prenom AS etudiant_prenom,
        r.id AS rapport_id,
        r.date_remise,
        ent.nom AS entreprise_nom,
        st.promotion_concernee
    FROM jury j
    JOIN jury_professeur jp ON j.id = jp.id_jury
    JOIN soutenance s ON j.id_soutenance = s.id
    JOIN etudiant e ON j.id_etudiant = e.id
    LEFT JOIN rapport_de_stage r ON j.id_rapport = r.id
    LEFT JOIN stage st ON j.id_etudiant = st.id_etudiant
    LEFT JOIN offre_de_stage o ON st.id_offre_de_stage = o.id
    LEFT JOIN entreprise ent ON o.id_entreprise = ent.id
    WHERE jp.id_professeur = ?
    GROUP BY j.id 
    ORDER BY s.date
");
$stmt->execute([$_SESSION['user_id']]);
$soutenances = $stmt->fetchAll();

foreach ($soutenances as &$soutenance) {
    $soutenance['is_passee'] = strtotime($soutenance['date']) < time();
    $soutenance['note'] = null;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Jury - Application de Gestion des Stages</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-blue: #003366;
            --secondary-blue: #0056b3;
            --light-blue: #e6f2ff;
            --text-dark: #333;
            --text-light: #fff;
            --gray-bg: #f8f9fa;
            --border-color: #dee2e6;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            background-color: var(--gray-bg);
        }

        .header {
            background-color: var(--text-light);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo {
            height: 50px;
            width: auto;
        }

        .university-name {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary-blue);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logout-btn {
            color: var(--primary-blue);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border: 1px solid var(--primary-blue);
            border-radius: 4px;
            transition: all 0.3s;
        }

        .logout-btn:hover {
            background-color: var(--primary-blue);
            color: var(--text-light);
        }

        .menu {
            position: relative;
        }

        .menu-toggle {
            background: none;
            border: 2px solid var(--primary-blue);
            color: var(--primary-blue);
            padding: 0.6rem 1rem;
            border-radius: 999px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .menu-toggle:hover {
            background-color: rgba(0, 51, 102, 0.08);
        }

        .menu-dropdown {
            list-style: none;
            position: absolute;
            right: 0;
            top: calc(100% + 0.5rem);
            min-width: 220px;
            background-color: var(--text-light);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            padding: 0.5rem;
            display: none;
            z-index: 1000;
        }

        .menu-dropdown.open {
            display: block;
        }

        .menu-dropdown li {
            margin: 0;
        }

        .menu-dropdown a {
            display: block;
            padding: 0.6rem 0.8rem;
            border-radius: 8px;
            text-decoration: none;
            color: var(--primary-blue);
            font-weight: 500;
        }

        .menu-dropdown a:hover {
            background-color: var(--light-blue);
        }

        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .section-title {
            font-size: 1.8rem;
            color: var(--primary-blue);
            margin-bottom: 2rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--light-blue);
        }

        .jury-section {
            background-color: var(--text-light);
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 3rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            align-items: center;
        }

        .jury-image {
            width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .jury-description {
            color: var(--text-dark);
            font-size: 1.1rem;
            line-height: 1.8;
        }

        .soutenances-section {
            margin-bottom: 3rem;
        }

        .filters-container {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-btn {
            background-color: var(--text-light);
            border: 1px solid var(--secondary-blue);
            color: var(--secondary-blue);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background-color: var(--secondary-blue);
            color: var(--text-light);
        }

        .soutenances-table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--text-light);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            margin-top: 1rem;
        }

        .soutenances-table th {
            background-color: var(--primary-blue);
            color: var(--text-light);
            padding: 1rem;
            text-align: left;
            font-weight: 500;
        }

        .soutenances-table td {
            padding: 0.8rem 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .soutenances-table tr:last-child td {
            border-bottom: none;
        }

        .soutenances-table tr:hover {
            background-color: rgba(0, 51, 102, 0.05);
        }

        .status-badge {
            display: inline-block;
            padding: 0.3rem 0.6rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-a-passer {
            background-color: rgba(255, 193, 7, 0.2);
            color: var(--warning-color);
        }

        .status-passe {
            background-color: rgba(40, 167, 69, 0.2);
            color: var(--success-color);
        }

        .status-absent {
            background-color: rgba(220, 53, 69, 0.2);
            color: var(--danger-color);
        }

        .note-cell {
            font-weight: 600;
            color: var(--primary-blue);
        }

        .actions-cell {
            white-space: nowrap;
        }

        .action-btn {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 500;
            margin: 0 0.2rem;
        }

        .view-btn {
            background-color: var(--light-blue);
            color: var(--primary-blue);
        }

        .view-btn:hover {
            background-color: var(--primary-blue);
            color: var(--text-light);
        }

        .evaluate-btn {
            background-color: var(--light-blue);
            color: var(--primary-blue);
        }

        .evaluate-btn:hover {
            background-color: var(--success-color);
            color: var(--text-light);
        }

        .back-to-top-container {
            text-align: center;
            margin: 3rem 0;
        }

        .back-to-top {
            background-color: var(--text-light);
            border: 2px solid var(--secondary-blue);
            color: var(--secondary-blue);
            padding: 0.8rem 1.5rem;
            border-radius: 25px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .back-to-top:hover {
            background-color: var(--secondary-blue);
            color: var(--text-light);
        }

        .back-to-top-icon {
            color: var(--secondary-blue);
            font-size: 1.2rem;
        }

        .back-to-top:hover .back-to-top-icon {
            color: var(--text-light);
        }

        .footer {
            background-color: var(--primary-blue);
            color: var(--text-light);
            padding: 3rem 2rem 1rem;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-university {
            text-align: center;
            margin-bottom: 2rem;
        }

        .footer-info {
            text-align: center;
            margin-bottom: 2rem;
        }

        .footer-info h3 {
            color: var(--text-light);
            margin-bottom: 0.5rem;
            font-size: 1.5rem;
        }

        .footer-info p {
            color: rgba(255, 255, 255, 0.8);
            max-width: 600px;
            margin: 0 auto;
        }

        .footer-links,
        .footer-contact {
            margin-bottom: 2rem;
        }

        .footer-links h4,
        .footer-contact h4 {
            color: var(--text-light);
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .footer-links ul {
            list-style-type: none;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center;
        }

        .footer-links li a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 0.3rem 0.8rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .footer-links li a:hover {
            color: var(--text-light);
            border-color: var(--text-light);
        }

        .footer-contact p {
            color: rgba(255, 255, 255, 0.8);
            margin: 0.3rem 0;
            text-align: center;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding-top: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
        }

        .footer-legal {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .footer-legal p {
            color: rgba(255, 255, 255, 0.6);
            margin: 0;
            font-size: 0.9rem;
        }

        .footer-legal a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 0.9rem;
        }

        .footer-legal a:hover {
            color: var(--text-light);
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .header {
                padding: 1rem;
            }
            .university-name {
                font-size: 1rem;
            }
            .main-content {
                padding: 1rem;
            }
            .section-title {
                font-size: 1.5rem;
            }
            .jury-section {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            .filters-container {
                flex-direction: column;
                align-items: stretch;
            }
            .soutenances-table {
                font-size: 0.9rem;
            }
            .soutenances-table th,
            .soutenances-table td {
                padding: 0.6rem 0.5rem;
            }
            .footer-links ul {
                flex-direction: column;
                align-items: center;
            }
            .footer-contact p {
                text-align: center;
            }
            .footer-bottom {
                flex-direction: column;
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .logo-container {
                flex-direction: column;
                align-items: flex-start;
            }
            .actions-cell {
                display: flex;
                flex-direction: column;
                gap: 0.3rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo-container">
            <img src="img/logo.png" width="400px">
        </div>
        <div class="header-actions">
            <span>Bienvenue, <?php echo htmlspecialchars($_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom']); ?></span>
            <a href="logout.php" class="logout-btn">Déconnexion</a>
        </div>
        <div class="menu">
            <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="menu-dropdown">
                Menu
            </button>
            <ul id="menu-dropdown" class="menu-dropdown" role="menu">
                <li role="none"><a role="menuitem" href="espace_prof.php">Espace professeur</a></li>
                <li role="none"><a role="menuitem" href="espace_etudiant.php">Espace étudiant</a></li>
                <li role="none"><a role="menuitem" href="espace_entreprise.php">Espace entreprise</a></li>
            </ul>
        </div>
    </header>

    <main class="main-content">
        <section class="jury-section">
            <h2 class="section-title">Espace Jury</h2>
            <div class="jury-content">
                <img src="img/soutenance.jpg" width="400">
                <p class="jury-description">
                    Consultez les dates et horaires de passage des étudiants, leurs notes et évaluations, et accédez à leurs rapports de stage.
                </p>
            </div>
        </section>

        <section class="soutenances-section">
            <h2 class="section-title">Soutenances à évaluer</h2>

            <div class="filters-container">
                <button class="filter-btn active" data-filter="all">Toutes</button>
                <button class="filter-btn" data-filter="a-passer">À passer</button>
                <button class="filter-btn" data-filter="passe">Passées</button>
            </div>

            <?php if (empty($soutenances)): ?>
                <p style="text-align: center; color: #666; margin: 2rem 0;">Aucune soutenance assignée.</p>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="soutenances-table">
                        <thead>
                            <tr>
                                <th>Date & Heure</th>
                                <th>Étudiant</th>
                                <th>Entreprise</th>
                                <th>Promotion</th>
                                <th>Rapport</th>
                                <th>Note</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($soutenances as $soutenance): ?>
                            <tr class="soutenance-row" data-status="<?php echo $soutenance['is_passee'] ? 'passee' : 'a-passer'; ?>">
                                <td><?php echo date('d/m/Y H:i', strtotime($soutenance['date'])); ?></td>
                                <td><?php echo htmlspecialchars($soutenance['etudiant_prenom'] . ' ' . $soutenance['etudiant_nom']); ?></td>
                                <td><?php echo htmlspecialchars($soutenance['entreprise_nom'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($soutenance['promotion_concernee'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php if ($soutenance['rapport_id']): ?>
                                        <a href="voir_rapport.php?id=<?php echo $soutenance['rapport_id']; ?>" class="action-btn view-btn" target="_blank">Voir</a>
                                    <?php else: ?>
                                        <span style="color: #6c757d; font-size: 0.8rem;">Non soumis</span>
                                    <?php endif; ?>
                                </td>
                                <td class="note-cell">
                                    <?php if ($soutenance['is_passee'] && $soutenance['note'] !== null): ?>
                                        <?php echo htmlspecialchars($soutenance['note'] . '/20'); ?>
                                    <?php else: ?>
                                        -/-
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($soutenance['is_passee']): ?>
                                        <span class="status-badge status-passe">Passé</span>
                                    <?php else: ?>
                                        <span class="status-badge status-a-passer">À passer</span>
                                    <?php endif; ?>
                                </td>
                                <td class="actions-cell">
                                    <?php if ($soutenance['is_passee']): ?>
                                        <a href="evaluer_etudiant.php?id=<?php echo $soutenance['etudiant_id']; ?>&soutenance=<?php echo $soutenance['soutenance_id']; ?>" class="action-btn evaluate-btn">Évaluer</a>
                                    <?php else: ?>
                                        <span style="color: #6c757d; font-size: 0.8rem;">En attente</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <div class="back-to-top-container">
            <button class="back-to-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
                <span class="back-to-top-icon">▲</span>
                <span>Haut de page</span>
            </button>
        </div>
    </main>

    <footer class="footer">
        <!-- Le footer reste identique -->
        <div class="footer-content">
            <div class="footer-info">
                <h3>Application de Gestion des Stages BUT MMI</h3>
                <p>Plateforme dédiée au suivi des stages des étudiants du département MMI de Meaux.</p>
            </div>
            <div class="footer-links">
                <h4>Liens rapides</h4>
                <ul>
                    <li><a href="index.html">Accueil</a></li>
                    <li><a href="espace_etudiant.html">Offres de stage</a></li>
                    <li><a href="#">Tableau de bord</a></li>
                    <li><a href="#">Mon profil</a></li>
                    <li><a href="#">Contact</a></li>
                    <li><a href="#">Mentions légales</a></li>
                    <li><a href="#">Politique de confidentialité</a></li>
                </ul>
            </div>
            <div class="footer-contact">
                <h4>Contact</h4>
                <p>Département MMI Meaux</p>
                <p>IUT de Meaux</p>
                <p>17 Rue Jablinot, 77100 Meaux</p>
                <p>Téléphone : 01 64 36 44 10</p>
                <p>support-stage.mmi@univ-eiffel.fr</p>
            </div>
            <div class="footer-bottom">
                <div class="footer-legal">
                    <p>Tous droits réservés.</p>
                    <a href="#">Politique de confidentialité</a>
                    <a href="#">Conditions d'utilisation (CGU)</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        (function() {
            const toggleBtn = document.querySelector('.menu-toggle');
            const dropdown = document.querySelector('.menu-dropdown');
            if (!toggleBtn || !dropdown) return;

            const setOpen = (isOpen) => {
                dropdown.classList.toggle('open', isOpen);
                toggleBtn.setAttribute('aria-expanded', String(isOpen));
            };

            toggleBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const isOpen = dropdown.classList.contains('open');
                setOpen(!isOpen);
            });

            document.addEventListener('click', () => setOpen(false));

            dropdown.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (link) setOpen(false);
            });
        })();

        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const filter = this.dataset.filter;
                document.querySelectorAll('.soutenance-row').forEach(row => {
                    if (filter === 'all' || row.dataset.status === filter) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>
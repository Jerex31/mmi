<?php
require_once 'check_session.php';
requireRole(ROLE_ENTREPRISE);

$entreprise = getUserFullInfo($pdo, $_SESSION['user_id'], ROLE_ENTREPRISE);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Entreprise - Application de Gestion des Stages</title>
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

        .company-section {
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

        .company-image {
            width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .company-description {
            color: var(--text-dark);
            font-size: 1.1rem;
            line-height: 1.8;
        }

        .offers-section {
            margin-bottom: 3rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .stat-card {
            background-color: var(--text-light);
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            border: 1px solid var(--border-color);
        }

        .stat-label {
            color: var(--text-dark);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            color: var(--primary-blue);
            font-size: 2rem;
            font-weight: bold;
        }

        .add-offer-container {
            text-align: center;
            margin-top: 2rem;
        }

        .add-offer-btn {
            background-color: var(--light-blue);
            color: var(--primary-blue);
            border: 1px solid var(--primary-blue);
            padding: 0.8rem 1.5rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .add-offer-btn:hover {
            background-color: var(--primary-blue);
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

        .social-icons {
            display: flex;
            gap: 1rem;
        }

        .social-icons a {
            color: var(--text-light);
            font-size: 1.5rem;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .social-icons a:hover {
            color: var(--secondary-blue);
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
            .company-section {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            .stats-grid {
                grid-template-columns: 1fr;
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
        }

        .offers-table-section {
            margin: 2rem 0;
        }

        .offers-table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--text-light);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .offers-table th {
            background-color: var(--primary-blue);
            color: var(--text-light);
            padding: 1rem;
            text-align: left;
            font-weight: 500;
        }

        .offers-table td {
            padding: 0.8rem 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .offers-table tr:last-child td {
            border-bottom: none;
        }

        .offers-table tr:hover {
            background-color: rgba(0, 51, 102, 0.05);
        }

        .action-btn {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            margin: 0 0.3rem;
        }

        .edit-btn {
            background-color: var(--light-blue);
            color: var(--primary-blue);
        }

        .edit-btn:hover {
            background-color: var(--primary-blue);
            color: var(--text-light);
        }

        .delete-btn {
            background-color: #f8d7da;
            color: #721c24;
        }

        .delete-btn:hover {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo-container">
            <a href="index.html"><img src="img/logo.png" width="400px"></a>
        </div>
        <div class="header-actions">
            <span>Bienvenue, <?php echo htmlspecialchars($_SESSION['user_nom']); ?></span>
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
        <section class="company-section">
            <h2 class="section-title">Espace Entreprise</h2>
            <div class="company-content">
                <img src="img/entreprise.jpg" width="800">
                <p class="company-description">
                    Publier et gérer vos offres de stage destinées aux étudiants du BUT MMI.
                </p>
            </div>
        </section>

        <section class="offers-section">
            <h2 class="section-title">Gestion des offres de stage</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Offres publiées</div>
                    <div class="stat-value"><?php echo (int)($pdo->query("SELECT COUNT(*) FROM offre_de_stage WHERE id_entreprise = ".(int)$_SESSION['user_id']))->fetchColumn(); ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Candidatures reçues</div>
                    <div class="stat-value">48</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Étudiants recrutés</div>
                    <div class="stat-value">6</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Stages actifs</div>
                    <div class="stat-value">4</div>
                </div>
            </div>
            <div class="add-offer-container">
                <a href="ajout_offre.php" class="add-offer-btn">Ajouter une nouvelle offre</a>
            </div>

            <?php
            $stmt = $pdo->prepare("
                SELECT o.*, 
                    m.nom AS maitre_nom, 
                    m.prenom AS maitre_prenom,
                    p.nom AS professeur_nom,
                    p.prenom AS professeur_prenom,
                    s.id AS stage_id,
                    etu.nom AS etudiant_nom,
                    etu.prenom AS etudiant_prenom
                FROM offre_de_stage o
                LEFT JOIN maitre_de_stage m ON o.id_maitre_de_stage = m.id
                LEFT JOIN professeur p ON o.id_professeur_suivi = p.id
                LEFT JOIN stage s ON o.id = s.id_offre_de_stage
                LEFT JOIN etudiant etu ON s.id_etudiant = etu.id
                WHERE o.id_entreprise = ?
                ORDER BY o.date_publication DESC
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $offres = $stmt->fetchAll();
            ?>

        <section class="offers-table-section">
            <h2 class="section-title">Mes offres de stage publiées</h2>
            <?php if (empty($offres)): ?>
                <p style="text-align: center; color: #666; margin: 2rem 0;">Aucune offre publiée pour le moment.</p>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="offers-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Description</th>
                                <th>Date</th>
                                <th>Maître de stage</th>
                                <th>Professeur suivi</th>
                                <th>Rémunération</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($offres as $offre): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($offre['id']); ?></td>
                                <td><?php echo htmlspecialchars(substr($offre['description'], 0, 50)); ?></td>
                                <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($offre['date_publication']))); ?></td>
                                <td><?php echo htmlspecialchars(($offre['maitre_nom'] ?? '') . ' ' . ($offre['maitre_prenom'] ?? '')); ?></td>
                                <td><?php echo htmlspecialchars(($offre['professeur_nom'] ?? '') . ' ' . ($offre['professeur_prenom'] ?? '')); ?></td>
                                <td><?php echo htmlspecialchars($offre['montant_remuneration'] . ' €'); ?></td>
                                <td>
                                    <?php if ($offre['stage_id']): ?>
                                        <span style="color: #28a745; font-weight: 500;">✓ Attribué</span>
                                    <?php else: ?>
                                        <span style="color: #ffc107; font-weight: 500;">En attente</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="editer_offre.php?id=<?php echo $offre['id']; ?>" class="action-btn edit-btn">Éditer</a>
                                    <a href="supprimer_offre.php?id=<?php echo $offre['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette offre ?')">Supprimer</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
        </section>

        <div class="back-to-top-container">
            <button class="back-to-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
                <span class="back-to-top-icon">▲</span>
                <span>Haut de page</span>
            </button>
        </div>
    </main>

    <footer class="footer">
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

        document.querySelectorAll('.faq-question').forEach(button => {
            button.addEventListener('click', () => {
                const faqItem = button.parentElement;
                const isActive = faqItem.classList.contains('active');

                document.querySelectorAll('.faq-item').forEach(item => {
                    item.classList.remove('active');
                });

                if (!isActive) {
                    faqItem.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>
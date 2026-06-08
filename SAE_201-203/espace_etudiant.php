<?php
require_once 'check_session.php';
requireRole(ROLE_ETUDIANT);

$etudiant = getUserFullInfo($pdo, $_SESSION['user_id'], ROLE_ETUDIANT);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Étudiant - Application de Gestion des Stages</title>
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

        .student-section {
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

        .student-image {
            width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .student-description {
            color: var(--text-dark);
            font-size: 1.1rem;
            line-height: 1.8;
        }

        .offers-section {
            margin-bottom: 3rem;
        }

        .filters-container {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-group {
            display: flex;
            gap: 0.5rem;
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

        .search-container {
            flex-grow: 1;
            min-width: 250px;
        }

        .search-input {
            width: 100%;
            padding: 0.5rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 20px;
            font-size: 0.9rem;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--secondary-blue);
        }

        .offers-grid {
            display: grid;
            gap: 1.5rem;
        }

        .offer-card {
            background-color: var(--text-light);
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            border: 1px solid var(--border-color);
        }

        .offer-header {
            margin-bottom: 1rem;
        }

        .offer-title {
            color: var(--primary-blue);
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .offer-meta {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .offer-meta span {
            color: var(--text-dark);
        }

        .offer-meta strong {
            color: var(--primary-blue);
        }

        .offer-description {
            color: var(--text-dark);
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .offer-competences {
            margin-bottom: 1rem;
        }

        .offer-competences h4 {
            color: var(--primary-blue);
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .competences-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .competence-tag {
            background-color: var(--light-blue);
            color: var(--primary-blue);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
        }

        .offer-date {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: var(--text-dark);
            margin-bottom: 1rem;
        }

        .offer-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        .action-btn {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-details {
            background-color: var(--light-blue);
            color: var(--primary-blue);
            border: 1px solid var(--primary-blue);
        }

        .btn-details:hover {
            background-color: var(--primary-blue);
            color: var(--text-light);
        }

        .btn-apply {
            background-color: var(--text-light);
            color: var(--primary-blue);
            border: 1px solid var(--primary-blue);
        }

        .btn-apply:hover {
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
            .student-section {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            .filters-container {
                flex-direction: column;
                align-items: stretch;
            }
            .filter-group {
                justify-content: center;
            }
            .offer-actions {
                justify-content: center;
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
            .offer-actions {
                flex-direction: column;
            }
            .action-btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo-container">
            <a href="index.html"><img src="img/logo.png" width="400px"></a>
            <div class="university-name">Université<br>Gustave Eiffel</div>
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
        <section class="student-section">
            <h2 class="section-title">Espace Étudiant</h2>
            <div class="student-content">
                <img src="img/etudiant.jpg" width="800">
                <p class="student-description">
                    Consulter les offres proposées par les entreprises et soumettre directement vos candidatures via la plateforme.
                </p>
            </div>
        </section>

        <section class="offers-section">
            <h2 class="section-title">Découvrez les offres de stage disponibles</h2>
            <div class="filters-container">
                <div class="search-container">
                    <input type="text" class="search-input" placeholder="">
                </div>
            </div>

            <?php
            $stmt = $pdo->prepare(
                "SELECT o.*, 
                        ent.nom AS entreprise_nom 
                 FROM offre_de_stage o
                 LEFT JOIN entreprise ent ON o.id_entreprise = ent.id
                 ORDER BY o.date_publication DESC"
            );
            $stmt->execute();
            $offres = $stmt->fetchAll();
            ?>

            <div class="offers-grid">
                <?php if (empty($offres)): ?>
                    <p style="text-align:center; color:#666;">Aucune offre disponible pour le moment.</p>
                <?php else: ?>
                    <?php foreach ($offres as $offre): ?>
                        <div class="offer-card">
                            <div class="offer-header">
                                <h3 class="offer-title">Offre de stage</h3>
                                <div class="offer-meta">
                                    <span><strong>Entreprise:</strong> <?php echo htmlspecialchars($offre['entreprise_nom'] ?? ''); ?></span>
                                    <span><strong>Rémunération:</strong> <?php echo htmlspecialchars($offre['montant_remuneration'] ?? ''); ?>€</span>
                                </div>
                                <p class="offer-description">
                                    <?php echo htmlspecialchars(substr($offre['description'] ?? '', 0, 180)); ?>
                                </p>
                                <div class="offer-date">
                                    📅 publié le <?php echo htmlspecialchars(date('d/m/Y', strtotime($offre['date_publication']))); ?>
                                </div>
                                <div class="offer-actions">
                                    <a href="#" class="action-btn btn-details">Voir détails</a>
                                    <a href="#" class="action-btn btn-apply">Postuler</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
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
                    <li><a href="#">Accueil</a></li>
                    <li><a href="#">Offres de stage</a></li>
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
                <div class="social-icons">
                    <a href="#">📘</a>
                    <a href="#">🐦</a>
                    <a href="#">💼</a>
                </div>
                <div class="footer-legal">
                    <p>Tous droits réservés.</p>
                    <a href="#">Politique de confidentialité</a>
                    <a href="#">Conditions d'utilisation (CGU)</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.querySelector('.hamburger').addEventListener('click', function() {
            console.log('Menu toggled');
        });

        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>
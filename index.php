<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/rbac.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/functions.php';

$auth = new Auth();
$security = new Security();

$featuredProjects = [
    [
        'title' => 'Northline Studio',
        'description' => 'High-end brand identity and commerce experience for a boutique interiors studio.',
        'url' => 'https://example.com/northline',
        'tag' => 'Brand / Commerce'
    ],
    [
        'title' => 'Morrow House',
        'description' => 'Luxury editorial landing experience with motion-led conversion patterning.',
        'url' => 'https://example.com/morrow',
        'tag' => 'Editorial / UX'
    ],
    [
        'title' => 'Cinder & Form',
        'description' => 'Product story and detail-driven commerce system for a designer maker brand.',
        'url' => 'https://example.com/cinder-form',
        'tag' => 'E-commerce / Product'
    ],
    [
        'title' => 'Harbor Labs',
        'description' => 'A clean SaaS platform curating product launches for premium B2B teams.',
        'url' => 'https://example.com/harbor-labs',
        'tag' => 'SaaS / Product'
    ],
    [
        'title' => 'Aster & Vale',
        'description' => 'Boutique campaign interface blending storytelling with performance design.',
        'url' => 'https://example.com/aster-vale',
        'tag' => 'Campaign / Story'
    ],
    [
        'title' => 'Vanta Works',
        'description' => 'Architectural portfolio with immersive motion and layered product storytelling.',
        'url' => 'https://example.com/vanta-works',
        'tag' => 'Portfolio / Brand'
    ],
];
$csrf = $security->csrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>typeofshit — Portfolio</title>
    <meta name="description" content="Premium portfolio and case studies with modern editorial design.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= assetUrl('css/main.css') ?>">
    <link rel="stylesheet" href="<?= assetUrl('css/animations.css') ?>">
</head>
<body>
    <div class="noise-overlay"></div>

    <header class="topbar">
        <nav class="nav-shell">
            <div class="brand-wrap">
                <span class="brand-mark">-</span>
                <span class="brand-name">typeofshit</span>
            </div>

            <div class="nav-pill">
                <a href="#work">work</a>
                <a href="#story">story</a>
                <a href="#journal">journal</a>
                <a href="#contact">contact</a>
            </div>

            <div class="nav-actions">
                <?php if ($auth->isLoggedIn()): ?>
                    <a class="cart-button small" href="/<?= strtolower($_SESSION['user_role']) ?>/index.php">dashboard</a>
                    <a class="cart-button" href="/logout.php">logout</a>
                <?php else: ?>
                    <a class="cart-button" href="/login.php">login</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero section-sage">
            <div class="hero-inner reveal">
                <div class="hero-copy">
                    <p class="eyebrow">selected builds / online presence</p>
                    <h1>
                        <span>type</span>
                        <span>of</span>
                        <span>shit</span>
                    </h1>
                </div>

                <div class="hero-meta">
                    <div class="meta-block">
                        <p class="label">based in</p>
                        <p>Berlin / Remote</p>
                    </div>
                    <div class="meta-block">
                        <p class="label">focus</p>
                        <p>Brand systems / product stories / digital experiences</p>
                    </div>
                </div>
            </div>

            <div class="floating-visuals reveal">
                <div class="visual-card card-one">
                    <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=900&q=80" alt="Workspace" />
                </div>
                <div class="visual-card card-two">
                    <img src="https://images.unsplash.com/photo-1524758631624-e2822e304c36?auto=format&fit=crop&w=900&q=80" alt="Lifestyle" />
                </div>
                <div class="visual-card card-three">
                    <img src="https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=900&q=80" alt="Workspace detail" />
                </div>
            </div>
        </section>

        <section class="story-block section-olive" id="story">
            <div class="story-grid reveal">
                <div class="story-intro">
                    <p class="eyebrow">about</p>
                    <h2>Built to feel tactile, premium, and alive.</h2>
                </div>
                <div class="story-text">
                    <p>
                        I craft digital experiences for brands that want to look considered, feel human, and convert without looking generic.
                    </p>
                    <p>
                        Strategy, design systems, and refined build execution are all part of the same process.
                    </p>
                </div>
            </div>
        </section>

        <section class="work section-cream" id="work">
            <div class="work-header reveal">
                <h3>work</h3>
                <a href="#contact" class="circle-cta">book</a>
            </div>

            <div class="projects-grid">
                <?php foreach ($featuredProjects as $project): ?>
                    <article class="project-card reveal">
                        <div class="project-media">
                            <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=900&q=80" alt="<?= htmlspecialchars($project['title']) ?>">
                            <div class="project-overlay">
                                <a href="<?= htmlspecialchars($project['url']) ?>" target="_blank" rel="noreferrer">quick add</a>
                            </div>
                        </div>
                        <div class="project-copy">
                            <span><?= htmlspecialchars($project['tag']) ?></span>
                            <h4><?= htmlspecialchars($project['title']) ?></h4>
                            <p><?= htmlspecialchars($project['description']) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <footer class="site-footer" id="contact">
        <div class="footer-grid">
            <div class="footer-newsletter">
                <p class="label">newsletter</p>
                <form class="newsletter-form">
                    <input type="email" placeholder="your@email.com" aria-label="Email address">
                    <button type="submit">submit</button>
                </form>
            </div>

            <div class="footer-links">
                <div>
                    <p class="label">navigate</p>
                    <a href="#work">work</a>
                    <a href="#story">about</a>
                    <a href="#journal">journal</a>
                </div>
                <div>
                    <p class="label">connect</p>
                    <a href="https://instagram.com" target="_blank" rel="noreferrer">instagram</a>
                    <a href="https://dribbble.com" target="_blank" rel="noreferrer">dribbble</a>
                    <a href="mailto:hello@typeofshit.com">email</a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>© 2026 typeofshit</p>
            <div>
                <a href="#">terms</a>
                <a href="#">privacy</a>
            </div>
        </div>
    </footer>

    <script src="<?= assetUrl('js/main.js') ?>"></script>
</body>
</html>

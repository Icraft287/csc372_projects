<?php
/*
    File: destinations.php
    Author: Isaac Craft
    Date: March 25, 2026
    Description: Destinations page for T's Travel. Updated with:
                 - INSERT into inquiries table on successful form submit
                 - Inquiry count per trip pulled from DB
                 - ?type= query string filter for trip packages
                 - Trip type filter buttons (JS-powered client-side)
                 - PRG pattern, cookies, sessions, validation
                 - Visitor info panel hidden behind a toggle button
                 Claude AI was used to assist with comments and structure.
*/

session_start();

// =====================================================================
// COOKIE SETUP
// =====================================================================
$visit_count = isset($_COOKIE['visit_count']) && is_numeric($_COOKIE['visit_count'])
    ? (int)$_COOKIE['visit_count'] : 0;
$visit_count++;
setcookie('visit_count', $visit_count, time() + (30 * 24 * 60 * 60), '/');

$last_visit = isset($_COOKIE['last_visit']) ? $_COOKIE['last_visit'] : 'This is your first visit!';
setcookie('last_visit', date('F j, Y'), time() + (30 * 24 * 60 * 60), '/');

$cookie_name = isset($_COOKIE['visitor_name']) ? $_COOKIE['visitor_name'] : '';

// =====================================================================
// SESSION SETUP
// =====================================================================
if (!isset($_SESSION['inquiry_count']))      $_SESSION['inquiry_count']     = 0;
if (!isset($_SESSION['last_trip_type']))     $_SESSION['last_trip_type']    = '';
if (!isset($_SESSION['just_submitted_name']))$_SESSION['just_submitted_name'] = '';

// =====================================================================
// SUCCESS MESSAGE (PRG pattern)
// =====================================================================
$form_message = '';
$all_errors   = '';

if (isset($_SESSION['success_message'])) {
    $form_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// =====================================================================
// INCLUDES
// =====================================================================
require_once 'validate.php';
require_once 'db.php';
require_once 'destination.class.php';

// =====================================================================
// QUERY STRING FILTER (#1 new feature)
// =====================================================================
$allowed_trip_types = ['adventure', 'relaxation', 'cultural', 'family'];
$filter_type = $_GET['type'] ?? '';
if (!in_array($filter_type, $allowed_trip_types)) {
    $filter_type = '';
}

// =====================================================================
// FORM VALUES & ERRORS
// =====================================================================
$values = ['full_name' => '', 'travelers' => '', 'trip_type' => ''];
$errors = ['full_name' => '', 'travelers' => '', 'trip_type' => ''];

// =====================================================================
// FORM PROCESSING
// =====================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $values['full_name'] = $_POST['full_name'];
    $values['travelers'] = $_POST['travelers'];
    $values['trip_type'] = $_POST['trip_type'] ?? '';

    if (!is_valid_text($values['full_name'], 2, 50))
        $errors['full_name'] = 'Full name must be between 2 and 50 characters.';

    if (!is_valid_number($values['travelers'], 1, 20))
        $errors['travelers'] = 'Number of travelers must be a whole number between 1 and 20.';

    if (!is_valid_option($values['trip_type'], $allowed_trip_types))
        $errors['trip_type'] = 'Please select a valid trip type.';

    $all_errors = implode('', $errors);

    if ($all_errors === '') {

        $id_stmt = $pdo->prepare("SELECT trip_id FROM trips WHERE trip_type = ? LIMIT 1");
        $id_stmt->execute([$values['trip_type']]);
        $matched_trip = $id_stmt->fetch();
        $trip_id_for_insert = $matched_trip ? (int)$matched_trip['trip_id'] : 1;

        $insert = $pdo->prepare(
            "INSERT INTO inquiries (trip_id, trip_type, full_name, travelers)
             VALUES (?, ?, ?, ?)"
        );
        $insert->execute([
            $trip_id_for_insert,
            $values['trip_type'],
            $values['full_name'],
            (int)$values['travelers'],
        ]);

        setcookie('visitor_name', $values['full_name'], [
            'expires'  => time() + (30 * 24 * 60 * 60),
            'path'     => '/',
            'secure'   => false,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        $_SESSION['just_submitted_name'] = $values['full_name'];
        $_SESSION['inquiry_count']++;
        $_SESSION['last_trip_type'] = $values['trip_type'];

        $_SESSION['success_message'] = 'Thank you, ' . htmlspecialchars($values['full_name'])
            . '! Your inquiry for ' . htmlspecialchars($values['travelers'])
            . ' traveler(s) has been saved to our system. We will be in touch soon!';

        header('Location: destinations.php#inquiry-form');
        exit;

    } else {
        $form_message = 'Please correct the errors below and try again.';
    }
}

// =====================================================================
// DATABASE QUERY: trips with optional type filter
// =====================================================================
if ($filter_type !== '') {
    $sql  = "SELECT trip_id, trip_name, trip_type, description, price_per_person, max_travelers
             FROM trips WHERE trip_type = ? ORDER BY price_per_person ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$filter_type]);
} else {
    $sql  = "SELECT trip_id, trip_name, trip_type, description, price_per_person, max_travelers
             FROM trips ORDER BY price_per_person ASC";
    $stmt = $pdo->query($sql);
}
$trips = $stmt->fetchAll();

// =====================================================================
// INQUIRY COUNT PER TRIP
// =====================================================================
$count_stmt   = $pdo->query("SELECT trip_id, COUNT(*) AS inquiry_count FROM inquiries GROUP BY trip_id");
$inquiry_counts_raw = $count_stmt->fetchAll();
$inquiry_counts = [];
foreach ($inquiry_counts_raw as $row) {
    $inquiry_counts[(int)$row['trip_id']] = (int)$row['inquiry_count'];
}

// =====================================================================
// DESTINATION OBJECTS
// =====================================================================
$caribbean = new Destination("Caribbean Paradise","🌴",
    "Experience pristine beaches, crystal-clear waters, and vibrant island culture. The Caribbean offers the perfect blend of relaxation and adventure.",
    "caribbean","Caribbean beach destination",[
    ["name"=>"Jamaica","desc"=>"Reggae rhythms, stunning beaches, and warm hospitality"],
    ["name"=>"Aruba","desc"=>"Year-round sunshine and picture-perfect Caribbean shores"],
    ["name"=>"Bahamas","desc"=>"Island hopping, water sports, and luxurious resorts"],
    ["name"=>"St. Lucia","desc"=>"Dramatic landscapes, romantic settings, and tropical beauty"],
]);
$europe = new Destination("European Adventures","🗼",
    "Immerse yourself in centuries of history, art, and culture. From the romantic streets of Paris to the ancient ruins of Rome.",
    "europe","European city destination",[
    ["name"=>"Italy","desc"=>"Art, history, cuisine, and breathtaking landscapes"],
    ["name"=>"France","desc"=>"Romance, culture, wine country, and iconic landmarks"],
    ["name"=>"Greece","desc"=>"Ancient history, island hopping, and Mediterranean beauty"],
    ["name"=>"Spain","desc"=>"Vibrant cities, stunning coastlines, and rich traditions"],
],"var(--warm-sand)");
$cruises = new Destination("Cruise Destinations","🚢",
    "See the world from the sea with carefully selected cruise itineraries. Wake up in a new destination each day.",
    "cruises","Cruise ship at sea",[
    ["name"=>"Alaska","desc"=>"Glaciers, wildlife, and untouched natural beauty"],
    ["name"=>"Mediterranean","desc"=>"Explore multiple European countries in one voyage"],
    ["name"=>"Caribbean Islands","desc"=>"Island hop through tropical paradise"],
    ["name"=>"River Cruises","desc"=>"Intimate journeys through Europe and beyond"],
]);
$romantic = new Destination("Romantic Getaways","💑",
    "Create unforgettable memories with your special someone. Whether it's a honeymoon, anniversary, or just because.",
    "romantic","Romantic overwater bungalow getaway",[
    ["name"=>"Maldives","desc"=>"Overwater bungalows and pristine private beaches"],
    ["name"=>"Santorini","desc"=>"Stunning sunsets and white-washed village charm"],
    ["name"=>"Bora Bora","desc"=>"Turquoise lagoons and luxury overwater resorts"],
    ["name"=>"Tuscany","desc"=>"Rolling hills, vineyard stays, and Italian romance"],
],"var(--warm-sand)");

$destinations = [$caribbean, $europe, $cruises, $romantic];

$icons = ['adventure'=>'🏔️','relaxation'=>'🌴','cultural'=>'🏛️','family'=>'👨‍👩‍👧‍👦'];
$page_title       = "Destinations - T's Travel";
$meta_description = "Explore popular travel destinations with T's Travel";
$active_page      = 'destinations';
require_once 'header.php';
?>
    <style>
        /* Trip card header image */
        .card-trip-image {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 8px 8px 0 0;
            margin: -20px -20px 15px -20px;
            width: calc(100% + 40px);
            display: block;
        }
        .card-trip-image.loading-placeholder {
            background: linear-gradient(90deg, #e8e8e8 25%, #f5f5f5 50%, #e8e8e8 75%);
            background-size: 200% 100%;
            animation: shimmer 1.4s infinite;
        }
        @keyframes shimmer {
            0%   { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        /* Search + sort bar */
        .search-sort-bar {
            display: flex;
            gap: 12px;
            margin: 20px 0 10px;
            flex-wrap: wrap;
            align-items: center;
        }
        .search-sort-bar input {
            flex: 1;
            min-width: 200px;
            padding: 10px 16px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 0.95rem;
            font-family: 'Lato', sans-serif;
            transition: border-color 0.2s;
        }
        .search-sort-bar input:focus {
            outline: none;
            border-color: var(--accent-teal);
        }
        .search-sort-bar select {
            padding: 10px 16px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 0.95rem;
            font-family: 'Lato', sans-serif;
            background: white;
            cursor: pointer;
            transition: border-color 0.2s;
        }
        .search-sort-bar select:focus {
            outline: none;
            border-color: var(--accent-teal);
        }

        /* ── Tracking toggle button ── */
        .tracking-toggle-wrap {
            display: flex;
            justify-content: flex-end;
            padding: 14px 24px 0;
        }
        .tracking-toggle-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: transparent;
            border: 2px solid var(--accent-teal);
            color: var(--accent-teal);
            font-family: 'Lato', sans-serif;
            font-size: 0.88rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            padding: 8px 18px;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.18s, color 0.18s;
        }
        .tracking-toggle-btn:hover {
            background: var(--accent-teal);
            color: #fff;
        }
        .tracking-toggle-btn .toggle-arrow {
            display: inline-block;
            transition: transform 0.22s;
            font-style: normal;
        }
        .tracking-toggle-btn[aria-expanded="true"] .toggle-arrow {
            transform: rotate(180deg);
        }

        /* ── Collapsible panel animation ── */
        .visitor-info-section {
            overflow: hidden;
            max-height: 0;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            transition: max-height 0.35s ease, padding 0.25s ease;
        }
        .visitor-info-section.is-open {
            max-height: 400px; /* tall enough for content */
            padding-top: revert !important;
            padding-bottom: revert !important;
        }
    </style>
<body>

    <header class="hero">
        <div class="hero-content">
            <h1>Explore Dream Destinations</h1>
            <p>Discover where your next adventure will take you</p>
        </div>
    </header>

    <main>

        <!-- ============================================================
             TRACKING TOGGLE BUTTON
             Sits just above the collapsible visitor-info panel.
        ============================================================ -->
        <div class="tracking-toggle-wrap">
            <button class="tracking-toggle-btn"
                    id="tracking-toggle-btn"
                    aria-expanded="false"
                    aria-controls="visitor-info-section">
                🔍 Show Tracking Info <i class="toggle-arrow" aria-hidden="true">▾</i>
            </button>
        </div>

        <!-- VISITOR INFO PANEL (hidden by default) -->
        <section class="content-section visitor-info-section"
                 id="visitor-info-section"
                 aria-hidden="true">
            <div class="visitor-info-panel">
                <div class="visitor-info-group">
                    <h3>🍪 Your Visit Info <span class="info-label">(stored in cookies)</span></h3>
                    <ul>
                        <li><strong>Welcome back:</strong>
                            <?php
                            if (!empty($cookie_name)) {
                                echo htmlspecialchars($cookie_name);
                            } elseif (!empty($_SESSION['just_submitted_name'])) {
                                echo htmlspecialchars($_SESSION['just_submitted_name']);
                                unset($_SESSION['just_submitted_name']);
                            } else { echo 'Guest'; }
                            ?>
                        </li>
                        <li><strong>Page visits:</strong> <?= $visit_count ?></li>
                        <li><strong>Last visit:</strong> <?= htmlspecialchars($last_visit) ?></li>
                    </ul>
                </div>
                <div class="visitor-info-group">
                    <h3>🖥️ This Session <span class="info-label">(stored on server)</span></h3>
                    <ul>
                        <li><strong>Inquiries this session:</strong> <?= (int)$_SESSION['inquiry_count'] ?></li>
                        <li><strong>Last trip type inquired:</strong>
                            <?php if (!empty($_SESSION['last_trip_type'])): ?>
                                <?= htmlspecialchars(ucfirst($_SESSION['last_trip_type'])) ?>
                            <?php else: ?>None yet<?php endif; ?>
                        </li>
                    </ul>
                    <a href="clear_session.php" class="clear-session-btn">Clear Session</a>
                </div>
            </div>
        </section>

        <!-- ============================================================
             FEATURED TRIP PACKAGES
        ============================================================ -->
        <section class="content-section" id="trip-packages">
            <h2 class="section-title">
                Featured Trip Packages
                <?php if ($filter_type !== ''): ?>
                    <span style="font-size:1rem; color:var(--accent-teal); font-family:'Lato',sans-serif;">
                        — <?= htmlspecialchars(ucfirst($filter_type)) ?>
                    </span>
                <?php endif; ?>
            </h2>

            <div class="filter-bar" id="filter-bar">
                <button class="filter-btn active" data-filter="all">All</button>
                <button class="filter-btn" data-filter="adventure">🏔️ Adventure</button>
                <button class="filter-btn" data-filter="relaxation">🌴 Relaxation</button>
                <button class="filter-btn" data-filter="cultural">🏛️ Cultural</button>
                <button class="filter-btn" data-filter="family">👨‍👩‍👧‍👦 Family</button>
            </div>

            <div class="search-sort-bar">
                <input type="text" id="trip-search" placeholder="🔍 Search packages by name...">
                <select id="trip-sort">
                    <option value="">Sort: Default</option>
                    <option value="price-asc">Price: Low → High</option>
                    <option value="price-desc">Price: High → Low</option>
                </select>
            </div>

            <?php if (empty($trips)): ?>
                <p style="text-align:center; color:var(--text-dark); font-size:1.1rem; margin-top:20px;">
                    No trip packages found<?= $filter_type ? ' for "' . htmlspecialchars(ucfirst($filter_type)) . '"' : '' ?>.
                    <a href="destinations.php">View all packages</a>
                </p>
            <?php else: ?>
                <div class="card-grid" id="trips-grid">
                    <?php foreach ($trips as $trip):
                        $tid  = (int)$trip['trip_id'];
                        $icon = $icons[$trip['trip_type']] ?? '✈️';
                        $count = $inquiry_counts[$tid] ?? 0;
                    ?>
                        <article class="card trip-package-card"
                                 data-type="<?= htmlspecialchars($trip['trip_type']) ?>"
                                 data-price="<?= number_format((float)$trip['price_per_person'], 2, '.', '') ?>">
                            <img class="card-trip-image loading-placeholder"
                                 data-type="<?= htmlspecialchars($trip['trip_type']) ?>"
                                 alt="<?= htmlspecialchars($trip['trip_name']) ?>">
                            <div class="card-icon" aria-hidden="true"><?= $icon ?></div>
                            <h3><?= htmlspecialchars($trip['trip_name']) ?></h3>
                            <p class="trip-type-badge">
                                <?= htmlspecialchars(ucfirst($trip['trip_type'])) ?>
                            </p>
                            <p><?= htmlspecialchars($trip['description']) ?></p>
                            <div class="trip-package-meta">
                                <span class="trip-price">
                                    $<?= number_format((float)$trip['price_per_person'], 2) ?> / person
                                </span>
                                <span class="trip-max">
                                    Up to <?= (int)$trip['max_travelers'] ?> travelers
                                </span>
                            </div>
                            <?php if ($count > 0): ?>
                                <p class="inquiry-count-badge">
                                    🔥 <?= $count ?> <?= $count === 1 ? 'person has' : 'people have' ?> inquired
                                </p>
                            <?php endif; ?>
                            <a href="trip.php?id=<?= $tid ?>" class="trip-detail-link">View Details →</a>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- Destination sections loop -->
        <?php foreach ($destinations as $dest):
            $sectionStyle = !empty($dest->bgColor) ? 'style="background: ' . $dest->bgColor . ';"' : '';
        ?>
        <section class="content-section" <?= $sectionStyle ?> id="<?= htmlspecialchars($dest->anchor) ?>">
            <article class="service-category">
                <div class="service-header">
                    <div class="service-icon-large" aria-hidden="true"><?= $dest->icon ?></div>
                    <div>
                        <h2><?= htmlspecialchars($dest->getHeaderTitle()) ?></h2>
                        <p style="color:var(--accent-teal);font-size:0.95rem;margin-top:5px;">
                            <?= $dest->getSpotsCount() ?> featured destinations
                        </p>
                    </div>
                </div>
                <p class="service-description"><?= htmlspecialchars($dest->description) ?></p>
                <div class="destination-image-container" data-destination="<?= htmlspecialchars($dest->anchor) ?>">
                    <img class="destination-image loading-placeholder" alt="<?= htmlspecialchars($dest->altText) ?>" />
                    <div class="image-credit"></div>
                </div>
                <?= $dest->renderSpots() ?>
            </article>
        </section>
        <?php endforeach; ?>

        <!-- More destinations grid -->
        <section class="content-section">
            <h2 class="section-title">More Destinations We Specialize In</h2>
            <div class="card-grid">
                <article class="card"><div class="card-icon" aria-hidden="true">🏔️</div><h3>Adventure Travel</h3><p>Costa Rica, New Zealand, Iceland, and thrilling destinations</p></article>
                <article class="card"><div class="card-icon" aria-hidden="true">🌸</div><h3>Asia &amp; Pacific</h3><p>Thailand, Bali, Japan, Australia, and exotic locations</p></article>
                <article class="card"><div class="card-icon" aria-hidden="true">🦁</div><h3>Safari Adventures</h3><p>Kenya, Tanzania, South Africa, and wildlife experiences</p></article>
                <article class="card"><div class="card-icon" aria-hidden="true">🏙️</div><h3>City Escapes</h3><p>New York, London, Dubai, and vibrant urban destinations</p></article>
            </div>
        </section>

        <!-- INQUIRY FORM -->
        <section class="content-section" id="inquiry-form">
            <article class="service-category">
                <div class="service-header">
                    <div class="service-icon-large" aria-hidden="true">✈️</div>
                    <div>
                        <h2>Plan Your Trip</h2>
                        <p style="color:var(--accent-teal);font-size:0.95rem;margin-top:5px;">Tell us about your dream vacation</p>
                    </div>
                </div>
                <p class="service-description">Ready to start planning? Fill out the form below and one of our travel specialists will put together a personalized itinerary just for you.</p>

                <?php if ($form_message !== ''): ?>
                    <p class="form-message <?= ($all_errors === '') ? 'success' : 'error' ?>">
                        <?= $form_message ?>
                    </p>
                <?php endif; ?>

                <form action="destinations.php" method="POST" novalidate id="inquiry-form-el">
                    <div class="form-group">
                        <label for="full_name">Full Name:</label>
                        <input type="text" id="full_name" name="full_name"
                            value="<?= htmlspecialchars($values['full_name']) ?>"
                            placeholder="e.g. Jane Smith">
                        <?php if ($errors['full_name'] !== ''): ?>
                            <span class="error-msg"><?= $errors['full_name'] ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="travelers">Number of Travelers:</label>
                        <input type="number" id="travelers" name="travelers"
                            value="<?= htmlspecialchars($values['travelers']) ?>"
                            placeholder="e.g. 2">
                        <?php if ($errors['travelers'] !== ''): ?>
                            <span class="error-msg"><?= $errors['travelers'] ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <fieldset class="radio-fieldset">
                            <legend>Trip Type:</legend>
                            <?php foreach (['adventure'=>'🏔️ Adventure','relaxation'=>'🌴 Relaxation','cultural'=>'🏛️ Cultural','family'=>'👨‍👩‍👧‍👦 Family'] as $val => $label): ?>
                            <label class="radio-label">
                                <input type="radio" name="trip_type" value="<?= $val ?>"
                                    <?= ($values['trip_type'] === $val) ? 'checked' : '' ?>>
                                <?= $label ?>
                            </label>
                            <?php endforeach; ?>
                        </fieldset>
                        <?php if ($errors['trip_type'] !== ''): ?>
                            <span class="error-msg"><?= $errors['trip_type'] ?></span>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="cta-button">Submit Inquiry</button>
                </form>
            </article>
        </section>

        <section class="content-section" style="background:var(--warm-sand);text-align:center;">
            <h2 style="font-family:'Playfair Display',serif;font-size:2.5rem;color:var(--primary-blue);margin-bottom:20px;">Not Sure Where to Go?</h2>
            <p style="font-size:1.2rem;color:var(--text-dark);margin-bottom:30px;max-width:700px;margin-left:auto;margin-right:auto;">Let us help you find the perfect destination based on your interests, budget, and travel style.</p>
            <a href="contact.php" class="cta-button">Get Recommendations</a>
        </section>

    </main>

    <!-- ================================================================
         TRACKING TOGGLE SCRIPT
         Toggles .is-open on the panel and updates button label/aria state.
    ================================================================ -->
    <script>
    (function () {
        const btn   = document.getElementById('tracking-toggle-btn');
        const panel = document.getElementById('visitor-info-section');
        if (!btn || !panel) return;

        btn.addEventListener('click', function () {
            const isOpen = panel.classList.toggle('is-open');
            btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            panel.setAttribute('aria-hidden',  isOpen ? 'false' : 'true');
            // Swap button label text while keeping the arrow element intact
            btn.childNodes[0].textContent = isOpen ? '🔍 Hide Tracking Info ' : '🔍 Show Tracking Info ';
        });
    })();
    </script>

<?php require_once 'footer.php'; ?>
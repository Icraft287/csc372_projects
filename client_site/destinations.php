<?php
/*
    File: destinations.php
    Author: Isaac Crft
    Date: March 25, 2026
    Description: Destinations page for T's Travel – dynamically displays popular travel 
                 destination categories using the Destination class. Includes a trip inquiry 
                 form with PHP server-side validation, cookies to track visitor name and 
                 visit count, and sessions to track activity within the current browsing session.
                 Uses the Post-Redirect-Get (PRG) pattern so cookies and session values are 
                 correctly read after a successful form submission.
                 FIXED: Visitor name now updates immediately in "Welcome back" after submission.
                 Claude AI was used to assist with comments and structure.
*/

// =====================================================================
// SESSION START
// =====================================================================
session_start();

// =====================================================================
// COOKIE SETUP
// =====================================================================

// Read visit count from cookie. Default to 0 if this is a first visit.
$visit_count = isset($_COOKIE['visit_count']) && is_numeric($_COOKIE['visit_count'])
    ? (int)$_COOKIE['visit_count']
    : 0;

// Increment and write the updated count back to the browser.
$visit_count++;
setcookie('visit_count', $visit_count, time() + (30 * 24 * 60 * 60), '/');

// Read last visit date.
$last_visit = isset($_COOKIE['last_visit'])
    ? $_COOKIE['last_visit']
    : 'This is your first visit!';

setcookie('last_visit', date('F j, Y'), time() + (30 * 24 * 60 * 60), '/');

// Read the visitor's saved name from the cookie (for returning visitors)
$cookie_name = isset($_COOKIE['visitor_name']) ? $_COOKIE['visitor_name'] : '';

// =====================================================================
// SESSION SETUP
// =====================================================================

// Count how many inquiries the visitor has submitted this session.
if (!isset($_SESSION['inquiry_count'])) {
    $_SESSION['inquiry_count'] = 0;
}

// Remember the last trip type the visitor inquired about this session.
if (!isset($_SESSION['last_trip_type'])) {
    $_SESSION['last_trip_type'] = '';
}

// NEW: Temporary session variable to show the name immediately after successful submission + redirect
if (!isset($_SESSION['just_submitted_name'])) {
    $_SESSION['just_submitted_name'] = '';
}

// =====================================================================
// SUCCESS MESSAGE (Post-Redirect-Get pattern)
// =====================================================================
$form_message = '';
$all_errors   = '';

if (isset($_SESSION['success_message'])) {
    $form_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// =====================================================================
// INCLUDE VALIDATION FUNCTIONS
// =====================================================================
require_once 'validate.php';

// =====================================================================
// INCLUDE DATABASE CONNECTION
// db.php creates the $pdo object used to query the trips table below.
// =====================================================================
require_once 'db.php';

$allowed_trip_types = ['adventure', 'relaxation', 'cultural', 'family'];

// Default values shown in inputs
$values = [
    'full_name'  => '',
    'travelers'  => '',
    'trip_type'  => '',
];

// Error messages per field
$errors = [
    'full_name'  => '',
    'travelers'  => '',
    'trip_type'  => '',
];

// =====================================================================
// FORM PROCESSING (POST requests only)
// =====================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Collect submitted values
    $values['full_name'] = $_POST['full_name'];
    $values['travelers'] = $_POST['travelers'];
    $values['trip_type'] = $_POST['trip_type'] ?? '';

    // Validate each field
    if (!is_valid_text($values['full_name'], 2, 50)) {
        $errors['full_name'] = 'Full name must be between 2 and 50 characters.';
    }

    if (!is_valid_number($values['travelers'], 1, 20)) {
        $errors['travelers'] = 'Number of travelers must be a whole number between 1 and 20.';
    }

    if (!is_valid_option($values['trip_type'], $allowed_trip_types)) {
        $errors['trip_type'] = 'Please select a valid trip type.';
    }

    $all_errors = implode('', $errors);

    if ($all_errors === '') {
        // ----------------------------------------------------------------
        // ALL FIELDS VALID — Success path with PRG
        // ----------------------------------------------------------------

            // ==========================================
        // GET trip_id FROM trips TABLE
        // ==========================================
        $sql = "SELECT trip_id FROM trips WHERE trip_type = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$values['trip_type']]);
        $trip = $stmt->fetch();

        if ($trip) {
            $trip_id = (int)$trip['trip_id'];

            // ==========================================
            // INSERT INTO inquiries TABLE
            // ==========================================
            $sql = "INSERT INTO inquiries (trip_id, trip_type, full_name, travelers, submitted_at)
                    VALUES (?, ?, ?, ?, NOW())";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $trip_id,
                $values['trip_type'],
                $values['full_name'],
                (int)$values['travelers']
            ]);
        }

        // Save visitor name to cookie (will be available on the next request)
        setcookie(
            'visitor_name',
            $values['full_name'],
            [
                'expires'  => time() + (30 * 24 * 60 * 60),
                'path'     => '/',
                'secure'   => false,        // Change to true if site uses HTTPS
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );

        // Store name in session so we can display it immediately after redirect
        $_SESSION['just_submitted_name'] = $values['full_name'];

        // Update session data
        $_SESSION['inquiry_count']++;
        $_SESSION['last_trip_type'] = $values['trip_type'];

        // Success message stored in session (survives redirect)
        $_SESSION['success_message'] = 'Thank you, ' . htmlspecialchars($values['full_name'])
            . '! Your inquiry for ' . htmlspecialchars($values['travelers'])
            . ' traveler(s) has been received. We will be in touch soon!';

        // Redirect back to this page (PRG pattern)
        header('Location: destinations.php#inquiry-form');
        exit;

    } else {
        // Validation failed
        $form_message = 'Please correct the errors below and try again.';
    }
}

// =====================================================================
// DATABASE QUERY: Retrieve all trip packages from the trips table.
//
// Steps:
//  1. Write the SQL SELECT query as a string.
//  2. Execute it using $pdo->query() since there is no user input.
//  3. Fetch all rows at once into $trips using fetchAll().
//  4. If $trips is empty, a "not found" message is shown in the HTML.
//  5. All output is escaped with htmlspecialchars() before display.
// =====================================================================

// Step 1: SQL — retrieve all columns, ordered by price ascending
$sql = "SELECT trip_id, trip_name, trip_type, description, price_per_person, max_travelers
        FROM trips
        ORDER BY price_per_person ASC";

// Step 2: Execute — $pdo->query() runs a no-parameter query, returns PDOStatement
$stmt = $pdo->query($sql);

// Step 3: Fetch all rows into a PHP array of associative arrays
$trips = $stmt->fetchAll();

// =====================================================================
// CLASS DEFINITION
// =====================================================================
class Destination {

    public string $name;
    public string $icon;
    public string $description;
    public string $anchor;
    public string $altText;
    public string $bgColor;
    private array $spots;

    public function __construct(
        string $name,
        string $icon,
        string $description,
        string $anchor,
        string $altText,
        array  $spots,
        string $bgColor = ""
    ) {
        $this->name        = $name;
        $this->icon        = $icon;
        $this->description = $description;
        $this->anchor      = $anchor;
        $this->altText     = $altText;
        $this->spots       = $spots;
        $this->bgColor     = $bgColor;
    }

    public function getHeaderTitle(): string {
        return $this->icon . " " . $this->name;
    }

    public function getSpotsCount(): int {
        return count($this->spots);
    }

    public function renderSpots(): string {
        $html = '<div class="service-details">';
        foreach ($this->spots as $spot) {
            $html .= '
                <div class="service-feature">
                    <h3>' . htmlspecialchars($spot['name']) . '</h3>
                    <p>' . htmlspecialchars($spot['desc']) . '</p>
                </div>';
        }
        $html .= '</div>';
        return $html;
    }
}

// =====================================================================
// OBJECT CREATION
// =====================================================================

$caribbean = new Destination(
    name:        "Caribbean Paradise",
    icon:        "🌴",
    description: "Experience pristine beaches, crystal-clear waters, and vibrant island culture. The Caribbean offers the perfect blend of relaxation and adventure, with options ranging from all-inclusive resorts to intimate boutique properties.",
    anchor:      "caribbean",
    altText:     "Caribbean beach destination",
    spots: [
        ["name" => "Jamaica",    "desc" => "Reggae rhythms, stunning beaches, and warm hospitality"],
        ["name" => "Aruba",      "desc" => "Year-round sunshine and picture-perfect Caribbean shores"],
        ["name" => "Bahamas",    "desc" => "Island hopping, water sports, and luxurious resorts"],
        ["name" => "St. Lucia",  "desc" => "Dramatic landscapes, romantic settings, and tropical beauty"],
    ]
);

$europe = new Destination(
    name:        "European Adventures",
    icon:        "🗼",
    description: "Immerse yourself in centuries of history, art, and culture. From the romantic streets of Paris to the ancient ruins of Rome, European destinations offer unforgettable experiences for every traveler.",
    anchor:      "europe",
    altText:     "European city destination",
    bgColor:     "var(--warm-sand)",
    spots: [
        ["name" => "Italy",  "desc" => "Art, history, cuisine, and breathtaking landscapes"],
        ["name" => "France", "desc" => "Romance, culture, wine country, and iconic landmarks"],
        ["name" => "Greece", "desc" => "Ancient history, island hopping, and Mediterranean beauty"],
        ["name" => "Spain",  "desc" => "Vibrant cities, stunning coastlines, and rich traditions"],
    ]
);

$cruises = new Destination(
    name:        "Cruise Destinations",
    icon:        "🚢",
    description: "See the world from the sea with carefully selected cruise itineraries. Wake up in a new destination each day while enjoying world-class dining, entertainment, and amenities onboard.",
    anchor:      "cruises",
    altText:     "Cruise ship at sea",
    spots: [
        ["name" => "Alaska",            "desc" => "Glaciers, wildlife, and untouched natural beauty"],
        ["name" => "Mediterranean",     "desc" => "Explore multiple European countries in one voyage"],
        ["name" => "Caribbean Islands", "desc" => "Island hop through tropical paradise"],
        ["name" => "River Cruises",     "desc" => "Intimate journeys through Europe and beyond"],
    ]
);

$romantic = new Destination(
    name:        "Romantic Getaways",
    icon:        "💑",
    description: "Create unforgettable memories with your special someone. Whether it's a honeymoon, anniversary, or just because, these destinations set the perfect romantic scene.",
    anchor:      "romantic",
    altText:     "Romantic overwater bungalow getaway",
    bgColor:     "var(--warm-sand)",
    spots: [
        ["name" => "Maldives",  "desc" => "Overwater bungalows and pristine private beaches"],
        ["name" => "Santorini", "desc" => "Stunning sunsets and white-washed village charm"],
        ["name" => "Bora Bora", "desc" => "Turquoise lagoons and luxury overwater resorts"],
        ["name" => "Tuscany",   "desc" => "Rolling hills, vineyard stays, and Italian romance"],
    ]
);

$destinations = [$caribbean, $europe, $cruises, $romantic];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Explore popular travel destinations with T's Travel - Caribbean, Europe, Cruises, and Romantic Getaways">
    <title>Destinations - T's Travel</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar">
        <a href="index.html" class="logo">
            <div class="logo-icon" aria-hidden="true">✈️</div>
            <span>T's Travel</span>
        </a>
        <div class="hamburger" aria-label="Toggle navigation menu" role="button" tabindex="0">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="nav-links">
            <a href="index.html" class="nav-link">Home</a>
            <a href="about.html" class="nav-link">About</a>
            <a href="services.html" class="nav-link">Services</a>
            <a href="destinations.php" class="nav-link active">Destinations</a>
            <a href="contact.html" class="contact-btn">Contact Us</a>
        </div>
    </nav>

    <!-- Hero -->
    <header class="hero">
        <div class="hero-content">
            <h1>Explore Dream Destinations</h1>
            <p>Discover where your next adventure will take you</p>
        </div>
    </header>

    <main>

        <!-- ============================================================
             VISITOR INFO PANEL - UPDATED with immediate name display
        ============================================================ -->
        <section class="content-section visitor-info-section">
            <div class="visitor-info-panel">

                <!-- COOKIE DATA -->
                <div class="visitor-info-group">
                    <h3>🍪 Your Visit Info <span class="info-label">(stored in cookies)</span></h3>
                    <ul>
                        <li>
                            <strong>Welcome back:</strong>
                            <?php 
                            if (!empty($cookie_name)) {
                                echo htmlspecialchars($cookie_name);
                            } elseif (!empty($_SESSION['just_submitted_name'])) {
                                echo htmlspecialchars($_SESSION['just_submitted_name']);
                                // Clear the temporary session variable after displaying it once
                                unset($_SESSION['just_submitted_name']);
                            } else {
                                echo 'Guest';
                            }
                            ?>
                        </li>
                        <li>
                            <strong>Page visits:</strong> <?= $visit_count ?>
                        </li>
                        <li>
                            <strong>Last visit:</strong> <?= htmlspecialchars($last_visit) ?>
                        </li>
                    </ul>
                </div>

                <!-- SESSION DATA -->
                <div class="visitor-info-group">
                    <h3>🖥️ This Session <span class="info-label">(stored on server)</span></h3>
                    <ul>
                        <li>
                            <strong>Inquiries this session:</strong> <?= (int)$_SESSION['inquiry_count'] ?>
                        </li>
                        <li>
                            <strong>Last trip type inquired:</strong>
                            <?php if (!empty($_SESSION['last_trip_type'])): ?>
                                <?= htmlspecialchars(ucfirst($_SESSION['last_trip_type'])) ?>
                            <?php else: ?>
                                None yet
                            <?php endif; ?>
                        </li>
                    </ul>
                    <a href="clear_session.php" class="clear-session-btn">Clear Session</a>
                </div>

            </div>
        </section>

        <?php
        // ---------------------------------------------------------------
        // LOOP: Render each Destination object
        // ---------------------------------------------------------------
        foreach ($destinations as $dest):
            $sectionStyle = !empty($dest->bgColor) ? 'style="background: ' . $dest->bgColor . ';"' : '';
        ?>

        <section class="content-section" <?php echo $sectionStyle; ?> id="<?php echo htmlspecialchars($dest->anchor); ?>">
            <article class="service-category">

                <div class="service-header">
                    <div class="service-icon-large" aria-hidden="true"><?php echo $dest->icon; ?></div>
                    <div>
                        <h2><?php echo htmlspecialchars($dest->getHeaderTitle()); ?></h2>
                        <p style="color: var(--accent-teal); font-size: 0.95rem; margin-top: 5px;">
                            <?php echo $dest->getSpotsCount(); ?> featured destinations
                        </p>
                    </div>
                </div>

                <p class="service-description"><?php echo htmlspecialchars($dest->description); ?></p>

                <div class="destination-image-container" data-destination="<?php echo htmlspecialchars($dest->anchor); ?>">
                    <img class="destination-image loading-placeholder" alt="<?php echo htmlspecialchars($dest->altText); ?>" />
                    <div class="image-credit"></div>
                </div>

                <?php echo $dest->renderSpots(); ?>

            </article>
        </section>

        <?php endforeach; ?>

        <!-- More destinations grid (static) -->
        <section class="content-section">
            <h2 class="section-title">More Destinations We Specialize In</h2>
            <div class="card-grid">
                <article class="card">
                    <div class="card-icon" aria-hidden="true">🏔️</div>
                    <h3>Adventure Travel</h3>
                    <p>Costa Rica, New Zealand, Iceland, and thrilling destinations</p>
                </article>
                <article class="card">
                    <div class="card-icon" aria-hidden="true">🌸</div>
                    <h3>Asia &amp; Pacific</h3>
                    <p>Thailand, Bali, Japan, Australia, and exotic locations</p>
                </article>
                <article class="card">
                    <div class="card-icon" aria-hidden="true">🦁</div>
                    <h3>Safari Adventures</h3>
                    <p>Kenya, Tanzania, South Africa, and wildlife experiences</p>
                </article>
                <article class="card">
                    <div class="card-icon" aria-hidden="true">🏙️</div>
                    <h3>City Escapes</h3>
                    <p>New York, London, Dubai, and vibrant urban destinations</p>
                </article>
            </div>
        </section>

        <!-- TRIP INQUIRY FORM -->
        <section class="content-section" id="inquiry-form">
            <article class="service-category">

                <div class="service-header">
                    <div class="service-icon-large" aria-hidden="true">✈️</div>
                    <div>
                        <h2>Plan Your Trip</h2>
                        <p style="color: var(--accent-teal); font-size: 0.95rem; margin-top: 5px;">
                            Tell us about your dream vacation
                        </p>
                    </div>
                </div>

                <p class="service-description">
                    Ready to start planning? Fill out the form below and one of our travel specialists
                    will put together a personalized itinerary just for you.
                </p>

                <?php if ($form_message !== ''): ?>
                    <p class="form-message <?= ($all_errors === '') ? 'success' : 'error' ?>">
                        <?= $form_message ?>
                    </p>
                <?php endif; ?>

                <form action="destinations.php" method="POST" novalidate>

                    <div class="form-group">
                        <label for="full_name">Full Name:</label>
                        <input
                            type="text"
                            id="full_name"
                            name="full_name"
                            value="<?= htmlspecialchars($values['full_name']) ?>"
                            placeholder="e.g. Jane Smith">
                        <?php if ($errors['full_name'] !== ''): ?>
                            <span class="error-msg"><?= $errors['full_name'] ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="travelers">Number of Travelers:</label>
                        <input
                            type="number"
                            id="travelers"
                            name="travelers"
                            value="<?= htmlspecialchars($values['travelers']) ?>"
                            placeholder="e.g. 2">
                        <?php if ($errors['travelers'] !== ''): ?>
                            <span class="error-msg"><?= $errors['travelers'] ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <fieldset class="radio-fieldset">
                            <legend>Trip Type:</legend>

                            <label class="radio-label">
                                <input type="radio" name="trip_type" value="adventure"
                                    <?= ($values['trip_type'] === 'adventure') ? 'checked' : '' ?>>
                                🏔️ Adventure
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="trip_type" value="relaxation"
                                    <?= ($values['trip_type'] === 'relaxation') ? 'checked' : '' ?>>
                                🌴 Relaxation
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="trip_type" value="cultural"
                                    <?= ($values['trip_type'] === 'cultural') ? 'checked' : '' ?>>
                                🏛️ Cultural
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="trip_type" value="family"
                                    <?= ($values['trip_type'] === 'family') ? 'checked' : '' ?>>
                                👨‍👩‍👧‍👦 Family
                            </label>

                        </fieldset>
                        <?php if ($errors['trip_type'] !== ''): ?>
                            <span class="error-msg"><?= $errors['trip_type'] ?></span>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="cta-button">Submit Inquiry</button>

                </form>

            </article>
        </section>

        <!-- Final CTA -->
        <section class="content-section" style="background: var(--warm-sand); text-align: center;">
            <h2 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; color: var(--primary-blue); margin-bottom: 20px;">Not Sure Where to Go?</h2>
            <p style="font-size: 1.2rem; color: var(--text-dark); margin-bottom: 30px; max-width: 700px; margin-left: auto; margin-right: auto;">Let us help you find the perfect destination based on your interests, budget, and travel style. We'll provide personalized recommendations and handle all the planning details.</p>
            <a href="contact.html" class="cta-button">Get Recommendations</a>
        </section>

    </main>

    <!-- Footer -->
    <footer class="footer">
        <nav class="footer-links" aria-label="Footer navigation">
            <a href="index.html" class="footer-link">Home</a>
            <a href="about.html" class="footer-link">About</a>
            <a href="services.html" class="footer-link">Services</a>
            <a href="destinations.php" class="footer-link">Destinations</a>
            <a href="contact.html" class="footer-link">Contact</a>
        </nav>
        <div class="footer-bottom">
            <p>&copy; 2026 T's Travel. All rights reserved.</p>
            <div class="social-links">
                <a href="#" class="social-link" aria-label="Visit our Facebook page">Facebook</a>
                <a href="#" class="social-link" aria-label="Visit our Instagram page">Instagram</a>
                <a href="#" class="social-link" aria-label="Visit our Twitter page">Twitter</a>
            </div>
        </div>
    </footer>

    <script src="js/server.js"></script>
</body>
</html>
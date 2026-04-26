<?php
/*
    File: trip.php
    Author: Isaac Crft
    Date: March 25, 2026
    Description: Dynamic trip detail page for T's Travel.
                 Uses a query string (?id=X) to retrieve and display
                 a single trip package from the trips table via PDO.
                 Demonstrates: query string validation, prepared statements,
                 single-row fetch, and escaped output.
                 Claude AI was used to assist with comments and structure.
*/

// =====================================================================
// INCLUDE DATABASE CONNECTION
// db.php creates the $pdo object needed for the prepared statement below.
// =====================================================================
require_once 'db.php';

// =====================================================================
// STEP 1: RETRIEVE THE VALUE FROM THE QUERY STRING
// The visitor arrives here via a URL like: trip.php?id=2
// $_GET['id'] holds the raw string value from the URL.
// We use the null coalescing operator ?? to default to '' if not set.
// =====================================================================
$raw_id = $_GET['id'] ?? '';

// =====================================================================
// STEP 2: VALIDATE THE QUERY STRING VALUE
// Before using the id in a SQL query we must confirm it is:
//   - Not empty
//   - A whole number (ctype_digit works on strings, rejects negatives/floats)
//   - Greater than zero
// If any check fails we set an error message and skip the database query.
// =====================================================================
$query_error = '';
$trip        = null;   // will hold the fetched row if found

if ($raw_id === '' || !ctype_digit($raw_id) || (int)$raw_id < 1) {
    // The id is missing, non-numeric, or not a positive integer
    $query_error = 'Invalid trip ID. Please select a trip from the Destinations page.';
} else {
    // id passed validation — safe to cast and query
    $trip_id = (int)$raw_id;

    // =====================================================================
    // STEP 3: PREPARED STATEMENT WITH PLACEHOLDER
    // Using a placeholder (?) instead of inserting $trip_id directly into
    // the SQL string prevents SQL injection, even though we already validated.
    // prepare() sends the SQL structure to MySQL before any data is attached.
    // =====================================================================
    $sql  = "SELECT trip_id, trip_name, trip_type, description, price_per_person, max_travelers
             FROM trips
             WHERE trip_id = ?";
    $stmt = $pdo->prepare($sql);

    // =====================================================================
    // STEP 4: EXECUTE THE QUERY
    // execute() binds the value to the placeholder and runs the query.
    // The result set is stored in $stmt.
    // =====================================================================
    $stmt->execute([$trip_id]);

    // =====================================================================
    // STEP 5: FETCH THE RESULT
    // fetch() retrieves a single row as an associative array.
    // Returns false if no row matched — we check for that below.
    // =====================================================================
    $trip = $stmt->fetch();

    // If no row was returned the id doesn't exist in the database
    if (!$trip) {
        $query_error = 'Trip not found. The package you are looking for does not exist.';
    }
}

// Emoji icon map — used in the HTML below
$icons = [
    'adventure'  => '🏔️',
    'relaxation' => '🌴',
    'cultural'   => '🏛️',
    'family'     => '👨‍👩‍👧‍👦',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php
        // Step 5 continued: escape and display the trip name in the title if available
        echo $trip ? htmlspecialchars($trip['trip_name']) . ' - T\'s Travel' : 'Trip Not Found - T\'s Travel';
        ?>
    </title>
    <!-- Preconnect to Google Fonts servers to reduce DNS lookup time -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!--
        display=swap tells the browser to render text in a fallback font immediately
        while Playfair Display and Lato load in the background.
        This prevents the page from being blank while waiting for fonts.
    -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css?v=2">
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar">
        <a href="index.php" class="logo">
            <div class="logo-icon" aria-hidden="true">✈️</div>
            <span>T's Travel</span>
        </a>
        <div class="hamburger" aria-label="Toggle navigation menu" role="button" tabindex="0">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="nav-links">
            <a href="index.php" class="nav-link">Home</a>
            <a href="about.php" class="nav-link">About</a>
            <a href="services.php" class="nav-link">Services</a>
            <a href="destinations.php" class="nav-link active">Destinations</a>
            <a href="contact.php" class="contact-btn">Contact Us</a>
        </div>
    </nav>

    <header class="hero">
        <div class="hero-content">
            <h1>Trip Details</h1>
            <p>Everything you need to know about this package</p>
        </div>
    </header>

    <main>
        <section class="content-section">
            <article class="service-category">

                <?php if ($query_error !== ''): ?>
                    <!--
                        Query string was invalid or no matching row was found.
                        Display the error message instead of trip data.
                        htmlspecialchars() escapes it even though we wrote it ourselves —
                        good habit to always escape before output.
                    -->
                    <p class="form-message error"><?= htmlspecialchars($query_error) ?></p>
                    <p style="margin-top: 20px;">
                        <a href="destinations.php" class="cta-button">← Back to Destinations</a>
                    </p>

                <?php else: ?>
                    <!--
                        A valid row was fetched — display all fields.
                        Every value from $trip is passed through htmlspecialchars()
                        before being written into the HTML to prevent XSS.
                    -->

                    <?php $icon = $icons[$trip['trip_type']] ?? '✈️'; ?>

                    <div class="service-header">
                        <div class="service-icon-large" aria-hidden="true"><?= $icon ?></div>
                        <div>
                            <!-- trip_name — escaped string from the database -->
                            <h2><?= htmlspecialchars($trip['trip_name']) ?></h2>
                            <!-- trip_type — ucfirst for display -->
                            <p style="color: var(--accent-teal); font-size: 0.95rem; margin-top: 5px;">
                                <?= htmlspecialchars(ucfirst($trip['trip_type'])) ?> Package
                            </p>
                        </div>
                    </div>

                    <!-- description — escaped string from the database -->
                    <p class="service-description"><?= htmlspecialchars($trip['description']) ?></p>

                    <div class="trip-detail-meta">
                        <div class="trip-detail-item">
                            <span class="trip-detail-label">Price per person</span>
                            <!--
                                price_per_person — cast to float, formatted with number_format()
                                for currency display (e.g. $1,299.99)
                            -->
                            <span class="trip-detail-value trip-price">
                                $<?= number_format((float)$trip['price_per_person'], 2) ?>
                            </span>
                        </div>
                        <div class="trip-detail-item">
                            <span class="trip-detail-label">Maximum group size</span>
                            <!--
                                max_travelers — cast to int for safe output
                            -->
                            <span class="trip-detail-value">
                                <?= (int)$trip['max_travelers'] ?> travelers
                            </span>
                        </div>
                        <div class="trip-detail-item">
                            <span class="trip-detail-label">Package ID</span>
                            <!-- trip_id — already validated and cast to int above -->
                            <span class="trip-detail-value">#<?= (int)$trip['trip_id'] ?></span>
                        </div>
                    </div>

                    <div style="margin-top: 30px; display: flex; gap: 15px; flex-wrap: wrap;">
                        <a href="destinations.php#inquiry-form" class="cta-button">Inquire About This Trip</a>
                        <a href="destinations.php" class="cta-button" style="background: var(--primary-blue);">← All Packages</a>
                    </div>

                <?php endif; ?>

            </article>
        </section>
    </main>

    <footer class="footer">
        <nav class="footer-links" aria-label="Footer navigation">
            <a href="index.php" class="footer-link">Home</a>
            <a href="about.php" class="footer-link">About</a>
            <a href="services.php" class="footer-link">Services</a>
            <a href="destinations.php" class="footer-link">Destinations</a>
            <a href="contact.php" class="footer-link">Contact</a>
        </nav>
        <div class="footer-bottom">
            <p>&copy; 2026 T's Travel. All rights reserved.</p>
        </div>
    </footer>

    <script src="js/server.js"></script>
</body>
</html>

<?php
/*
    File: destinations.php
    Author: Isaac Crft
    Date: March 8, 2026
    Description: Destinations page for T's Travel – dynamically displays popular travel 
                 destination categories using the Destination class. Each destination object 
                 holds its name, icon, description, highlight spots, and display settings.
                 Claude Ai was used to assit with making my comments look goog, undertstandble, ad in the right place.
*/

// =====================================================================
// CLASS DEFINITION
// Destination models a travel destination category shown on this page.
// Each instance represents one section (e.g. Caribbean, Europe, etc.)
// =====================================================================
class Destination {

    // $name: The display title of the destination category (e.g. "Caribbean Paradise")
    public string $name;

    // $icon: Emoji icon used in the section header
    public string $icon;

    // $description: Paragraph text describing the destination category
    public string $description;

    // $anchor: The HTML id anchor for this section (e.g. "caribbean")
    public string $anchor;

    // $altText: Alt text for the destination image
    public string $altText;

    // $bgColor: Optional inline background color for alternating section style
    public string $bgColor;

    // $spots: Array of sub-destinations, each with a name and short description
    // Each element is an associative array: ['name' => ..., 'desc' => ...]
    private array $spots;

    // Constructor: accepts all properties and sets them via $this
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

    // ----------------------------------------------------------------
    // METHOD 1: getHeaderTitle()
    // Returns a formatted string combining the icon and destination name.
    // Used in the section <h2> heading.
    // ----------------------------------------------------------------
    public function getHeaderTitle(): string {
        return $this->icon . " " . $this->name;
    }

    // ----------------------------------------------------------------
    // METHOD 2: getSpotsCount()
    // Returns the number of highlight spots for this destination.
    // Used to dynamically show "X featured destinations" in the section.
    // ----------------------------------------------------------------
    public function getSpotsCount(): int {
        return count($this->spots);
    }

    // ----------------------------------------------------------------
    // METHOD 3: renderSpots()
    // Loops through the $spots array and returns the complete HTML 
    // for the service-details grid of feature boxes.
    // ----------------------------------------------------------------
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
// Creating instances of Destination — each one is a unique category.
// =====================================================================

// Object 1: Caribbean
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

// Object 2: Europe
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

// Object 3: Cruises
$cruises = new Destination(
    name:        "Cruise Destinations",
    icon:        "🚢",
    description: "See the world from the sea with carefully selected cruise itineraries. Wake up in a new destination each day while enjoying world-class dining, entertainment, and amenities onboard.",
    anchor:      "cruises",
    altText:     "Cruise ship at sea",
    spots: [
        ["name" => "Alaska",           "desc" => "Glaciers, wildlife, and untouched natural beauty"],
        ["name" => "Mediterranean",    "desc" => "Explore multiple European countries in one voyage"],
        ["name" => "Caribbean Islands","desc" => "Island hop through tropical paradise"],
        ["name" => "River Cruises",    "desc" => "Intimate journeys through Europe and beyond"],
    ]
);

// Object 4: Romantic Getaways
$romantic = new Destination(
    name:        "Romantic Getaways",
    icon:        "💑",
    description: "Create unforgettable memories with your special someone. Whether it's a honeymoon, anniversary, or just because, these destinations set the perfect romantic scene.",
    anchor:      "romantic",
    altText:     "Romantic overwater bungalow getaway",
    bgColor:     "var(--warm-sand)",
    spots: [
        ["name" => "Maldives",   "desc" => "Overwater bungalows and pristine private beaches"],
        ["name" => "Santorini",  "desc" => "Stunning sunsets and white-washed village charm"],
        ["name" => "Bora Bora",  "desc" => "Turquoise lagoons and luxury overwater resorts"],
        ["name" => "Tuscany",    "desc" => "Rolling hills, vineyard stays, and Italian romance"],
    ]
);

// Collect all destination objects into an array for easy looping
$destinations = [$caribbean, $europe, $cruises, $romantic];
?>
<!DOCTYPE html>
<!-- 
    File: destinations.php
    Author: Isaac Crft
    Date: March 8, 2026
    Description: Destinations page for T's Travel – highlights popular travel spots 
                 and categories using PHP objects built from the Destination class.
-->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Explore popular travel destinations with T's Travel - Caribbean, Europe, Cruises, and Romantic Getaways">
    <title>Destinations - T's Travel</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    
    <!-- Main stylesheet -->
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

    <!-- Hero section -->
    <header class="hero">
        <div class="hero-content">
            <h1>Explore Dream Destinations</h1>
            <p>Discover where your next adventure will take you</p>
        </div>
    </header>

    <main>

        <?php
        // ---------------------------------------------------------------
        // LOOP: Render each Destination object as a full page section.
        // Accesses the public $bgColor property directly.
        // Calls getHeaderTitle(), getSpotsCount(), and renderSpots().
        // ---------------------------------------------------------------
        foreach ($destinations as $dest):
            // Access the public $bgColor property to set alternating backgrounds
            $sectionStyle = !empty($dest->bgColor) ? 'style="background: ' . $dest->bgColor . ';"' : '';
        ?>

        <section class="content-section" <?php echo $sectionStyle; ?> id="<?php echo htmlspecialchars($dest->anchor); ?>">
            <article class="service-category">

                <div class="service-header">
                    <div class="service-icon-large" aria-hidden="true"><?php echo $dest->icon; ?></div>
                    <div>
                        <!-- Calls getHeaderTitle() to display icon + name together -->
                        <h2><?php echo htmlspecialchars($dest->getHeaderTitle()); ?></h2>
                        <!-- Calls getSpotsCount() to show how many spots are listed -->
                        <p style="color: var(--accent-teal); font-size: 0.95rem; margin-top: 5px;">
                            <?php echo $dest->getSpotsCount(); ?> featured destinations
                        </p>
                    </div>
                </div>

                <p class="service-description"><?php echo htmlspecialchars($dest->description); ?></p>

                <!-- Image container (populated by script.js as before) -->
                <div class="destination-image-container" data-destination="<?php echo htmlspecialchars($dest->anchor); ?>">
                    <img class="destination-image loading-placeholder" alt="<?php echo htmlspecialchars($dest->altText); ?>" />
                    <div class="image-credit"></div>
                </div>

                <!-- Calls renderSpots() to output the full spots grid HTML -->
                <?php echo $dest->renderSpots(); ?>

            </article>
        </section>

        <?php endforeach; ?>

        <!-- More destinations grid (static cards — not part of the class) -->
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

        <!-- Final call to action -->
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

    <!-- JavaScript -->
    <script src="js/server.js"></script>
</body>
</html>
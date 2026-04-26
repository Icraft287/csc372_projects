<?php
/*
    File: contact.php
    Author: Isaac Crft
    Date: February 14, 2026
    Description: Contact page for T's Travel. Updated to process the
                 inquiry form server-side with PHP validation and INSERT
                 into the contacts table via PDO.
    AI help: Got help with form structure and two-column layout.
             Updated: Converted to PHP, form now INSERTs into contacts table.
*/

session_start();
require_once 'validate.php';
require_once 'db.php';

// =====================================================================
// FORM VALUES — defaults shown in inputs on first load
// =====================================================================
$values = [
    'name'        => '',
    'email'       => '',
    'phone'       => '',
    'service'     => '',
    'destination' => '',
    'dates'       => '',
    'travelers'   => '',
    'budget'      => '',
    'message'     => '',
];

$errors = [
    'name'    => '',
    'email'   => '',
    'phone'   => '',
    'service' => '',
    'message' => '',
];

$form_message = '';
$all_errors   = '';

$allowed_services = ['cruise','all-inclusive','group','custom','other'];
$allowed_budgets  = ['under-2000','2000-5000','5000-10000','over-10000',''];

// =====================================================================
// PRG: Read success message left by the POST redirect
// =====================================================================
if (isset($_SESSION['contact_success'])) {
    $form_message = $_SESSION['contact_success'];
    unset($_SESSION['contact_success']);
}

// =====================================================================
// FORM PROCESSING
// =====================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Collect all submitted values
    $values['name']        = trim($_POST['name']        ?? '');
    $values['email']       = trim($_POST['email']       ?? '');
    $values['phone']       = trim($_POST['phone']       ?? '');
    $values['service']     = trim($_POST['service']     ?? '');
    $values['destination'] = trim($_POST['destination'] ?? '');
    $values['dates']       = trim($_POST['dates']       ?? '');
    $values['travelers']   = trim($_POST['travelers']   ?? '');
    $values['budget']      = trim($_POST['budget']      ?? '');
    $values['message']     = trim($_POST['message']     ?? '');

    // Validate required fields
    if (!is_valid_text($values['name'], 2, 100))
        $errors['name'] = 'Name must be between 2 and 100 characters.';

    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL))
        $errors['email'] = 'Please enter a valid email address.';

    if (!is_valid_text($values['phone'], 7, 20))
        $errors['phone'] = 'Please enter a valid phone number.';

    if (!is_valid_option($values['service'], $allowed_services))
        $errors['service'] = 'Please select a valid service type.';

    if (!is_valid_text($values['message'], 0, 2000) && $values['message'] !== '')
        $errors['message'] = 'Message must be under 2000 characters.';

    $all_errors = implode('', $errors);

    if ($all_errors === '') {
        // All valid — INSERT into contacts table using a prepared statement
        $stmt = $pdo->prepare(
            "INSERT INTO contacts
                (name, email, phone, service, destination, travel_dates, travelers, budget, message)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $values['name'],
            $values['email'],
            $values['phone'],
            $values['service'],
            $values['destination'],
            $values['dates'],
            ($values['travelers'] !== '' && is_numeric($values['travelers']))
                ? (int)$values['travelers'] : null,
            $values['budget'] !== '' ? $values['budget'] : null,
            $values['message'],
        ]);

        // Store success message in session and redirect (PRG pattern)
        $_SESSION['contact_success'] = 'Thank you, ' . htmlspecialchars($values['name'])
            . '! Your inquiry has been received. We will get back to you within 24 hours.';

        header('Location: contact.php');
        exit;
    } else {
        $form_message = 'Please correct the errors below and try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Contact T's Travel - Get in touch to start planning your dream vacation today">
    <title>Contact Us - T's Travel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="logo">
            <div class="logo-icon" aria-hidden="true">✈️</div>
            <span>T's Travel</span>
        </a>
        <div class="hamburger" aria-label="Toggle navigation menu" role="button" tabindex="0">
            <span></span><span></span><span></span>
        </div>
        <div class="nav-links">
            <a href="index.php" class="nav-link">Home</a>
            <a href="about.php" class="nav-link">About</a>
            <a href="services.php" class="nav-link">Services</a>
            <a href="destinations.php" class="nav-link">Destinations</a>
            <a href="contact.php" class="contact-btn active">Contact Us</a>
        </div>
    </nav>

    <header class="hero">
        <div class="hero-content">
            <h1>Get in Touch</h1>
            <p>Let's start planning your perfect vacation</p>
        </div>
    </header>

    <main>

        <!-- Success message (shown after PRG redirect) -->
        <?php if ($form_message !== '' && $all_errors === ''): ?>
            <section class="content-section" style="padding-bottom:0;">
                <p class="form-message success" style="max-width:800px;margin:0 auto;">
                    <?= htmlspecialchars($form_message) ?>
                </p>
            </section>
        <?php endif; ?>

        <section class="contact-layout">
            <!-- Contact info -->
            <div class="contact-info">
                <h2>Contact Information</h2>
                <p style="margin-bottom:30px;line-height:1.8;">We're here to help you plan the vacation of your dreams. Reach out using any method below and we'll respond promptly.</p>
                <div class="info-item">
                    <div class="info-icon" aria-hidden="true">📞</div>
                    <div>
                        <strong style="display:block;margin-bottom:5px;">Phone</strong>
                        <a href="tel:5551234567" style="color:white;text-decoration:none;">(555) 123-4567</a><br>
                        <span style="font-size:0.9rem;opacity:0.8;">Monday - Saturday, 9 AM - 6 PM</span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon" aria-hidden="true">📧</div>
                    <div>
                        <strong style="display:block;margin-bottom:5px;">Email</strong>
                        <a href="mailto:info@tstravel.com" style="color:white;text-decoration:none;">info@tstravel.com</a><br>
                        <span style="font-size:0.9rem;opacity:0.8;">We respond within 24 hours</span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon" aria-hidden="true">📍</div>
                    <div>
                        <strong style="display:block;margin-bottom:5px;">Office Location</strong>
                        <span>123 Travel Lane, Suite 456</span><br>
                        <span>Wanderlust City, ST 12345</span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon" aria-hidden="true">⏰</div>
                    <div>
                        <strong style="display:block;margin-bottom:5px;">Business Hours</strong>
                        <span>Monday - Friday: 9:00 AM - 6:00 PM</span><br>
                        <span>Saturday: 10:00 AM - 3:00 PM</span><br>
                        <span>Sunday: Closed</span>
                    </div>
                </div>
            </div>

            <!-- Contact form — now processed by PHP and INSERTs into contacts table -->
            <div class="inquiry-form">
                <h2>Travel Inquiry Form</h2>
                <p style="color:var(--text-dark);margin-bottom:25px;">Tell us about your dream vacation and we'll get back to you with personalized recommendations.</p>

                <?php if ($form_message !== '' && $all_errors !== ''): ?>
                    <p class="form-message error"><?= htmlspecialchars($form_message) ?></p>
                <?php endif; ?>

                <form action="contact.php" method="POST" novalidate id="contact-form">

                    <div class="form-group <?= $errors['name'] ? 'has-error' : '' ?>">
                        <label for="name">Your Name *</label>
                        <input type="text" id="name" name="name" required
                            value="<?= htmlspecialchars($values['name']) ?>"
                            placeholder="John Smith" aria-required="true">
                        <?php if ($errors['name']): ?>
                            <span class="error-msg"><?= $errors['name'] ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group <?= $errors['email'] ? 'has-error' : '' ?>">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required
                            value="<?= htmlspecialchars($values['email']) ?>"
                            placeholder="john@example.com" aria-required="true">
                        <?php if ($errors['email']): ?>
                            <span class="error-msg"><?= $errors['email'] ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group <?= $errors['phone'] ? 'has-error' : '' ?>">
                        <label for="phone">Phone Number *</label>
                        <input type="tel" id="phone" name="phone" required
                            value="<?= htmlspecialchars($values['phone']) ?>"
                            placeholder="(555) 123-4567" aria-required="true">
                        <?php if ($errors['phone']): ?>
                            <span class="error-msg"><?= $errors['phone'] ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group <?= $errors['service'] ? 'has-error' : '' ?>">
                        <label for="service">Type of Service *</label>
                        <select id="service" name="service" required aria-required="true">
                            <option value="">Select a service...</option>
                            <?php foreach (['cruise'=>'Cruise Vacation','all-inclusive'=>'All-Inclusive Resort','group'=>'Group Travel','custom'=>'Custom Trip Planning','other'=>'Other'] as $val => $label): ?>
                                <option value="<?= $val ?>" <?= ($values['service'] === $val) ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($errors['service']): ?>
                            <span class="error-msg"><?= $errors['service'] ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="destination">Preferred Destination</label>
                        <input type="text" id="destination" name="destination"
                            value="<?= htmlspecialchars($values['destination']) ?>"
                            placeholder="e.g., Caribbean, Europe, Asia">
                    </div>

                    <div class="form-group">
                        <label for="dates">Preferred Travel Dates</label>
                        <input type="text" id="dates" name="dates"
                            value="<?= htmlspecialchars($values['dates']) ?>"
                            placeholder="e.g., June 2026">
                    </div>

                    <div class="form-group">
                        <label for="travelers">Number of Travelers</label>
                        <input type="number" id="travelers" name="travelers" min="1"
                            value="<?= htmlspecialchars($values['travelers']) ?>"
                            placeholder="2">
                    </div>

                    <div class="form-group">
                        <label for="budget">Approximate Budget (Optional)</label>
                        <select id="budget" name="budget">
                            <option value="">Select budget range...</option>
                            <?php foreach (['under-2000'=>'Under $2,000','2000-5000'=>'$2,000 - $5,000','5000-10000'=>'$5,000 - $10,000','over-10000'=>'Over $10,000'] as $val => $label): ?>
                                <option value="<?= $val ?>" <?= ($values['budget'] === $val) ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group <?= $errors['message'] ? 'has-error' : '' ?>">
                        <label for="message">Additional Details</label>
                        <textarea id="message" name="message"
                            placeholder="Tell us about your travel preferences, special requests, or any questions..."><?= htmlspecialchars($values['message']) ?></textarea>
                        <?php if ($errors['message']): ?>
                            <span class="error-msg"><?= $errors['message'] ?></span>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="submit-btn">Submit Inquiry</button>
                </form>
            </div>
        </section>

        <section class="content-section" style="text-align:center;background:var(--warm-sand);padding:60px 40px;">
            <h2 style="font-family:'Playfair Display',serif;font-size:2rem;color:var(--primary-blue);margin-bottom:15px;">Prefer to Talk First?</h2>
            <p style="font-size:1.1rem;color:var(--text-dark);margin-bottom:25px;">Give us a call and let's discuss your travel plans over the phone</p>
            <a href="tel:5551234567" class="cta-button">📞 Call Now: (555) 123-4567</a>
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
            <div class="social-links">
                <a href="#" class="social-link" aria-label="Facebook">Facebook</a>
                <a href="#" class="social-link" aria-label="Instagram">Instagram</a>
                <a href="#" class="social-link" aria-label="Twitter">Twitter</a>
            </div>
        </div>
    </footer>

    <script src="js/server.js"></script>
</body>
</html>

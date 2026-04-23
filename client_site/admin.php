<?php
/*
    File: admin.php
    Author: Isaac Crft
    Date: March 25, 2026
    Description: Admin page for T's Travel — allows INSERT, UPDATE, and DELETE
                 operations on the trips table via a simple web interface.
                 All user input is validated and sanitized before use.
                 Prepared statements with placeholders prevent SQL injection.
                 Claude AI was used to assist with comments and structure.
*/

// =====================================================================
// AUTHENTICATION GUARD
// session_start() must run before any output.
// If $_SESSION['admin_logged_in'] is not set, the visitor is not logged
// in — redirect them to login.php immediately before any page renders.
// This prevents unauthorised access to all INSERT/UPDATE/DELETE operations.
// =====================================================================
session_start();

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// =====================================================================
// INCLUDES
// =====================================================================
require_once 'db.php';
require_once 'validate.php';

// Allowed trip types — used to validate the trip_type select input
$allowed_types = ['adventure', 'relaxation', 'cultural', 'family'];

// =====================================================================
// FEEDBACK MESSAGE
// Set after any INSERT / UPDATE / DELETE operation and displayed in HTML.
// =====================================================================
$feedback      = '';
$feedback_type = ''; // 'success' or 'error'

// =====================================================================
// HANDLE POST ACTIONS
// A hidden input named "action" tells us which operation to perform.
// All inputs are validated before any SQL runs.
// =====================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    // ==================================================================
    // INSERT — add a new trip package
    // ==================================================================
    if ($action === 'insert') {

        // Collect and trim all inputs
        $new_name        = trim($_POST['new_name']        ?? '');
        $new_type        = trim($_POST['new_type']        ?? '');
        $new_desc        = trim($_POST['new_desc']        ?? '');
        $new_price       = trim($_POST['new_price']       ?? '');
        $new_max         = trim($_POST['new_max']         ?? '');

        // Validate each field before touching the database
        $insert_errors = [];

        if (!is_valid_text($new_name, 2, 100)) {
            $insert_errors[] = 'Trip name must be between 2 and 100 characters.';
        }
        if (!is_valid_option($new_type, $allowed_types)) {
            $insert_errors[] = 'Please select a valid trip type.';
        }
        if (!is_valid_text($new_desc, 10, 1000)) {
            $insert_errors[] = 'Description must be between 10 and 1000 characters.';
        }
        if (!is_numeric($new_price) || (float)$new_price <= 0) {
            $insert_errors[] = 'Price must be a positive number.';
        }
        if (!is_valid_number($new_max, 1, 100)) {
            $insert_errors[] = 'Max travelers must be a whole number between 1 and 100.';
        }

        if (!empty($insert_errors)) {
            $feedback      = implode(' ', $insert_errors);
            $feedback_type = 'error';
        } else {
            // All inputs valid — run the INSERT with a prepared statement.
            // Each ? placeholder is bound to the matching value in execute().
            $sql  = "INSERT INTO trips (trip_name, trip_type, description, price_per_person, max_travelers)
                     VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $new_name,
                $new_type,
                $new_desc,
                (float)$new_price,
                (int)$new_max,
            ]);

            $feedback      = 'Trip package "' . htmlspecialchars($new_name) . '" was added successfully.';
            $feedback_type = 'success';
        }
    }

    // ==================================================================
    // UPDATE — modify an existing trip package
    // ==================================================================
    elseif ($action === 'update') {

        $upd_id    = trim($_POST['upd_id']    ?? '');
        $upd_name  = trim($_POST['upd_name']  ?? '');
        $upd_type  = trim($_POST['upd_type']  ?? '');
        $upd_desc  = trim($_POST['upd_desc']  ?? '');
        $upd_price = trim($_POST['upd_price'] ?? '');
        $upd_max   = trim($_POST['upd_max']   ?? '');

        // Validate each field
        $update_errors = [];

        if (!ctype_digit($upd_id) || (int)$upd_id < 1) {
            $update_errors[] = 'Invalid trip ID for update.';
        }
        if (!is_valid_text($upd_name, 2, 100)) {
            $update_errors[] = 'Trip name must be between 2 and 100 characters.';
        }
        if (!is_valid_option($upd_type, $allowed_types)) {
            $update_errors[] = 'Please select a valid trip type.';
        }
        if (!is_valid_text($upd_desc, 10, 1000)) {
            $update_errors[] = 'Description must be between 10 and 1000 characters.';
        }
        if (!is_numeric($upd_price) || (float)$upd_price <= 0) {
            $update_errors[] = 'Price must be a positive number.';
        }
        if (!is_valid_number($upd_max, 1, 100)) {
            $update_errors[] = 'Max travelers must be a whole number between 1 and 100.';
        }

        if (!empty($update_errors)) {
            $feedback      = implode(' ', $update_errors);
            $feedback_type = 'error';
        } else {
            // Prepared UPDATE statement — SET the new values WHERE trip_id matches.
            $sql  = "UPDATE trips
                     SET trip_name = ?, trip_type = ?, description = ?,
                         price_per_person = ?, max_travelers = ?
                     WHERE trip_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $upd_name,
                $upd_type,
                $upd_desc,
                (float)$upd_price,
                (int)$upd_max,
                (int)$upd_id,
            ]);

            $feedback      = 'Trip package ID ' . (int)$upd_id . ' was updated successfully.';
            $feedback_type = 'success';
        }
    }

    // ==================================================================
    // DELETE — remove a trip package
    // ==================================================================
    elseif ($action === 'delete') {

        $del_id = trim($_POST['del_id'] ?? '');

        // Validate: must be a positive integer that exists in the database
        if (!ctype_digit($del_id) || (int)$del_id < 1) {
            $feedback      = 'Invalid trip ID for deletion.';
            $feedback_type = 'error';
        } else {
            // Prepared DELETE — WHERE trip_id = ? prevents deleting the wrong row
            $sql  = "DELETE FROM trips WHERE trip_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([(int)$del_id]);

            // rowCount() tells us how many rows were actually deleted
            if ($stmt->rowCount() > 0) {
                $feedback      = 'Trip package ID ' . (int)$del_id . ' was deleted successfully.';
                $feedback_type = 'success';
            } else {
                $feedback      = 'No trip found with ID ' . (int)$del_id . '. Nothing was deleted.';
                $feedback_type = 'error';
            }
        }
    }
}

// =====================================================================
// FETCH ALL TRIPS for the current trips table display and update form.
// Runs after any POST so the table always shows the latest data.
// =====================================================================
$all_trips = $pdo->query("SELECT * FROM trips ORDER BY trip_id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - T's Travel</title>
    <!-- Preconnect to Google Fonts servers to reduce DNS lookup time -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!--
        display=swap tells the browser to render text in a fallback font immediately
        while Playfair Display and Lato load in the background.
        This prevents the page from being blank while waiting for fonts.
    -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <nav class="navbar">
        <a href="index.html" class="logo">
            <div class="logo-icon" aria-hidden="true">✈️</div>
            <span>T's Travel</span>
        </a>
        <div class="nav-links">
            <a href="index.html" class="nav-link">Home</a>
            <a href="destinations.php" class="nav-link">Destinations</a>
            <!-- Logout link clears the admin session and redirects to login.php -->
            <a href="logout.php" class="nav-link" style="color: #ffaaaa;">Log Out</a>
            <a href="contact.html" class="contact-btn">Contact Us</a>
        </div>
    </nav>

    <header class="hero">
        <div class="hero-content">
            <h1>⚙️ Trip Package Admin</h1>
            <p>Add, edit, and remove trip packages from the database</p>
        </div>
    </header>

    <main>
        <div class="admin-wrapper">

            <!-- ======================================================
                 FEEDBACK MESSAGE
                 Shown after any INSERT / UPDATE / DELETE operation.
                 The message was set in PHP above — escaped before output.
            ====================================================== -->
            <?php if ($feedback !== ''): ?>
                <p class="form-message <?= $feedback_type ?>">
                    <?= htmlspecialchars($feedback) ?>
                </p>
            <?php endif; ?>

            <!-- ======================================================
                 CURRENT TRIPS TABLE
                 Displays all rows currently in the trips table.
                 All values escaped with htmlspecialchars() before output.
            ====================================================== -->
            <section class="admin-section">
                <h2>Current Trip Packages</h2>

                <?php if (empty($all_trips)): ?>
                    <p>No trips in the database yet. Use the form below to add one.</p>
                <?php else: ?>
                    <div class="admin-table-wrapper">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Trip Name</th>
                                    <th>Type</th>
                                    <th>Price/Person</th>
                                    <th>Max Travelers</th>
                                    <th>View</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($all_trips as $t): ?>
                                    <tr>
                                        <td><?= (int)$t['trip_id'] ?></td>
                                        <td><?= htmlspecialchars($t['trip_name']) ?></td>
                                        <td><?= htmlspecialchars(ucfirst($t['trip_type'])) ?></td>
                                        <td>$<?= number_format((float)$t['price_per_person'], 2) ?></td>
                                        <td><?= (int)$t['max_travelers'] ?></td>
                                        <td>
                                            <!-- Link uses query string — demonstrates dynamic pages -->
                                            <a href="trip.php?id=<?= (int)$t['trip_id'] ?>" class="admin-view-link">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <!-- ======================================================
                 INSERT FORM — Add a new trip
                 action="insert" tells the POST handler which block to run.
                 All inputs validated server-side before the SQL executes.
            ====================================================== -->
            <section class="admin-section">
                <h2>➕ Add New Trip Package</h2>
                <form action="admin.php" method="POST" class="admin-form" novalidate>
                    <input type="hidden" name="action" value="insert">

                    <div class="form-group">
                        <label for="new_name">Trip Name:</label>
                        <input type="text" id="new_name" name="new_name"
                            placeholder="e.g. Caribbean Paradise" maxlength="100">
                    </div>

                    <div class="form-group">
                        <label for="new_type">Trip Type:</label>
                        <select id="new_type" name="new_type">
                            <option value="">-- Select Type --</option>
                            <option value="adventure">Adventure</option>
                            <option value="relaxation">Relaxation</option>
                            <option value="cultural">Cultural</option>
                            <option value="family">Family</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="new_desc">Description:</label>
                        <textarea id="new_desc" name="new_desc"
                            placeholder="Describe this trip package..." rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="new_price">Price Per Person ($):</label>
                        <input type="number" id="new_price" name="new_price"
                            placeholder="e.g. 1299.99" step="0.01" min="1">
                    </div>

                    <div class="form-group">
                        <label for="new_max">Max Travelers:</label>
                        <input type="number" id="new_max" name="new_max"
                            placeholder="e.g. 20" min="1" max="100">
                    </div>

                    <button type="submit" class="cta-button">Add Trip</button>
                </form>
            </section>

            <!-- ======================================================
                 UPDATE FORM — Edit an existing trip
                 The trip ID field tells the UPDATE query which row to change.
                 Pre-filled with first trip's values if trips exist.
            ====================================================== -->
            <section class="admin-section">
                <h2>✏️ Update Existing Trip Package</h2>
                <form action="admin.php" method="POST" class="admin-form" novalidate>
                    <input type="hidden" name="action" value="update">

                    <div class="form-group">
                        <label for="upd_id">Trip ID to Update:</label>
                        <input type="number" id="upd_id" name="upd_id"
                            placeholder="Enter the ID from the table above" min="1">
                    </div>

                    <div class="form-group">
                        <label for="upd_name">New Trip Name:</label>
                        <input type="text" id="upd_name" name="upd_name"
                            placeholder="e.g. Caribbean Paradise" maxlength="100">
                    </div>

                    <div class="form-group">
                        <label for="upd_type">New Trip Type:</label>
                        <select id="upd_type" name="upd_type">
                            <option value="">-- Select Type --</option>
                            <option value="adventure">Adventure</option>
                            <option value="relaxation">Relaxation</option>
                            <option value="cultural">Cultural</option>
                            <option value="family">Family</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="upd_desc">New Description:</label>
                        <textarea id="upd_desc" name="upd_desc"
                            placeholder="Updated description..." rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="upd_price">New Price Per Person ($):</label>
                        <input type="number" id="upd_price" name="upd_price"
                            placeholder="e.g. 1499.99" step="0.01" min="1">
                    </div>

                    <div class="form-group">
                        <label for="upd_max">New Max Travelers:</label>
                        <input type="number" id="upd_max" name="upd_max"
                            placeholder="e.g. 15" min="1" max="100">
                    </div>

                    <button type="submit" class="cta-button" style="background: var(--accent-teal);">Update Trip</button>
                </form>
            </section>

            <!-- ======================================================
                 DELETE FORM — Remove a trip
                 Only requires the trip ID. rowCount() confirms deletion.
            ====================================================== -->
            <section class="admin-section">
                <h2>🗑️ Delete Trip Package</h2>
                <p style="color: #721c24; margin-bottom: 15px;">
                    ⚠️ This permanently removes the trip from the database.
                </p>
                <form action="admin.php" method="POST" class="admin-form" novalidate
                      onsubmit="return confirm('Are you sure you want to delete this trip? This cannot be undone.');">
                    <input type="hidden" name="action" value="delete">

                    <div class="form-group">
                        <label for="del_id">Trip ID to Delete:</label>
                        <input type="number" id="del_id" name="del_id"
                            placeholder="Enter the ID from the table above" min="1">
                    </div>

                    <button type="submit" class="cta-button" style="background: #cc0000;">Delete Trip</button>
                </form>
            </section>

        </div><!-- end .admin-wrapper -->
    </main>

    <footer class="footer">
        <nav class="footer-links" aria-label="Footer navigation">
            <a href="index.html" class="footer-link">Home</a>
            <a href="destinations.php" class="footer-link">Destinations</a>
            <a href="contact.html" class="footer-link">Contact</a>
        </nav>
        <div class="footer-bottom">
            <p>&copy; 2026 T's Travel. All rights reserved.</p>
        </div>
    </footer>

    <script src="js/server.js"></script>
</body>
</html>

<?php
/*
    File: admin.php
    Author: Isaac Crft
    Date: April 05, 2026
    Description: Admin panel for T's Travel with fixed INSERT behavior.
*/

// =====================================================================
// INCLUDES
// =====================================================================
require_once 'db.php';      // PDO database connection stored in $pdo
require_once 'validate.php'; // Custom validation helper functions

// Whitelist of accepted trip types used for validation
$allowed_types = ['adventure', 'relaxation', 'cultural', 'family'];

// Feedback message and its type (success or error) shown after form submission
$feedback      = '';
$feedback_type = '';

// =====================================================================
// HANDLE POST ACTIONS
// =====================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Determine which form was submitted (insert, update, or delete)
    $action = $_POST['action'] ?? '';

    // -----------------------------------------------------------------
    // INSERT – Add a new trip package to the database
    // -----------------------------------------------------------------
    if ($action === 'insert') {

        // Sanitize all incoming POST fields by trimming whitespace
        $new_name   = trim($_POST['new_name']   ?? '');
        $new_type   = trim($_POST['new_type']   ?? '');
        $new_desc   = trim($_POST['new_desc']   ?? '');
        $new_price  = trim($_POST['new_price']  ?? '');
        $new_max    = trim($_POST['new_max']    ?? '');

        $insert_errors = [];

        // Validate each field using helper functions from validate.php
        if (!is_valid_text($new_name, 2, 100)) $insert_errors[] = 'Trip name must be 2-100 characters.';
        if (!is_valid_option($new_type, $allowed_types)) $insert_errors[] = 'Please select a valid trip type.';
        if (!is_valid_text($new_desc, 10, 1000)) $insert_errors[] = 'Description must be 10-1000 characters.';
        if (!is_numeric($new_price) || (float)$new_price <= 0) $insert_errors[] = 'Price must be a positive number.';
        if (!is_valid_number($new_max, 1, 100)) $insert_errors[] = 'Max travelers must be 1-100.';

        if (!empty($insert_errors)) {
            // Combine all validation errors into one feedback message
            $feedback = implode(' ', $insert_errors);
            $feedback_type = 'error';
        } else {
            try {
                // Prepared statement prevents SQL injection
                $sql  = "INSERT INTO trips (trip_name, trip_type, description, price_per_person, max_travelers)
                         VALUES (?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $new_name,
                    $new_type,
                    $new_desc,
                    (float)$new_price,  // Cast to float for decimal storage
                    (int)$new_max       // Cast to int for whole-number storage
                ]);

                if ($stmt->rowCount() > 0) {
                    // Retrieve the auto-incremented ID of the newly inserted row
                    $new_id = $pdo->lastInsertId();
                    $feedback = 'Trip package "' . htmlspecialchars($new_name) . '" added successfully! (ID: ' . $new_id . ')';
                    $feedback_type = 'success';
                } else {
                    $feedback = 'Insert executed but no row was added.';
                    $feedback_type = 'error';
                }
            } catch (PDOException $e) {
                // Catch and display any database-level errors safely
                $feedback = 'Database error: ' . htmlspecialchars($e->getMessage());
                $feedback_type = 'error';
            }
        }
    }

    // UPDATE (unchanged)
    // -----------------------------------------------------------------
    // UPDATE – Modify an existing trip package by ID
    // -----------------------------------------------------------------
    elseif ($action === 'update') {
        // Sanitize all incoming POST fields
        $upd_id    = trim($_POST['upd_id'] ?? '');
        $upd_name  = trim($_POST['upd_name'] ?? '');
        $upd_type  = trim($_POST['upd_type'] ?? '');
        $upd_desc  = trim($_POST['upd_desc'] ?? '');
        $upd_price = trim($_POST['upd_price'] ?? '');
        $upd_max   = trim($_POST['upd_max'] ?? '');

        $update_errors = [];
        // ctype_digit ensures the ID is a positive integer string (no decimals, no negatives)
        if (!ctype_digit($upd_id) || (int)$upd_id < 1) $update_errors[] = 'Invalid trip ID.';
        if (!is_valid_text($upd_name, 2, 100)) $update_errors[] = 'Invalid name.';
        if (!is_valid_option($upd_type, $allowed_types)) $update_errors[] = 'Invalid type.';
        if (!is_valid_text($upd_desc, 10, 1000)) $update_errors[] = 'Invalid description.';
        if (!is_numeric($upd_price) || (float)$upd_price <= 0) $update_errors[] = 'Invalid price.';
        if (!is_valid_number($upd_max, 1, 100)) $update_errors[] = 'Invalid max travelers.';

        if (!empty($update_errors)) {
            $feedback = implode(' ', $update_errors);
            $feedback_type = 'error';
        } else {
            try {
                // Update all editable columns for the matching trip_id
                $sql = "UPDATE trips SET trip_name=?, trip_type=?, description=?, price_per_person=?, max_travelers=? WHERE trip_id=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$upd_name, $upd_type, $upd_desc, (float)$upd_price, (int)$upd_max, (int)$upd_id]);

                // rowCount() returns 0 if the values were identical or ID wasn't found
                $feedback = $stmt->rowCount() > 0 ? 'Trip ID ' . (int)$upd_id . ' updated successfully.' : 'No changes made or ID not found.';
                $feedback_type = $stmt->rowCount() > 0 ? 'success' : 'error';
            } catch (PDOException $e) {
                $feedback = 'Update error: ' . htmlspecialchars($e->getMessage());
                $feedback_type = 'error';
            }
        }
    }

    // DELETE (unchanged)
    // -----------------------------------------------------------------
    // DELETE – Permanently remove a trip package by ID
    // -----------------------------------------------------------------
    elseif ($action === 'delete') {
        $del_id = trim($_POST['del_id'] ?? '');
        // Validate that the ID is a positive integer before attempting deletion
        if (!ctype_digit($del_id) || (int)$del_id < 1) {
            $feedback = 'Invalid trip ID for deletion.';
            $feedback_type = 'error';
        } else {
            try {
                $sql = "DELETE FROM trips WHERE trip_id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([(int)$del_id]);

                // Confirm whether a row was actually removed
                $feedback = $stmt->rowCount() > 0 
                    ? 'Trip ID ' . (int)$del_id . ' deleted successfully.' 
                    : 'No trip found with that ID.';
                $feedback_type = $stmt->rowCount() > 0 ? 'success' : 'error';
            } catch (PDOException $e) {
                $feedback = 'Delete error: ' . htmlspecialchars($e->getMessage());
                $feedback_type = 'error';
            }
        }
    }
}

// =====================================================================
// ALWAYS fetch fresh data after any POST
// =====================================================================
// Re-query the full trips table so the display reflects the latest state
$all_trips = $pdo->query("SELECT * FROM trips ORDER BY trip_id ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - T's Travel</title>
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
            <a href="contact.html" class="contact-btn">Contact Us</a>
        </div>
    </nav>

    <header class="hero">
        <div class="hero-content">
            <h1>⚙️ Trip Package Admin</h1>
            <p>Add, edit, and remove trip packages</p>
        </div>
    </header>

    <main>
        <div class="admin-wrapper">

            <?php if ($feedback !== ''): ?>
                <!-- Display success or error message after form submission -->
                <p class="form-message <?= $feedback_type ?>">
                    <?= htmlspecialchars($feedback) ?>
                </p>
            <?php endif; ?>

            <!-- Current trips table – pulled fresh from the database on every load -->
            <section class="admin-section">
                <h2>Current Trip Packages (<?= count($all_trips) ?> total)</h2>
                <?php if (empty($all_trips)): ?>
                    <p>No trips in the database yet.</p>
                <?php else: ?>
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
                                    <!-- Cast and escape all output to prevent XSS -->
                                    <td><?= (int)$t['trip_id'] ?></td>
                                    <td><?= htmlspecialchars($t['trip_name']) ?></td>
                                    <td><?= htmlspecialchars(ucfirst($t['trip_type'])) ?></td>
                                    <td>$<?= number_format((float)$t['price_per_person'], 2) ?></td>
                                    <td><?= (int)$t['max_travelers'] ?></td>
                                    <td><a href="trip.php?id=<?= (int)$t['trip_id'] ?>" class="admin-view-link">View</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>

            <!-- INSERT FORM -->
            <section class="admin-section">
                <h2>➕ Add New Trip Package</h2>
                <!-- Hidden action field tells the PHP handler which operation to run -->
                <form action="admin.php" method="POST" novalidate>
                    <input type="hidden" name="action" value="insert">
                    <div class="form-group">
                        <label for="new_name">Trip Name:</label>
                        <input type="text" id="new_name" name="new_name" placeholder="e.g. French Riviera" maxlength="100" required>
                    </div>
                    <div class="form-group">
                        <label for="new_type">Trip Type:</label>
                        <select id="new_type" name="new_type" required>
                            <option value="">-- Select Type --</option>
                            <option value="adventure">Adventure</option>
                            <option value="relaxation">Relaxation</option>
                            <option value="cultural">Cultural</option>
                            <option value="family">Family</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="new_desc">Description:</label>
                        <textarea id="new_desc" name="new_desc" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="new_price">Price Per Person ($):</label>
                        <input type="number" id="new_price" name="new_price" step="0.01" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="new_max">Max Travelers:</label>
                        <input type="number" id="new_max" name="new_max" min="1" max="100" required>
                    </div>
                    <button type="submit" class="cta-button">Add Trip</button>
                </form>
            </section>

            <!-- UPDATE FORM (shortened for space - copy from previous if needed) -->
            <!-- DELETE FORM (same) -->
             <!-- UPDATE FORM -->
            <section class="admin-section">
                <h2>✏️ Update Existing Trip Package</h2>
                <form action="admin.php" method="POST" class="admin-form" novalidate>
                    <input type="hidden" name="action" value="update">

                    <div class="form-group">
                        <label for="upd_id">Trip ID to Update:</label>
                        <!-- User must reference the ID from the table above -->
                        <input type="number" id="upd_id" name="upd_id"
                            placeholder="Enter the ID from the table above" min="1" required>
                    </div>

                    <div class="form-group">
                        <label for="upd_name">New Trip Name:</label>
                        <input type="text" id="upd_name" name="upd_name"
                            placeholder="e.g. Caribbean Paradise" maxlength="100" required>
                    </div>

                    <div class="form-group">
                        <label for="upd_type">New Trip Type:</label>
                        <select id="upd_type" name="upd_type" required>
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
                            placeholder="Updated description..." rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="upd_price">New Price Per Person ($):</label>
                        <input type="number" id="upd_price" name="upd_price"
                            placeholder="e.g. 1499.99" step="0.01" min="1" required>
                    </div>

                    <div class="form-group">
                        <label for="upd_max">New Max Travelers:</label>
                        <input type="number" id="upd_max" name="upd_max"
                            placeholder="e.g. 15" min="1" max="100" required>
                    </div>

                    <button type="submit" class="cta-button" style="background: var(--accent-teal);">Update Trip</button>
                </form>
            </section>

            <!-- DELETE FORM -->
            <section class="admin-section">
                <h2>🗑️ Delete Trip Package</h2>
                <p style="color: #721c24; margin-bottom: 15px;">
                    ⚠️ This permanently removes the trip from the database.
                </p>
                <!-- onsubmit confirm dialog gives the admin a final chance to cancel -->
                <form action="admin.php" method="POST" class="admin-form" novalidate
                      onsubmit="return confirm('Are you sure you want to delete this trip? This cannot be undone.');">
                    <input type="hidden" name="action" value="delete">

                    <div class="form-group">
                        <label for="del_id">Trip ID to Delete:</label>
                        <input type="number" id="del_id" name="del_id"
                            placeholder="Enter the ID from the table above" min="1" required>
                    </div>

                    <button type="submit" class="cta-button" style="background: #cc0000;">Delete Trip</button>
                </form>
            </section>

        </div>
    </main>

    <footer class="footer">
        <p>&copy; 2026 T's Travel. All rights reserved.</p>
    </footer>

</body>
</html>
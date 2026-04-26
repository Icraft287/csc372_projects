<?php
/*
    File: admin.php
    Author: Isaac Crft
    Date: March 25, 2026
    Description: Admin panel for T's Travel. Handles INSERT, UPDATE, DELETE
                 on trips table. Protected by session auth.
                 CHANGE #5: ?edit=ID pre-populates the update form with the
                 current values from the DB so the admin doesn't retype everything.
*/

session_start();

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once 'db.php';
require_once 'validate.php';

$allowed_types = ['adventure', 'relaxation', 'cultural', 'family'];

$feedback      = '';
$feedback_type = '';

// =====================================================================
// HANDLE POST ACTIONS
// =====================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    // INSERT
    if ($action === 'insert') {
        $new_name  = trim($_POST['new_name']  ?? '');
        $new_type  = trim($_POST['new_type']  ?? '');
        $new_desc  = trim($_POST['new_desc']  ?? '');
        $new_price = trim($_POST['new_price'] ?? '');
        $new_max   = trim($_POST['new_max']   ?? '');

        $errs = [];
        if (!is_valid_text($new_name, 2, 100))         $errs[] = 'Trip name must be 2–100 characters.';
        if (!is_valid_option($new_type, $allowed_types))$errs[] = 'Please select a valid trip type.';
        if (!is_valid_text($new_desc, 10, 1000))        $errs[] = 'Description must be 10–1000 characters.';
        if (!is_numeric($new_price) || (float)$new_price <= 0) $errs[] = 'Price must be a positive number.';
        if (!is_valid_number($new_max, 1, 100))         $errs[] = 'Max travelers must be 1–100.';

        if (!empty($errs)) {
            $feedback = implode(' ', $errs); $feedback_type = 'error';
        } else {
            $stmt = $pdo->prepare("INSERT INTO trips (trip_name, trip_type, description, price_per_person, max_travelers) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$new_name, $new_type, $new_desc, (float)$new_price, (int)$new_max]);
            $feedback = 'Trip "' . htmlspecialchars($new_name) . '" added successfully.';
            $feedback_type = 'success';
        }
    }

    // UPDATE
    elseif ($action === 'update') {
        $upd_id    = trim($_POST['upd_id']    ?? '');
        $upd_name  = trim($_POST['upd_name']  ?? '');
        $upd_type  = trim($_POST['upd_type']  ?? '');
        $upd_desc  = trim($_POST['upd_desc']  ?? '');
        $upd_price = trim($_POST['upd_price'] ?? '');
        $upd_max   = trim($_POST['upd_max']   ?? '');

        $errs = [];
        if (!ctype_digit($upd_id) || (int)$upd_id < 1) $errs[] = 'Invalid trip ID.';
        if (!is_valid_text($upd_name, 2, 100))           $errs[] = 'Trip name must be 2–100 characters.';
        if (!is_valid_option($upd_type, $allowed_types)) $errs[] = 'Please select a valid trip type.';
        if (!is_valid_text($upd_desc, 10, 1000))         $errs[] = 'Description must be 10–1000 characters.';
        if (!is_numeric($upd_price) || (float)$upd_price <= 0) $errs[] = 'Price must be a positive number.';
        if (!is_valid_number($upd_max, 1, 100))          $errs[] = 'Max travelers must be 1–100.';

        if (!empty($errs)) {
            $feedback = implode(' ', $errs); $feedback_type = 'error';
        } else {
            $stmt = $pdo->prepare("UPDATE trips SET trip_name=?, trip_type=?, description=?, price_per_person=?, max_travelers=? WHERE trip_id=?");
            $stmt->execute([$upd_name, $upd_type, $upd_desc, (float)$upd_price, (int)$upd_max, (int)$upd_id]);
            $feedback = 'Trip ID ' . (int)$upd_id . ' updated successfully.';
            $feedback_type = 'success';
        }
    }

    // DELETE
    elseif ($action === 'delete') {
        $del_id = trim($_POST['del_id'] ?? '');
        if (!ctype_digit($del_id) || (int)$del_id < 1) {
            $feedback = 'Invalid trip ID for deletion.'; $feedback_type = 'error';
        } else {
            $stmt = $pdo->prepare("DELETE FROM trips WHERE trip_id = ?");
            $stmt->execute([(int)$del_id]);
            if ($stmt->rowCount() > 0) {
                $feedback = 'Trip ID ' . (int)$del_id . ' deleted.'; $feedback_type = 'success';
            } else {
                $feedback = 'No trip found with ID ' . (int)$del_id . '.'; $feedback_type = 'error';
            }
        }
    }
}

// =====================================================================
// CHANGE #5: Pre-populate update form when ?edit=ID is in the URL
// Fetch that trip's current values from the DB so the admin can see
// what's already there before making changes — no retyping needed.
// =====================================================================
$edit_trip = null;
$edit_id   = $_GET['edit'] ?? '';
if (ctype_digit($edit_id) && (int)$edit_id > 0) {
    $e_stmt   = $pdo->prepare("SELECT * FROM trips WHERE trip_id = ?");
    $e_stmt->execute([(int)$edit_id]);
    $edit_trip = $e_stmt->fetch();
}

// Fetch all trips for the table display
$all_trips = $pdo->query("SELECT * FROM trips ORDER BY trip_id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - T's Travel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css?v=2">
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="logo">
            <div class="logo-icon" aria-hidden="true">✈️</div>
            <span>T's Travel</span>
        </a>
        <div class="nav-links">
            <a href="index.php" class="nav-link">Home</a>
            <a href="destinations.php" class="nav-link">Destinations</a>
            <a href="logout.php" class="nav-link" style="color:#ffaaaa;">Log Out</a>
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

            <?php if ($feedback !== ''): ?>
                <p class="form-message <?= $feedback_type ?>">
                    <?= htmlspecialchars($feedback) ?>
                </p>
            <?php endif; ?>

            <!-- CURRENT TRIPS TABLE -->
            <section class="admin-section">
                <h2>Current Trip Packages</h2>
                <?php if (empty($all_trips)): ?>
                    <p>No trips yet. Use the form below to add one.</p>
                <?php else: ?>
                    <div class="admin-table-wrapper">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Trip Name</th>
                                    <th>Type</th>
                                    <th>Price/Person</th>
                                    <th>Max</th>
                                    <th>View</th>
                                    <!--
                                        CHANGE #5: "Edit" link passes ?edit=ID to this same
                                        page — the PHP above fetches that row and pre-fills
                                        the update form below with its current values.
                                    -->
                                    <th>Edit</th>
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
                                        <td><a href="trip.php?id=<?= (int)$t['trip_id'] ?>" class="admin-view-link">View</a></td>
                                        <td><a href="admin.php?edit=<?= (int)$t['trip_id'] ?>" class="admin-view-link" style="color:var(--coral-accent);">Edit</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <!-- INSERT -->
            <section class="admin-section">
                <h2>➕ Add New Trip Package</h2>
                <form action="admin.php" method="POST" class="admin-form" novalidate>
                    <input type="hidden" name="action" value="insert">
                    <div class="form-group">
                        <label for="new_name">Trip Name:</label>
                        <input type="text" id="new_name" name="new_name" placeholder="e.g. Caribbean Paradise" maxlength="100">
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
                        <textarea id="new_desc" name="new_desc" placeholder="Describe this trip package..." rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="new_price">Price Per Person ($):</label>
                        <input type="number" id="new_price" name="new_price" placeholder="e.g. 1299.99" step="0.01" min="1">
                    </div>
                    <div class="form-group">
                        <label for="new_max">Max Travelers:</label>
                        <input type="number" id="new_max" name="new_max" placeholder="e.g. 20" min="1" max="100">
                    </div>
                    <button type="submit" class="cta-button">Add Trip</button>
                </form>
            </section>

            <!-- UPDATE — pre-populated when ?edit=ID is present (CHANGE #5) -->
            <section class="admin-section" id="update-section">
                <h2>✏️ Update Existing Trip Package</h2>
                <?php if ($edit_trip): ?>
                    <p style="color:var(--accent-teal);margin-bottom:15px;">
                        Pre-filled with current values for Trip ID <?= (int)$edit_trip['trip_id'] ?>.
                        Update any field and click Save.
                    </p>
                <?php else: ?>
                    <p style="margin-bottom:15px;color:#666;">
                        Click <strong>Edit</strong> on any row above to auto-fill this form,
                        or enter an ID and fill in the fields manually.
                        You can also use the <strong>fetch auto-fill</strong> below by entering an ID.
                    </p>
                <?php endif; ?>
                <form action="admin.php" method="POST" class="admin-form" novalidate>
                    <input type="hidden" name="action" value="update">
                    <div class="form-group">
                        <label for="upd_id">Trip ID to Update:</label>
                        <!--
                            data-autofill="true" tells server.js to watch this field
                            for the fetch auto-fill (CHANGE #14).
                        -->
                        <input type="number" id="upd_id" name="upd_id" min="1"
                            placeholder="Enter ID or click Edit above"
                            value="<?= $edit_trip ? (int)$edit_trip['trip_id'] : '' ?>"
                            data-autofill="true">
                    </div>
                    <div class="form-group">
                        <label for="upd_name">Trip Name:</label>
                        <input type="text" id="upd_name" name="upd_name" maxlength="100"
                            value="<?= $edit_trip ? htmlspecialchars($edit_trip['trip_name']) : '' ?>"
                            placeholder="e.g. Caribbean Paradise">
                    </div>
                    <div class="form-group">
                        <label for="upd_type">Trip Type:</label>
                        <select id="upd_type" name="upd_type">
                            <option value="">-- Select Type --</option>
                            <?php foreach (['adventure','relaxation','cultural','family'] as $t): ?>
                                <option value="<?= $t ?>"
                                    <?= ($edit_trip && $edit_trip['trip_type'] === $t) ? 'selected' : '' ?>>
                                    <?= ucfirst($t) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="upd_desc">Description:</label>
                        <textarea id="upd_desc" name="upd_desc" rows="3"
                            placeholder="Updated description..."><?= $edit_trip ? htmlspecialchars($edit_trip['description']) : '' ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="upd_price">Price Per Person ($):</label>
                        <input type="number" id="upd_price" name="upd_price" step="0.01" min="1"
                            value="<?= $edit_trip ? (float)$edit_trip['price_per_person'] : '' ?>"
                            placeholder="e.g. 1499.99">
                    </div>
                    <div class="form-group">
                        <label for="upd_max">Max Travelers:</label>
                        <input type="number" id="upd_max" name="upd_max" min="1" max="100"
                            value="<?= $edit_trip ? (int)$edit_trip['max_travelers'] : '' ?>"
                            placeholder="e.g. 15">
                    </div>
                    <button type="submit" class="cta-button" style="background:var(--accent-teal);">Save Changes</button>
                </form>
            </section>

            <!-- DELETE -->
            <section class="admin-section">
                <h2>🗑️ Delete Trip Package</h2>
                <p style="color:#721c24;margin-bottom:15px;">⚠️ This permanently removes the trip from the database.</p>
                <form action="admin.php" method="POST" class="admin-form" novalidate
                      onsubmit="return confirm('Delete this trip permanently?');">
                    <input type="hidden" name="action" value="delete">
                    <div class="form-group">
                        <label for="del_id">Trip ID to Delete:</label>
                        <input type="number" id="del_id" name="del_id" min="1" placeholder="Enter ID from table above">
                    </div>
                    <button type="submit" class="cta-button" style="background:#cc0000;">Delete Trip</button>
                </form>
            </section>

        </div>
    </main>

    <footer class="footer">
        <nav class="footer-links" aria-label="Footer navigation">
            <a href="index.php" class="footer-link">Home</a>
            <a href="destinations.php" class="footer-link">Destinations</a>
            <a href="contact.php" class="footer-link">Contact</a>
        </nav>
        <div class="footer-bottom"><p>&copy; 2026 T's Travel. All rights reserved.</p></div>
    </footer>

    <script src="js/server.js"></script>
</body>
</html>

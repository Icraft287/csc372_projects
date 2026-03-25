<?php
/*
    File: clear_session.php
    Author: Isaac Crft
    Date: March 25, 2026
    Description: Terminates the current visitor session for T's Travel.
                 Clears all $_SESSION data, destroys the session, and
                 expires the PHPSESSID cookie. Redirects back to destinations.php.
*/

// Must call session_start() before we can access or destroy a session
session_start();

// Step 1: Clear all data stored in the $_SESSION array
$_SESSION = [];

// Step 2: Expire the PHPSESSID cookie in the browser by setting its
// expiry time to one hour in the past. This tells the browser to delete it.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),     // "PHPSESSID"
        '',                 // empty value
        time() - 3600,      // expired one hour ago = browser deletes it
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Step 3: Destroy all session data stored on the server
session_destroy();

// Redirect the visitor back to destinations.php after clearing the session
header("Location: destinations.php");
exit;
?>
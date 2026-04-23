<?php
/*
    File: logout.php
    Author: Isaac Crft
    Date: March 25, 2026
    Description: Logs out the admin user by clearing the admin session flag
                 and redirecting back to login.php.
*/

session_start();

// Remove the admin authentication flag from the session
unset($_SESSION['admin_logged_in']);

// Redirect back to the login page
header('Location: login.php');
exit;
?>

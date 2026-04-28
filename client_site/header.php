<?php
/*
    File: header.php
    Author: Isaac Craft
    Date: March 25, 2026
    Description: Shared header include for T's Travel.
                 Set these variables before require_once'ing this file:
                   $page_title       — <title> tag content (required)
                   $meta_description — <meta name="description"> content (optional)
                   $active_page      — 'home'|'about'|'services'|'destinations'|'contact'
                                       |'admin'|'login' (optional, defaults to '')
                   $is_homepage      — set to true only on index.php for the logo variant
*/

$meta_description = $meta_description ?? '';
$active_page      = $active_page      ?? '';
$is_homepage      = $is_homepage      ?? false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if ($meta_description !== ''): ?>
    <meta name="description" content="<?= htmlspecialchars($meta_description) ?>">
    <?php endif; ?>
    <title><?= htmlspecialchars($page_title) ?></title>
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

    <nav class="navbar">
        <?php if ($is_homepage): ?>
        <a href="index.php" class="logo">
            <span style="font-size: 4rem; font-weight: 500;">T's Travel</span>
        </a>
        <!-- Client logo display section -->
        <div class="logo-section">
            <img src="images/client_logov4.png" alt="Client Logo" class="client-logo">
        </div>
        <?php else: ?>
        <a href="index.php" class="logo">
            <div class="logo-icon" aria-hidden="true">✈️</div>
            <span>T's Travel</span>
        </a>
        <?php endif; ?>

        <?php if ($active_page !== 'admin' && $active_page !== 'login'): ?>
        <!-- Hamburger menu toggle (mobile) -->
        <div class="hamburger" aria-label="Toggle navigation menu" role="button" tabindex="0">
            <span></span><span></span><span></span>
        </div>
        <div class="nav-links">
            <a href="index.php"        class="nav-link <?= $active_page === 'home'         ? 'active' : '' ?>">Home</a>
            <a href="about.php"        class="nav-link <?= $active_page === 'about'        ? 'active' : '' ?>">About</a>
            <a href="services.php"     class="nav-link <?= $active_page === 'services'     ? 'active' : '' ?>">Services</a>
            <a href="destinations.php" class="nav-link <?= $active_page === 'destinations' ? 'active' : '' ?>">Destinations</a>
            <a href="contact.php"      class="contact-btn <?= $active_page === 'contact'   ? 'active' : '' ?>">Contact Us</a>
        </div>
        <?php elseif ($active_page === 'login'): ?>
        <!-- Login nav — limited public links, no hamburger -->
        <div class="nav-links">
            <a href="index.php"        class="nav-link">Home</a>
            <a href="destinations.php" class="nav-link">Destinations</a>
            <a href="contact.php"      class="contact-btn">Contact Us</a>
        </div>
        <?php else: ?>
        <!-- Admin nav — no hamburger, limited links -->
        <div class="nav-links">
            <a href="index.php"        class="nav-link">Home</a>
            <a href="destinations.php" class="nav-link">Destinations</a>
            <a href="logout.php"       class="nav-link" style="color:#ffaaaa;">Log Out</a>
        </div>
        <?php endif; ?>
    </nav>
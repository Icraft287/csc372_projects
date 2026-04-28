<?php
/*
    File: index.php
    Author: Isaac Crft
    Date: February 14, 2026
    Description: Main homepage for T's Travel - travel planning services website.
                 Converted from index.html to PHP so the entire site is PHP.
    AI usage: Used AI for help linking CSS and JS files correctly, generating some layout ideas, 
              and getting unstuck on navigation and card styling. 
              Updated: Added Unsplash API integration for dynamic hero backgrounds.
              Updated: Converted to PHP, added Google Fonts performance fix.
              Updated: Extracted shared <head>, nav, and footer into header.php / footer.php.
*/

$page_title       = "T's Travel - Your Dream Vacation Awaits";
$meta_description = "T's Travel - Professional travel planning services for cruises, all-inclusive vacations, group travel, and custom trip planning";
$active_page      = 'home';
$is_homepage      = true;
require_once 'header.php';
?>

    <!-- Hero Section with Dynamic Unsplash Background -->
    <header class="hero" id="hero-section">
        <div class="hero-content">
            <h1>Your Dream Vacation Awaits</h1>
            <p>Personalized travel planning with expert guidance every step of the way</p>
            <a href="contact.php" class="cta-button">Plan Your Trip</a>
        </div>
        <!-- Photo attribution for Unsplash -->
        <div class="photo-credit" id="photo-credit" style="display: none;"></div>
    </header>

    <main>
        <!-- Services Section -->
        <section class="content-section">
            <h2 class="section-title">Our Services</h2>
            <div class="card-grid">
                <article class="card">
                    <div class="card-icon" aria-hidden="true">🚢</div>
                    <h3>Cruises</h3>
                    <p>Explore the world's oceans with carefully curated cruise experiences</p>
                </article>
                <article class="card">
                    <div class="card-icon" aria-hidden="true">🏖️</div>
                    <h3>All-Inclusive Vacations</h3>
                    <p>Stress-free getaways with everything included</p>
                </article>
                <article class="card">
                    <div class="card-icon" aria-hidden="true">👥</div>
                    <h3>Group Travel</h3>
                    <p>Perfect trips for families, friends, and organizations</p>
                </article>
                <article class="card">
                    <div class="card-icon" aria-hidden="true">✨</div>
                    <h3>Custom Planning</h3>
                    <p>Tailored itineraries designed just for you</p>
                </article>
            </div>
        </section>

        <!-- Popular Destinations Section -->
        <section class="content-section" style="background: var(--warm-sand);">
            <h2 class="section-title">Popular Destinations</h2>
            <div class="destination-grid">
                <a href="destinations.php#caribbean" class="destination-card">🌴 Caribbean</a>
                <a href="destinations.php#europe"    class="destination-card">🗼 Europe</a>
                <a href="destinations.php#cruises"   class="destination-card">🚢 Cruises</a>
                <a href="destinations.php#romantic"  class="destination-card">💑 Romantic Getaways</a>
            </div>
        </section>

        <!-- Call to Action Section -->
        <section class="content-section" style="background: var(--warm-sand); text-align: center;">
            <h2 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; color: var(--primary-blue); margin-bottom: 20px;">Ready to Start Planning?</h2>
            <p style="font-size: 1.2rem; color: var(--text-dark); margin-bottom: 30px; max-width: 700px; margin-left: auto; margin-right: auto;">Let us help you create unforgettable memories. Contact us today to begin planning your perfect vacation.</p>
            <a href="contact.php" class="cta-button">Get Started</a>
        </section>
    </main>

<?php require_once 'footer.php'; ?>

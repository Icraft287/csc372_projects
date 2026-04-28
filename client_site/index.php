<?php
/*
    File: index.php
    Author: Isaac Craft
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

        <!-- Stats Bar -->
        <section style="background: var(--deep-ocean); padding: 40px; text-align: center;">
            <div style="display: flex; justify-content: center; flex-wrap: wrap; gap: 50px; max-width: 900px; margin: 0 auto;">
                <div>
                    <p style="font-family: 'Playfair Display', serif; font-size: 2.5rem; color: var(--accent-teal); font-weight: 700; margin-bottom: 5px;">50+</p>
                    <p style="color: #c0c0c0; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.05em;">Trips Planned</p>
                </div>
                <div>
                    <p style="font-family: 'Playfair Display', serif; font-size: 2.5rem; color: var(--accent-teal); font-weight: 700; margin-bottom: 5px;">10+</p>
                    <p style="color: #c0c0c0; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.05em;">Years Experience</p>
                </div>
                <div>
                    <p style="font-family: 'Playfair Display', serif; font-size: 2.5rem; color: var(--accent-teal); font-weight: 700; margin-bottom: 5px;">98%</p>
                    <p style="color: #c0c0c0; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.05em;">Client Satisfaction</p>
                </div>
                <div>
                    <p style="font-family: 'Playfair Display', serif; font-size: 2.5rem; color: var(--accent-teal); font-weight: 700; margin-bottom: 5px;">20+</p>
                    <p style="color: #c0c0c0; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.05em;">Destinations</p>
                </div>
            </div>
        </section>

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

        <!-- How It Works Section -->
        <section class="content-section" style="background: var(--deep-ocean);">
            <h2 class="section-title">How It Works</h2>
            <div class="card-grid" style="max-width: 900px;">
                <article class="card" style="position: relative;">
                    <div style="position: absolute; top: -18px; left: 50%; transform: translateX(-50%); background: var(--accent-teal); color: white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.1rem;">1</div>
                    <div class="card-icon" aria-hidden="true">💬</div>
                    <h3>Tell Us Your Dream</h3>
                    <p>Fill out our travel inquiry form or give us a call. Share your destination wishes, travel dates, budget, and group size — the more detail the better.</p>
                </article>
                <article class="card" style="position: relative;">
                    <div style="position: absolute; top: -18px; left: 50%; transform: translateX(-50%); background: var(--accent-teal); color: white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.1rem;">2</div>
                    <div class="card-icon" aria-hidden="true">🗺️</div>
                    <h3>We Plan Everything</h3>
                    <p>Our experts research and build a personalised itinerary for you — flights, hotels, excursions, and more — then walk you through every detail.</p>
                </article>
                <article class="card" style="position: relative;">
                    <div style="position: absolute; top: -18px; left: 50%; transform: translateX(-50%); background: var(--accent-teal); color: white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.1rem;">3</div>
                    <div class="card-icon" aria-hidden="true">✈️</div>
                    <h3>You Travel Happy</h3>
                    <p>Pack your bags and go. We're available throughout your trip if you need us, so you can relax knowing someone has your back.</p>
                </article>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="content-section">
            <h2 class="section-title">What Our Clients Say</h2>
            <div class="card-grid">
                <article class="card" style="text-align: left;">
                    <p style="font-size: 2rem; color: var(--accent-teal); line-height: 1; margin-bottom: 15px;">&ldquo;</p>
                    <p style="color: #c0c0c0; line-height: 1.8; margin-bottom: 20px;">T's Travel took care of every single detail of our Caribbean cruise. We just showed up at the port — everything else was already handled. Best holiday we've ever had.</p>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--accent-teal); display: flex; align-items: center; justify-content: center; font-weight: 700; color: white; flex-shrink: 0;">SM</div>
                        <div>
                            <p style="font-weight: 600; color: var(--text-dark);">Sarah M.</p>
                            <p style="font-size: 0.85rem; color: #888;">Caribbean Cruise, 2025</p>
                        </div>
                    </div>
                </article>
                <article class="card" style="text-align: left;">
                    <p style="font-size: 2rem; color: var(--accent-teal); line-height: 1; margin-bottom: 15px;">&ldquo;</p>
                    <p style="color: #c0c0c0; line-height: 1.8; margin-bottom: 20px;">We booked a group trip to Europe for 14 people — a logistical nightmare I was dreading. T's Travel made it completely stress-free and everyone had an amazing time.</p>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--primary-blue); display: flex; align-items: center; justify-content: center; font-weight: 700; color: white; flex-shrink: 0;">JR</div>
                        <div>
                            <p style="font-weight: 600; color: var(--text-dark);">James R.</p>
                            <p style="font-size: 0.85rem; color: #888;">European Group Tour, 2025</p>
                        </div>
                    </div>
                </article>
                <article class="card" style="text-align: left;">
                    <p style="font-size: 2rem; color: var(--accent-teal); line-height: 1; margin-bottom: 15px;">&ldquo;</p>
                    <p style="color: #c0c0c0; line-height: 1.8; margin-bottom: 20px;">Our all-inclusive resort in Mexico was absolutely perfect. The recommendations were spot-on for what we were looking for as a couple. We're already booking our next trip!</p>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--coral-accent); display: flex; align-items: center; justify-content: center; font-weight: 700; color: white; flex-shrink: 0;">LP</div>
                        <div>
                            <p style="font-weight: 600; color: var(--text-dark);">Lisa & Paul T.</p>
                            <p style="font-size: 0.85rem; color: #888;">All-Inclusive Mexico, 2026</p>
                        </div>
                    </div>
                </article>
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

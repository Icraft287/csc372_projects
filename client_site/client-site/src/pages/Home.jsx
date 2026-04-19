/*
  File: Home.jsx
  Author: Isaac Craft
  Date: February 14, 2026
  Description: Homepage for T's Travel. Displays the hero banner, services overview,
               popular destination quick-links, and a call-to-action section.
               Uses .map() to render service cards and destination cards from data arrays.
  State:
    - heroImage (object|null): Stores Unsplash photo URL and photographer credit
                               fetched on mount to use as the hero background.
  AI help: Unsplash API integration ported from original script.js.
*/

import { useState, useEffect } from "react";
import { Link } from "react-router-dom";
import Card from "../components/Card";
import { homeServiceCards } from "../data/servicesData";
import { homeDestinationCards } from "../data/destinationsData";

// Your Unsplash API key from the original script.js
const UNSPLASH_ACCESS_KEY = "BG94OREPs4vtLCNUMtKGsVfGUVsJIa_3hpKtMMPO9hs";

// Home — main landing page
function Home() {
  // State: stores fetched Unsplash photo data for the hero background.
  // null = not yet loaded; CSS gradient fallback shows while loading or on error.
  const [heroImage, setHeroImage] = useState(null);

  // ===== Unsplash API: fetch a random travel photo on mount =====
  useEffect(() => {
    async function fetchHeroImage() {
      if (!UNSPLASH_ACCESS_KEY || UNSPLASH_ACCESS_KEY === "YOUR_ACCESS_KEY_HERE") return;

      try {
        const res = await fetch(
          `https://api.unsplash.com/photos/random?query=travel,vacation,beach,destination&orientation=landscape&client_id=${UNSPLASH_ACCESS_KEY}`
        );
        if (!res.ok) throw new Error("Unsplash fetch failed");
        const data = await res.json();

        // Immutable state update — store only the fields we need
        setHeroImage({
          url: data.urls.regular,
          photographer: data.user.name,
          photographerUrl: data.user.links.html,
        });
      } catch (err) {
        // On error, heroImage stays null and the CSS gradient fallback shows
        console.error("Could not load hero image:", err);
      }
    }

    fetchHeroImage();
  }, []); // Empty array — runs once when the component mounts

  // Build the hero background style: photo + dark overlay when loaded, empty when not
  // (the CSS gradient from index.css acts as the fallback when no inline style is set)
  const heroStyle = heroImage
    ? {
        backgroundImage: `linear-gradient(rgba(30, 58, 95, 0.7), rgba(42, 157, 143, 0.7)), url('${heroImage.url}')`,
        backgroundSize: "cover",
        backgroundPosition: "center",
        backgroundRepeat: "no-repeat",
      }
    : {};

  return (
    <>
      {/* ===== Hero Section =====
          Rendered inline (not via Hero component) so we can apply the
          dynamic Unsplash background style and photo credit overlay. */}
      <header className="hero" style={heroStyle}>
        <div className="hero-content">
          <h1>Your Dream Vacation Awaits</h1>
          <p>Personalized travel planning with expert guidance every step of the way</p>
          <Link to="/contact" className="cta-button">Plan Your Trip</Link>
        </div>

        {/* Photo credit — only rendered once the Unsplash image has loaded */}
        {heroImage && (
          <div className="photo-credit">
            Photo by{" "}
            <a
              href={`${heroImage.photographerUrl}?utm_source=ts_travel&utm_medium=referral`}
              target="_blank"
              rel="noopener noreferrer"
            >
              {heroImage.photographer}
            </a>{" "}
            on{" "}
            <a
              href="https://unsplash.com?utm_source=ts_travel&utm_medium=referral"
              target="_blank"
              rel="noopener noreferrer"
            >
              Unsplash
            </a>
          </div>
        )}
      </header>

      <main>
        {/* ===== Services Overview Section ===== */}
        <section className="content-section">
          <h2 className="section-title">Our Services</h2>

          {/* Render service cards dynamically from homeServiceCards array */}
          <div className="card-grid">
            {homeServiceCards.map((service) => (
              <Card
                key={service.id}
                icon={service.icon}
                title={service.title}
                detail={service.detail}
              />
            ))}
          </div>
        </section>

        {/* ===== Popular Destinations Section ===== */}
        <section className="content-section" style={{ background: "var(--warm-sand)" }}>
          <h2 className="section-title">Popular Destinations</h2>

          {/* Render destination quick-links dynamically */}
          <div className="destination-grid">
            {homeDestinationCards.map((dest) => (
              <Link
                key={dest.id}
                to={`/destinations#${dest.id}`}
                className="destination-card"
              >
                {dest.icon} {dest.label}
              </Link>
            ))}
          </div>
        </section>

        {/* ===== Call to Action Section ===== */}
        <section className="content-section" style={{ background: "var(--warm-sand)", textAlign: "center" }}>
          <h2 style={{ fontFamily: "'Playfair Display', serif", fontSize: "2.5rem", color: "var(--primary-blue)", marginBottom: "20px" }}>
            Ready to Start Planning?
          </h2>
          <p style={{ fontSize: "1.2rem", color: "var(--text-dark)", marginBottom: "30px", maxWidth: "700px", marginLeft: "auto", marginRight: "auto" }}>
            Let us help you create unforgettable memories. Contact us today to begin planning your perfect vacation.
          </p>
          <Link to="/contact" className="cta-button">Get Started</Link>
        </section>
      </main>
    </>
  );
}

export default Home;
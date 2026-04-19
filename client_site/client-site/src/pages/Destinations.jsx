/*
  File: Destinations.jsx
  Author: Isaac Carft
  Date: February 14, 2026
  Description: Destinations page for T's Travel. Renders destination category sections
               from destinationCategories array using .map(). Includes Unsplash API
               integration (ported from original script.js) via useEffect.
  State:
    - images (object): Stores loaded Unsplash image URLs keyed by destination id.
    - activeFilter (string): Tracks which destination category filter tab is selected.
  AI help: Unsplash API integration ported from original script.js. Filter state and
           scroll-to-section behavior are new additions for React interactivity.
*/

import { useState, useEffect, useRef } from "react";
import { Link } from "react-router-dom";
import Hero from "../components/Hero";
import ServiceCategory from "../components/ServiceCategory";
import Card from "../components/Card";
import { destinationCategories, moreDestinations } from "../data/destinationsData";

// Your existing Unsplash API key from script.js
const UNSPLASH_ACCESS_KEY = "BG94OREPs4vtLCNUMtKGsVfGUVsJIa_3hpKtMMPO9hs";

// Destinations — full destination categories page with Unsplash images and a filter
function Destinations() {
  // State: stores fetched Unsplash image data keyed by destination id (e.g. "caribbean")
  const [images, setImages] = useState({});

  // State: which filter tab the user has selected ("all" or a destination id)
  const [activeFilter, setActiveFilter] = useState("all");

  // Refs map for each destination section — used to scroll to a section on filter click
  const sectionRefs = useRef({});

  // ===== Unsplash API: fetch destination images on mount =====
  useEffect(() => {
    async function fetchImages() {
      const results = {};

      for (const dest of destinationCategories) {
        // Skip if API key is not configured
        if (!UNSPLASH_ACCESS_KEY || UNSPLASH_ACCESS_KEY === "YOUR_ACCESS_KEY_HERE") break;

        try {
          const res = await fetch(
            `https://api.unsplash.com/photos/random?query=${encodeURIComponent(dest.unsplashQuery)}&orientation=landscape&client_id=${UNSPLASH_ACCESS_KEY}`
          );
          if (!res.ok) continue;
          const data = await res.json();
          // Store image url and photographer credit for each destination
          results[dest.id] = {
            url: data.urls.regular,
            photographer: data.user.name,
            photographerUrl: data.user.links.html,
          };
        } catch (err) {
          console.error(`Failed to load image for ${dest.id}:`, err);
        }
      }

      // Immutable state update — spread to create new object rather than mutating
      setImages((prev) => ({ ...prev, ...results }));
    }

    fetchImages();
  }, []); // Empty dependency array — runs once on mount

  // ===== Filter: scroll to section when a filter tab is clicked =====
  const handleFilterClick = (id) => {
    setActiveFilter(id);
    if (id !== "all" && sectionRefs.current[id]) {
      sectionRefs.current[id].scrollIntoView({ behavior: "smooth", block: "start" });
    }
  };

  // Filter which destination categories to display based on activeFilter state
  const visibleCategories =
    activeFilter === "all"
      ? destinationCategories
      : destinationCategories.filter((d) => d.id === activeFilter);

  return (
    <>
      {/* Hero banner */}
      <Hero
        title="Explore Dream Destinations"
        subtitle="Discover where your next adventure will take you"
      />

      <main>
        {/* ===== Filter Tabs ===== */}
        {/* State: activeFilter drives which tab is highlighted and which sections show */}
        <section className="content-section" style={{ paddingBottom: "20px" }}>
          <div className="destination-filter">
            {/* "All" tab */}
            <button
              className={`filter-btn${activeFilter === "all" ? " active" : ""}`}
              onClick={() => handleFilterClick("all")}
            >
              🌍 All Destinations
            </button>

            {/* One tab per category, rendered via .map() */}
            {destinationCategories.map((dest) => (
              <button
                key={dest.id}
                className={`filter-btn${activeFilter === dest.id ? " active" : ""}`}
                onClick={() => handleFilterClick(dest.id)}
              >
                {dest.icon} {dest.title}
              </button>
            ))}
          </div>
        </section>

        {/* ===== Destination Category Sections ===== */}
        {/*
          .map() renders each visible destination as a section with:
          - A ServiceCategory component (reused from services)
          - A Unsplash image (loaded into images state via useEffect)
          - Alternating background color via altBg prop
        */}
        {visibleCategories.map((dest) => (
          <section
            key={dest.id}
            id={dest.id}
            ref={(el) => (sectionRefs.current[dest.id] = el)}
            className="content-section"
            style={dest.altBg ? { background: "var(--warm-sand)" } : {}}
          >
            <ServiceCategory
              icon={dest.icon}
              title={dest.title}
              description={dest.description}
              features={dest.spots}
            />

            {/* Unsplash image — only rendered once the image state is populated */}
            {images[dest.id] && (
              <div className="destination-image-container">
                <img
                  className="destination-image"
                  src={images[dest.id].url}
                  alt={`${dest.title} destination`}
                />
                <div className="image-credit">
                  Photo by{" "}
                  <a
                    href={`${images[dest.id].photographerUrl}?utm_source=ts_travel&utm_medium=referral`}
                    target="_blank"
                    rel="noopener noreferrer"
                  >
                    {images[dest.id].photographer}
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
              </div>
            )}
          </section>
        ))}

        {/* ===== More Destinations Grid ===== */}
        <section className="content-section">
          <h2 className="section-title">More Destinations We Specialize In</h2>
          <div className="card-grid">
            {moreDestinations.map((dest) => (
              <Card key={dest.id} icon={dest.icon} title={dest.title} detail={dest.detail} />
            ))}
          </div>
        </section>

        {/* ===== Call to Action ===== */}
        <section className="content-section" style={{ background: "var(--warm-sand)", textAlign: "center" }}>
          <h2 style={{ fontFamily: "'Playfair Display', serif", fontSize: "2.5rem", color: "var(--primary-blue)", marginBottom: "20px" }}>
            Not Sure Where to Go?
          </h2>
          <p style={{ fontSize: "1.2rem", color: "var(--text-dark)", marginBottom: "30px", maxWidth: "700px", marginLeft: "auto", marginRight: "auto" }}>
            Let us help you find the perfect destination based on your interests, budget, and travel style.
          </p>
          <Link to="/contact" className="cta-button">Get Recommendations</Link>
        </section>
      </main>
    </>
  );
}

export default Destinations;

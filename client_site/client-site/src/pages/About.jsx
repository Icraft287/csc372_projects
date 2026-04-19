/*
  File: About.jsx
  Author: Isaac Craft
  Date: February 14, 2026
  Description: About page for T's Travel. Covers the company story, why choose us,
               mission statement, and a values grid. Uses .map() to render ValueCard
               components from the valuesData array.
*/

import { Link } from "react-router-dom";
import Hero from "../components/Hero";
import ValueCard from "../components/ValueCard";
import { valuesData } from "../data/valuesData";

// About — company background, mission, and values
function About() {
  return (
    <>
      {/* Hero banner */}
      <Hero
        title="About T's Travel"
        subtitle="Passionate about creating unforgettable travel experiences"
      />

      <main>
        {/* ===== Story / Why Choose Us / Mission Section ===== */}
        <section className="content-section">
          <article className="about-content">

            {/* Our Story */}
            <h2>Our Story</h2>
            <p>
              At T's Travel, we believe that travel is more than just visiting new places—it's about creating
              memories that last a lifetime. As a dedicated travel professional, I'm passionate about helping
              individuals, couples, families, and retirees plan vacations that exceed their expectations.
            </p>
            <p>
              With years of experience in the travel industry and a personal love for exploring the world, I
              understand what makes a trip truly special. From the initial consultation to the moment you return
              home, I'm committed to providing personalized service and expert guidance every step of the way.
            </p>

            {/* Why Choose Us */}
            <h2>Why Choose Us</h2>
            <p>
              Planning a vacation can be overwhelming. There are countless options, endless research, and the
              stress of making sure everything is perfect. That's where I come in. I take the hassle out of
              travel planning by:
            </p>
            <ul style={{ lineHeight: 2, margin: "20px 0 20px 40px" }}>
              <li>Listening to your needs and preferences</li>
              <li>Providing personalized recommendations based on your budget and interests</li>
              <li>Handling all the details so you can relax and enjoy</li>
              <li>Offering insider knowledge and expert advice</li>
              <li>Being available to support you before, during, and after your trip</li>
            </ul>
            <p>
              Whether you're dreaming of a Caribbean cruise, a European adventure, an all-inclusive resort
              getaway, or a custom-designed journey, I'm here to make it happen.
            </p>

            {/* Our Mission */}
            <h2>Our Mission</h2>
            <p>
              My mission is simple: to help clients confidently plan memorable travel experiences without the
              hassle of researching and booking everything on their own. I believe that working with a dedicated
              travel professional means less stress, better value, and more enjoyment from start to finish.
            </p>
          </article>
        </section>

        {/* ===== Our Values Section ===== */}
        <section className="content-section" style={{ background: "var(--warm-sand)" }}>
          <h2 className="section-title">Our Values</h2>

          {/* Render ValueCard components via .map() over valuesData array */}
          <div className="values-grid">
            {valuesData.map((value) => (
              <ValueCard
                key={value.id}
                icon={value.icon}
                title={value.title}
                detail={value.detail}
              />
            ))}
          </div>
        </section>

        {/* ===== Call to Action ===== */}
        <section className="content-section" style={{ textAlign: "center" }}>
          <h2 style={{ fontFamily: "'Playfair Display', serif", fontSize: "2.5rem", color: "var(--primary-blue)", marginBottom: "20px" }}>
            Let's Plan Your Next Adventure
          </h2>
          <p style={{ fontSize: "1.2rem", color: "var(--text-dark)", marginBottom: "30px", maxWidth: "700px", marginLeft: "auto", marginRight: "auto" }}>
            Ready to turn your travel dreams into reality? Contact us today to get started.
          </p>
          <Link to="/contact" className="cta-button">Contact Us</Link>
        </section>
      </main>
    </>
  );
}

export default About;

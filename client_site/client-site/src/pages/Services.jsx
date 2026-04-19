/*
  File: Services.jsx
  Author: Isaac Craft
  Date: February 14, 2026
  Description: Services page for T's Travel. Renders all service categories and the
               "What's Included" feature cards using .map() over servicesData and
               includedFeatures arrays. Each service block is a ServiceCategory component.
*/

import { Link } from "react-router-dom";
import Hero from "../components/Hero";
import ServiceCategory from "../components/ServiceCategory";
import Card from "../components/Card";
import { servicesData, includedFeatures } from "../data/servicesData";

// Services — lists all travel services and what's included with each
function Services() {
  return (
    <>
      {/* Hero banner */}
      <Hero
        title="Our Travel Services"
        subtitle="Expert planning for every type of journey"
      />

      <main>
        {/* ===== Services List Section ===== */}
        <section className="content-section">
          {/*
            Render each service using ServiceCategory component.
            .map() iterates over servicesData array — props pass icon, title,
            description, and features into each ServiceCategory instance.
          */}
          {servicesData.map((service) => (
            <ServiceCategory
              key={service.id}
              icon={service.icon}
              title={service.title}
              description={service.description}
              features={service.features}
            />
          ))}
        </section>

        {/* ===== What's Included Section ===== */}
        <section className="content-section" style={{ background: "var(--warm-sand)", textAlign: "center" }}>
          <h2 style={{ fontFamily: "'Playfair Display', serif", fontSize: "2.5rem", color: "var(--primary-blue)", marginBottom: "20px" }}>
            What's Included in Every Service
          </h2>

          {/* Render included feature cards via .map() over includedFeatures array */}
          <div className="card-grid" style={{ marginTop: "40px" }}>
            {includedFeatures.map((feature) => (
              <Card
                key={feature.id}
                icon={feature.icon}
                title={feature.title}
                detail={feature.detail}
              />
            ))}
          </div>
        </section>

        {/* ===== Call to Action ===== */}
        <section className="content-section" style={{ textAlign: "center" }}>
          <h2 style={{ fontFamily: "'Playfair Display', serif", fontSize: "2.5rem", color: "var(--primary-blue)", marginBottom: "20px" }}>
            Ready to Get Started?
          </h2>
          <p style={{ fontSize: "1.2rem", color: "var(--text-dark)", marginBottom: "30px", maxWidth: "700px", marginLeft: "auto", marginRight: "auto" }}>
            Let's discuss which service is right for your next adventure. Contact us today for a free consultation.
          </p>
          <Link to="/contact" className="cta-button">Contact Us</Link>
        </section>
      </main>
    </>
  );
}

export default Services;

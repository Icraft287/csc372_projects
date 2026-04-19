/*
  File: Contact.jsx
  Author: Isaac Craft
  Date: February 14, 2026
  Description: Contact page with a fully controlled travel inquiry form and validation.
               All inputs are connected to React state (controlled components).
  State:
    - formData (object): Stores all form field values. Updated on every input change.
    - errors (object): Stores validation error messages per field.
    - submitted (bool): Tracks whether the form has been successfully submitted.
  Validation covers: required name, valid email format, required phone (min 7 digits),
                     required service selection, min-length message (10 chars).
*/

import { useState } from "react";
import Hero from "../components/Hero";

// Initial empty state for the form — defined outside component to reuse on reset
const INITIAL_FORM = {
  name: "",
  email: "",
  phone: "",
  service: "",
  destination: "",
  dates: "",
  travelers: "",
  budget: "",
  message: "",
};

// Contact — contact info sidebar + fully controlled inquiry form
function Contact() {
  // State: all form field values as a single object
  const [formData, setFormData] = useState(INITIAL_FORM);

  // State: validation error messages per field (empty string = no error)
  const [errors, setErrors] = useState({});

  // State: controls success message display after valid submission
  const [submitted, setSubmitted] = useState(false);

  // ===== Handle input changes =====
  // Uses computed property names to update the correct field immutably
  function handleChange(e) {
    const { name, value } = e.target;
    // Immutable update: spread previous state, override only the changed field
    setFormData((prev) => ({ ...prev, [name]: value }));
    // Clear the error for this field as the user types
    setErrors((prev) => ({ ...prev, [name]: "" }));
  }

  // ===== Validate all fields =====
  // Returns an errors object; empty object means form is valid
  function validate() {
    const newErrors = {};

    // Required: name
    if (!formData.name.trim()) {
      newErrors.name = "Please enter your name.";
    }

    // Required: valid email format
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!formData.email.trim()) {
      newErrors.email = "Please enter your email address.";
    } else if (!emailPattern.test(formData.email)) {
      newErrors.email = "Please enter a valid email address.";
    }

    // Required: phone — must have at least 7 digits
    const digitsOnly = formData.phone.replace(/\D/g, "");
    if (!formData.phone.trim()) {
      newErrors.phone = "Please enter your phone number.";
    } else if (digitsOnly.length < 7) {
      newErrors.phone = "Please enter a valid phone number (at least 7 digits).";
    }

    // Required: service type selection
    if (!formData.service) {
      newErrors.service = "Please select a service type.";
    }

    // Optional but validated if filled: message minimum length
    if (formData.message.trim().length > 0 && formData.message.trim().length < 10) {
      newErrors.message = "Please provide at least 10 characters in your message.";
    }

    return newErrors;
  }

  // ===== Handle form submission =====
  function handleSubmit(e) {
    e.preventDefault(); // Prevent default browser form submission

    const validationErrors = validate();

    if (Object.keys(validationErrors).length > 0) {
      // Validation failed — update errors state to display messages
      setErrors(validationErrors);
      return;
    }

    // Validation passed — show success message and reset form
    setSubmitted(true);
    setFormData(INITIAL_FORM); // Immutable reset: replace state with fresh object
    setErrors({});
  }

  return (
    <>
      {/* Hero banner */}
      <Hero
        title="Get in Touch"
        subtitle="Let's start planning your perfect vacation"
      />

      <main>
        {/* ===== Contact Layout: Info + Form side by side ===== */}
        <section className="contact-layout">

          {/* Contact information block */}
          <div className="contact-info">
            <h2>Contact Information</h2>
            <p style={{ marginBottom: "30px", lineHeight: "1.8" }}>
              We're here to help you plan the vacation of your dreams. Reach out using any method below.
            </p>

            <div className="info-item">
              <div className="info-icon" aria-hidden="true">📞</div>
              <div>
                <strong style={{ display: "block", marginBottom: "5px" }}>Phone</strong>
                <a href="tel:5551234567" style={{ color: "white", textDecoration: "none" }}>(555) 123-4567</a><br />
                <span style={{ fontSize: "0.9rem", opacity: 0.8 }}>Monday – Saturday, 9 AM – 6 PM</span>
              </div>
            </div>

            <div className="info-item">
              <div className="info-icon" aria-hidden="true">📧</div>
              <div>
                <strong style={{ display: "block", marginBottom: "5px" }}>Email</strong>
                <a href="mailto:info@tstravel.com" style={{ color: "white", textDecoration: "none" }}>info@tstravel.com</a><br />
                <span style={{ fontSize: "0.9rem", opacity: 0.8 }}>We respond within 24 hours</span>
              </div>
            </div>

            <div className="info-item">
              <div className="info-icon" aria-hidden="true">📍</div>
              <div>
                <strong style={{ display: "block", marginBottom: "5px" }}>Office Location</strong>
                <span>123 Travel Lane, Suite 456</span><br />
                <span>Wanderlust City, ST 12345</span>
              </div>
            </div>

            <div className="info-item">
              <div className="info-icon" aria-hidden="true">⏰</div>
              <div>
                <strong style={{ display: "block", marginBottom: "5px" }}>Business Hours</strong>
                <span>Monday – Friday: 9:00 AM – 6:00 PM</span><br />
                <span>Saturday: 10:00 AM – 3:00 PM</span><br />
                <span>Sunday: Closed</span>
              </div>
            </div>
          </div>

          {/* ===== Travel Inquiry Form ===== */}
          <div className="inquiry-form">
            <h2>Travel Inquiry Form</h2>
            <p style={{ color: "var(--text-dark)", marginBottom: "25px" }}>
              Tell us about your dream vacation and we'll get back to you with personalized recommendations.
            </p>

            {/* Success message — shown after valid submission */}
            {submitted && (
              <div className="form-success">
                ✅ Thank you! Your travel inquiry has been submitted. We'll be in touch within 24 hours.
              </div>
            )}

            {/* Controlled form — all inputs tied to formData state via value + onChange */}
            <form onSubmit={handleSubmit} noValidate>

              {/* Name — required */}
              <div className={`form-group${errors.name ? " error" : ""}`}>
                <label htmlFor="name">Your Name *</label>
                <input
                  type="text"
                  id="name"
                  name="name"
                  value={formData.name}
                  onChange={handleChange}
                  placeholder="John Smith"
                  aria-required="true"
                  aria-describedby={errors.name ? "name-error" : undefined}
                />
                {errors.name && <div className="form-error" id="name-error">{errors.name}</div>}
              </div>

              {/* Email — required, format validated */}
              <div className={`form-group${errors.email ? " error" : ""}`}>
                <label htmlFor="email">Email Address *</label>
                <input
                  type="email"
                  id="email"
                  name="email"
                  value={formData.email}
                  onChange={handleChange}
                  placeholder="john@example.com"
                  aria-required="true"
                  aria-describedby={errors.email ? "email-error" : undefined}
                />
                {errors.email && <div className="form-error" id="email-error">{errors.email}</div>}
              </div>

              {/* Phone — required, min 7 digits */}
              <div className={`form-group${errors.phone ? " error" : ""}`}>
                <label htmlFor="phone">Phone Number *</label>
                <input
                  type="tel"
                  id="phone"
                  name="phone"
                  value={formData.phone}
                  onChange={handleChange}
                  placeholder="(555) 123-4567"
                  aria-required="true"
                  aria-describedby={errors.phone ? "phone-error" : undefined}
                />
                {errors.phone && <div className="form-error" id="phone-error">{errors.phone}</div>}
              </div>

              {/* Service type — required dropdown */}
              <div className={`form-group${errors.service ? " error" : ""}`}>
                <label htmlFor="service">Type of Service *</label>
                <select
                  id="service"
                  name="service"
                  value={formData.service}
                  onChange={handleChange}
                  aria-required="true"
                  aria-describedby={errors.service ? "service-error" : undefined}
                >
                  <option value="">Select a service...</option>
                  <option value="cruise">Cruise Vacation</option>
                  <option value="all-inclusive">All-Inclusive Resort</option>
                  <option value="group">Group Travel</option>
                  <option value="custom">Custom Trip Planning</option>
                  <option value="other">Other</option>
                </select>
                {errors.service && <div className="form-error" id="service-error">{errors.service}</div>}
              </div>

              {/* Destination — optional */}
              <div className="form-group">
                <label htmlFor="destination">Preferred Destination</label>
                <input
                  type="text"
                  id="destination"
                  name="destination"
                  value={formData.destination}
                  onChange={handleChange}
                  placeholder="e.g., Caribbean, Europe, Asia"
                />
              </div>

              {/* Travel dates — optional */}
              <div className="form-group">
                <label htmlFor="dates">Preferred Travel Dates</label>
                <input
                  type="text"
                  id="dates"
                  name="dates"
                  value={formData.dates}
                  onChange={handleChange}
                  placeholder="e.g., June 2026"
                />
              </div>

              {/* Number of travelers — optional */}
              <div className="form-group">
                <label htmlFor="travelers">Number of Travelers</label>
                <input
                  type="number"
                  id="travelers"
                  name="travelers"
                  value={formData.travelers}
                  onChange={handleChange}
                  min="1"
                  placeholder="2"
                />
              </div>

              {/* Budget — optional dropdown */}
              <div className="form-group">
                <label htmlFor="budget">Approximate Budget (Optional)</label>
                <select id="budget" name="budget" value={formData.budget} onChange={handleChange}>
                  <option value="">Select budget range...</option>
                  <option value="under-2000">Under $2,000</option>
                  <option value="2000-5000">$2,000 – $5,000</option>
                  <option value="5000-10000">$5,000 – $10,000</option>
                  <option value="over-10000">Over $10,000</option>
                </select>
              </div>

              {/* Message — optional, min 10 chars if filled */}
              <div className={`form-group${errors.message ? " error" : ""}`}>
                <label htmlFor="message">Additional Details</label>
                <textarea
                  id="message"
                  name="message"
                  value={formData.message}
                  onChange={handleChange}
                  placeholder="Tell us about your travel preferences, special requests, or any questions..."
                  aria-describedby={errors.message ? "message-error" : undefined}
                />
                {errors.message && <div className="form-error" id="message-error">{errors.message}</div>}
              </div>

              {/* Submit button */}
              <button type="submit" className="submit-btn">Submit Inquiry</button>
            </form>
          </div>
        </section>

        {/* ===== Call to Action — phone alternative ===== */}
        <section className="content-section" style={{ textAlign: "center", background: "var(--warm-sand)", padding: "60px 40px" }}>
          <h2 style={{ fontFamily: "'Playfair Display', serif", fontSize: "2rem", color: "var(--primary-blue)", marginBottom: "15px" }}>
            Prefer to Talk First?
          </h2>
          <p style={{ fontSize: "1.1rem", color: "var(--text-dark)", marginBottom: "25px" }}>
            Give us a call and let's discuss your travel plans over the phone.
          </p>
          <a href="tel:5551234567" className="cta-button">📞 Call Now: (555) 123-4567</a>
        </section>
      </main>
    </>
  );
}

export default Contact;

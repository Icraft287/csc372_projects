/*
  File: Footer.jsx
  Author: Isaac Craft
  Date: February 14, 2026
  Description: Site-wide footer component with navigation links and social media links.
               Shared across all pages via App.jsx layout.
  Props: none
*/

import { Link } from "react-router-dom";

// Footer — displays site links, copyright, and social media placeholders
function Footer() {
  return (
    <footer className="footer">
      {/* Footer navigation links */}
      <nav className="footer-links" aria-label="Footer navigation">
        <Link to="/"             className="footer-link">Home</Link>
        <Link to="/about"        className="footer-link">About</Link>
        <Link to="/services"     className="footer-link">Services</Link>
        <Link to="/destinations" className="footer-link">Destinations</Link>
        <Link to="/contact"      className="footer-link">Contact</Link>
      </nav>

      {/* Copyright and social links */}
      <div className="footer-bottom">
        <p>&copy; 2026 T's Travel. All rights reserved.</p>
        <div className="social-links">
          <a href="#" className="social-link" aria-label="Visit our Facebook page">Facebook</a>
          <a href="#" className="social-link" aria-label="Visit our Instagram page">Instagram</a>
          <a href="#" className="social-link" aria-label="Visit our Twitter page">Twitter</a>
        </div>
      </div>
    </footer>
  );
}

export default Footer;

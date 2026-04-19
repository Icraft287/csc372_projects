/*
  File: Navbar.jsx
  Author: Isaac Craft
  Date: February 14, 2026
  Description: Sticky navigation bar component shared across all pages.
               Includes the T's Travel logo, nav links, and a mobile hamburger menu.
  State: menuOpen (bool) — toggles the mobile nav drawer open/closed via useState.
  Props: none (reads current route via useLocation to set the active link)
*/

import { useState } from "react";
import { NavLink, Link } from "react-router-dom";

// Navbar component — renders site-wide navigation with mobile support
function Navbar() {
  // State: controls whether the mobile hamburger menu is open
  const [menuOpen, setMenuOpen] = useState(false);

  // Toggle handler — flips menuOpen between true/false
  const toggleMenu = () => setMenuOpen((prev) => !prev);

  // Close menu when a link is clicked (smooth UX on mobile)
  const closeMenu = () => setMenuOpen(false);

  return (
    <nav className="navbar">
      {/* Logo — links back to homepage */}
      <Link to="/" className="logo" onClick={closeMenu}>
        <div className="logo-icon" aria-hidden="true">✈️</div>
        <span>T's Travel</span>
      </Link>

      {/* Hamburger toggle button (visible on mobile only via CSS) */}
      <div
        className={`hamburger${menuOpen ? " open" : ""}`}
        onClick={toggleMenu}
        role="button"
        tabIndex={0}
        aria-label="Toggle navigation menu"
        aria-expanded={menuOpen}
        onKeyDown={(e) => e.key === "Enter" && toggleMenu()}
      >
        <span></span>
        <span></span>
        <span></span>
      </div>

      {/* Nav links — .active class applied automatically by NavLink */}
      <div className={`nav-links${menuOpen ? " active" : ""}`}>
        <NavLink to="/"            className={({ isActive }) => "nav-link" + (isActive ? " active" : "")} onClick={closeMenu} end>Home</NavLink>
        <NavLink to="/about"       className={({ isActive }) => "nav-link" + (isActive ? " active" : "")} onClick={closeMenu}>About</NavLink>
        <NavLink to="/services"    className={({ isActive }) => "nav-link" + (isActive ? " active" : "")} onClick={closeMenu}>Services</NavLink>
        <NavLink to="/destinations"className={({ isActive }) => "nav-link" + (isActive ? " active" : "")} onClick={closeMenu}>Destinations</NavLink>
        <NavLink to="/contact"     className={({ isActive }) => "contact-btn" + (isActive ? " active" : "")} onClick={closeMenu}>Contact Us</NavLink>
      </div>
    </nav>
  );
}

export default Navbar;

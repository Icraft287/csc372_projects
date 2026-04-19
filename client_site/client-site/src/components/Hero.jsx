/*
  File: Hero.jsx
  Author: Isaac Crft
  Date: February 14, 2026
  Description: Reusable hero/banner component used at the top of every page.
               Displays a heading, subtitle, and an optional call-to-action button.
  Props:
    - title (string): Main heading text
    - subtitle (string): Subheading/description text
    - buttonText (string, optional): CTA button label; if omitted, button is hidden
    - buttonTo (string, optional): React Router path for the CTA button
*/

import { Link } from "react-router-dom";

// Hero banner — accepts title, subtitle, and optional CTA button via props
function Hero({ title, subtitle, buttonText, buttonTo }) {
  return (
    <header className="hero">
      <div className="hero-content">
        <h1>{title}</h1>
        <p>{subtitle}</p>

        {/* Only render the CTA button when buttonText prop is provided */}
        {buttonText && buttonTo && (
          <Link to={buttonTo} className="cta-button">
            {buttonText}
          </Link>
        )}
      </div>
    </header>
  );
}

export default Hero;

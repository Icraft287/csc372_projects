/*
  File: ValueCard.jsx
  Author: Isaac Craft
  Date: February 14, 2026
  Description: Displays a single value card used in the "Our Values" section on the
               About page. Rendered via .map() over valuesData in About.jsx.
  Props:
    - icon (string): Emoji icon for the value
    - title (string): Value name/heading
    - detail (string): Short description of the value
*/

// ValueCard — displays an individual company value with icon, title, and description
function ValueCard({ icon, title, detail }) {
  return (
    <article className="value-card">
      {/* Value icon */}
      <div className="value-icon" aria-hidden="true">{icon}</div>
      <h3>{title}</h3>
      <p>{detail}</p>
    </article>
  );
}

export default ValueCard;

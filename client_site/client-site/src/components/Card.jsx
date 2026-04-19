/*
  File: Card.jsx
  Author: Isaac Craft
  Date: February 14, 2026
  Description: Reusable card component used throughout the site for services,
               included features, more destinations, and similar grid items.
  Props:
    - icon (string): Emoji or icon character
    - title (string): Card heading
    - detail (string): Short description text
*/

// Card — generic icon + title + detail card used in grids across the site
function Card({ icon, title, detail }) {
  return (
    <article className="card">
      {/* Emoji icon displayed at the top of the card */}
      <div className="card-icon" aria-hidden="true">{icon}</div>
      <h3>{title}</h3>
      <p>{detail}</p>
    </article>
  );
}

export default Card;

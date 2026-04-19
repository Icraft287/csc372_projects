/*
  File: ServiceCategory.jsx
  Author: Isaac Craft
  Date: February 14, 2026
  Description: Displays a full service block with icon, title, description, and
               a grid of sub-features. Used in Services.jsx via .map() over servicesData.
  Props:
    - icon (string): Emoji for the service icon
    - title (string): Service category name
    - description (string): Paragraph description of the service
    - features (array): Array of { title, detail } objects for the sub-feature grid
*/

// ServiceCategory — renders a full service block with header, description, and feature grid
function ServiceCategory({ icon, title, description, features }) {
  return (
    <article className="service-category">
      {/* Service header: large icon + title */}
      <div className="service-header">
        <div className="service-icon-large" aria-hidden="true">{icon}</div>
        <div>
          <h2>{title}</h2>
        </div>
      </div>

      {/* Service description paragraph */}
      <p className="service-description">{description}</p>

      {/* Sub-feature grid — each feature rendered from the features prop array */}
      <div className="service-details">
        {features.map((feature, index) => (
          <div className="service-feature" key={index}>
            <h3>{feature.title}</h3>
            <p>{feature.detail}</p>
          </div>
        ))}
      </div>
    </article>
  );
}

export default ServiceCategory;

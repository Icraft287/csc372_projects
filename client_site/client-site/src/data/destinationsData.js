/*
  File: destinationsData.js
  Author: Isaac Crft
  Date: February 14, 2026
  Description: Static data for destination categories and additional destination cards.
               Used with .map() to render destination sections and the "More Destinations" grid.
*/

// Main destination categories — rendered with .map() in Destinations.jsx
export const destinationCategories = [
  {
    id: "caribbean",
    icon: "🌴",
    title: "Caribbean Paradise",
    description:
      "Experience pristine beaches, crystal-clear waters, and vibrant island culture. The Caribbean offers the perfect blend of relaxation and adventure, with options ranging from all-inclusive resorts to intimate boutique properties.",
    altBg: false,
    unsplashQuery: "caribbean beach tropical island",
    spots: [
      { name: "Jamaica", detail: "Reggae rhythms, stunning beaches, and warm hospitality" },
      { name: "Aruba", detail: "Year-round sunshine and picture-perfect Caribbean shores" },
      { name: "Bahamas", detail: "Island hopping, water sports, and luxurious resorts" },
      { name: "St. Lucia", detail: "Dramatic landscapes, romantic settings, and tropical beauty" },
    ],
  },
  {
    id: "europe",
    icon: "🗼",
    title: "European Adventures",
    description:
      "Immerse yourself in centuries of history, art, and culture. From the romantic streets of Paris to the ancient ruins of Rome, European destinations offer unforgettable experiences for every traveler.",
    altBg: true,
    unsplashQuery: "europe landmark architecture",
    spots: [
      { name: "Italy", detail: "Art, history, cuisine, and breathtaking landscapes" },
      { name: "France", detail: "Romance, culture, wine country, and iconic landmarks" },
      { name: "Greece", detail: "Ancient history, island hopping, and Mediterranean beauty" },
      { name: "Spain", detail: "Vibrant cities, stunning coastlines, and rich traditions" },
    ],
  },
  {
    id: "cruises",
    icon: "🚢",
    title: "Cruise Destinations",
    description:
      "See the world from the sea with carefully selected cruise itineraries. Wake up in a new destination each day while enjoying world-class dining, entertainment, and amenities onboard.",
    altBg: false,
    unsplashQuery: "cruise ship ocean",
    spots: [
      { name: "Alaska", detail: "Glaciers, wildlife, and untouched natural beauty" },
      { name: "Mediterranean", detail: "Explore multiple European countries in one voyage" },
      { name: "Caribbean Islands", detail: "Island hop through tropical paradise" },
      { name: "River Cruises", detail: "Intimate journeys through Europe and beyond" },
    ],
  },
  {
    id: "romantic",
    icon: "💑",
    title: "Romantic Getaways",
    description:
      "Create unforgettable memories with your special someone. Whether it's a honeymoon, anniversary, or just because, these destinations set the perfect romantic scene.",
    altBg: true,
    unsplashQuery: "maldives santorini romantic sunset",
    spots: [
      { name: "Maldives", detail: "Overwater bungalows and pristine private beaches" },
      { name: "Santorini", detail: "Stunning sunsets and white-washed village charm" },
      { name: "Bora Bora", detail: "Turquoise lagoons and luxury overwater resorts" },
      { name: "Tuscany", detail: "Rolling hills, vineyard stays, and Italian romance" },
    ],
  },
];

// Additional destination cards for the "More Destinations" grid
export const moreDestinations = [
  { id: 1, icon: "🏔️", title: "Adventure Travel", detail: "Costa Rica, New Zealand, Iceland, and thrilling destinations" },
  { id: 2, icon: "🌸", title: "Asia & Pacific", detail: "Thailand, Bali, Japan, Australia, and exotic locations" },
  { id: 3, icon: "🦁", title: "Safari Adventures", detail: "Kenya, Tanzania, South Africa, and wildlife experiences" },
  { id: 4, icon: "🏙️", title: "City Escapes", detail: "New York, London, Dubai, and vibrant urban destinations" },
];

// Quick-nav destination cards for the Home page
export const homeDestinationCards = [
  { id: "caribbean", icon: "🌴", label: "Caribbean" },
  { id: "europe",    icon: "🗼", label: "Europe" },
  { id: "cruises",   icon: "🚢", label: "Cruises" },
  { id: "romantic",  icon: "💑", label: "Romantic Getaways" },
];

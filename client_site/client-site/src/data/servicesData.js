/*
  File: servicesData.js
  Author: Isaac Craft
  Date: February 14, 2026
  Description: Static data arrays for services and included features.
               Used with .map() to dynamically render service cards and feature cards.
*/

// Array of main service offerings — rendered with .map() in Services.jsx and Home.jsx
export const servicesData = [
  {
    id: 1,
    icon: "🚢",
    title: "Cruise Vacations",
    description:
      "Discover the world from the comfort of a luxury cruise ship. Whether you're dreaming of tropical Caribbean waters, Mediterranean coastlines, or Alaskan glaciers, we'll help you find the perfect cruise experience.",
    features: [
      { title: "Ocean Cruises", detail: "Caribbean, Mediterranean, Alaska, and worldwide destinations" },
      { title: "River Cruises", detail: "Intimate journeys through Europe, Asia, and beyond" },
      { title: "Specialty Cruises", detail: "Themed cruises, expedition cruises, and luxury experiences" },
    ],
  },
  {
    id: 2,
    icon: "🏖️",
    title: "All-Inclusive Vacations",
    description:
      "Enjoy complete relaxation with all-inclusive resort packages where everything is taken care of. From meals and drinks to activities and entertainment, simply unwind and enjoy.",
    features: [
      { title: "Beach Resorts", detail: "Stunning coastal properties in the Caribbean, Mexico, and beyond" },
      { title: "Family Resorts", detail: "Kid-friendly amenities, activities, and entertainment for all ages" },
      { title: "Adults-Only Retreats", detail: "Peaceful, sophisticated escapes perfect for couples and adults" },
    ],
  },
  {
    id: 3,
    icon: "👥",
    title: "Group Travel",
    description:
      "Traveling with family, friends, or colleagues? We specialize in coordinating group trips that keep everyone together while meeting individual needs and preferences.",
    features: [
      { title: "Family Reunions", detail: "Multi-generational trips with activities for everyone" },
      { title: "Corporate Travel", detail: "Team building trips and business group accommodations" },
      { title: "Special Celebrations", detail: "Destination weddings, anniversaries, and milestone events" },
    ],
  },
  {
    id: 4,
    icon: "✨",
    title: "Custom Trip Planning",
    description:
      "Have a unique vision for your perfect vacation? We create completely customized itineraries tailored to your interests, budget, and travel style.",
    features: [
      { title: "Custom Itineraries", detail: "Personalized day-by-day plans designed around your preferences" },
      { title: "Special Interests", detail: "Food tours, adventure travel, cultural experiences, and more" },
      { title: "Multi-Destination", detail: "Complex trips combining multiple cities, countries, or continents" },
    ],
  },
];

// Array of "What's Included" feature cards — rendered with .map() in Services.jsx
export const includedFeatures = [
  { id: 1, icon: "📋", title: "Personalized Consultation", detail: "One-on-one planning sessions to understand your needs" },
  { id: 2, icon: "💰", title: "Budget Management", detail: "Options and recommendations that fit your budget" },
  { id: 3, icon: "📞", title: "Ongoing Support", detail: "Available before, during, and after your trip" },
  { id: 4, icon: "🎁", title: "Value Added", detail: "Access to special amenities and exclusive offers" },
];

// Compact service cards for the Home page
export const homeServiceCards = [
  { id: 1, icon: "🚢", title: "Cruises", detail: "Explore the world's oceans with carefully curated cruise experiences" },
  { id: 2, icon: "🏖️", title: "All-Inclusive Vacations", detail: "Stress-free getaways with everything included" },
  { id: 3, icon: "👥", title: "Group Travel", detail: "Perfect trips for families, friends, and organizations" },
  { id: 4, icon: "✨", title: "Custom Planning", detail: "Tailored itineraries designed just for you" },
];

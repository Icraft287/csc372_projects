/*
  File: App.jsx
  Author: Isaac Craft
  Date: February 14, 2026
  Description: Root application component. Sets up React Router routes for all 5 pages
               and wraps each page in a shared Navbar + Footer layout.
               BrowserRouter is applied in main.jsx.
*/

import { Routes, Route } from "react-router-dom";
import Navbar from "./components/Navbar";
import Footer from "./components/Footer";
import Home from "./pages/Home";
import About from "./pages/About";
import Services from "./pages/Services";
import Destinations from "./pages/Destinations";
import Contact from "./pages/Contact";

// App — defines the overall layout shell and client-side routes
function App() {
  return (
    <>
      {/* Navbar is rendered on every page above the route content */}
      <Navbar />

      {/*
        Routes — each Route maps a URL path to a page component.
        Navigation between routes does NOT cause a full page reload.
      */}
      <Routes>
        <Route path="/"             element={<Home />} />
        <Route path="/about"        element={<About />} />
        <Route path="/services"     element={<Services />} />
        <Route path="/destinations" element={<Destinations />} />
        <Route path="/contact"      element={<Contact />} />
      </Routes>

      {/* Footer is rendered on every page below the route content */}
      <Footer />
    </>
  );
}

export default App;

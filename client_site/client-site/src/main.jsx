/*
  File: main.jsx
  Author: Isaac Craft
  Date: February 14, 2026
  Description: Entry point for the React application.
               Wraps App in BrowserRouter to enable React Router client-side routing.
*/

import { StrictMode } from "react";
import { createRoot } from "react-dom/client";
import { BrowserRouter } from "react-router-dom";
import "./index.css";
import App from "./App";

// Mount the app with BrowserRouter wrapping App so all routes work
createRoot(document.getElementById("root")).render(
  <StrictMode>
    <BrowserRouter>
      <App />
    </BrowserRouter>
  </StrictMode>
);

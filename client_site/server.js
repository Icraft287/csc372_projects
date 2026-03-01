/**
 * Author: Isaac Craft
 * Date: February 22, 2026
 * Description: Express server for T's Travel website using Handlebars templating.
 *              Defines routes for each page, passes dynamic data to views,
 *              and handles 404 and 500 errors with custom pages.
 *
 *  AI usage: Structure reviewed/assisted with Claude as well as assisting with comments.
 */

const express = require('express');
const { engine } = require('express-handlebars');
const path = require('path');

// Create Express app
const app = express();

// Port the server will listen on
const PORT = 3000;

// Configure Handlebars as the view engine with 'main' as the default layout
app.engine('handlebars', engine({ defaultLayout: 'main' }));
app.set('view engine', 'handlebars');
app.set('views', path.join(__dirname, 'views'));

// Serve static files (CSS, images, JS, etc.) from the public folder
app.use(express.static(path.join(__dirname, 'public')));


// ─── Routes ───────────────────────────────────────────────────────────────────

// Home page
app.get('/', (req, res) => {
    res.render('index', {
        pageTitle: "T's Travel - Your Dream Vacation Awaits",
        destinations: [
            { emoji: '🌴', label: 'Caribbean',        link: '/destinations#caribbean' },
            { emoji: '🗼', label: 'Europe',            link: '/destinations#europe'    },
            { emoji: '🚢', label: 'Cruises',           link: '/destinations#cruises'   },
            { emoji: '💑', label: 'Romantic Getaways', link: '/destinations#romantic'  }
        ]
    });
});

// About page
app.get('/about', (req, res) => {
    res.render('about', {
        pageTitle: "About Us - T's Travel",
        yearsInBusiness: new Date().getFullYear() - 2015
    });
});

// Services page
app.get('/services', (req, res) => {
    res.render('services', {
        pageTitle: "Our Services - T's Travel",
        services: [
            { emoji: '🚢', name: 'Cruise Vacations'       },
            { emoji: '🏖️', name: 'All-Inclusive Vacations' },
            { emoji: '👥', name: 'Group Travel'            },
            { emoji: '✨', name: 'Custom Trip Planning'    }
        ]
    });
});

// Destinations page
app.get('/destinations', (req, res) => {
    res.render('destinations', {
        pageTitle: "Destinations - T's Travel",
        featuredDestinations: [
            { name: 'Caribbean',         emoji: '🌴' },
            { name: 'Europe',            emoji: '🗼' },
            { name: 'Cruises',           emoji: '🚢' },
            { name: 'Romantic Getaways', emoji: '💑' }
        ]
    });
});

// Contact page
app.get('/contact', (req, res) => {
    res.render('contact', {
        pageTitle: "Contact Us - T's Travel",
        businessHours: [
            { day: 'Monday - Friday', hours: '9:00 AM - 6:00 PM' },
            { day: 'Saturday',        hours: '10:00 AM - 3:00 PM' },
            { day: 'Sunday',          hours: 'Closed'              }
        ]
    });
});


// ─── Error Handlers ───────────────────────────────────────────────────────────

// 404 catch-all — must be defined after all valid routes
app.use((req, res) => {
    res.status(404).render('404', {
        pageTitle: "404 - Page Not Found | T's Travel",
        requestedUrl: req.originalUrl
    });
});

// 500 error handler — must have four parameters for Express to treat it as an error handler
app.use((err, req, res, next) => {
    console.error(err.stack);
    res.status(500).render('500', {
        pageTitle: "500 - Server Error | T's Travel"
    });
});


// ─── Start Server ─────────────────────────────────────────────────────────────

app.listen(PORT, () => {
    console.log(`Server running at http://localhost:${PORT}`);
});
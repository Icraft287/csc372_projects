/**
 * File: server.js
 * Author: Isaac Crft
 * Date: February 14, 2026
 * Description: Main JavaScript for T's Travel website.
 *
 * Original features (preserved):
 *   - Unsplash API hero background (index.php)
 *   - Unsplash API destination images (destinations.php)
 *   - Photo credit attribution (required by Unsplash guidelines)
 *   - Contact form validation and submission feedback
 *
 * New features added:
 *   #11 - Hamburger menu (keyboard support + outside-click close)
 *   #12 - Client-side blur validation on contact.php
 *   #13 - Smooth scroll for anchor links
 *   #14 - Fetch auto-fill for admin.php update form
 *   #15 - Trip type filter buttons on destinations.php
 *
 * AI help: Used AI to help structure the form validation logic, create the
 *          showError function, and make the hamburger menu work smoothly.
 *          Updated: merged new features with original Unsplash integration.
 */

// ===== UNSPLASH API CONFIGURATION =====
const UNSPLASH_ACCESS_KEY = 'BG94OREPs4vtLCNUMtKGsVfGUVsJIa_3hpKtMMPO9hs';
// Get your free API key at: https://unsplash.com/developers

// ===== UNSPLASH CACHE HELPERS =====
// Caches image URLs in localStorage for 1 hour so repeat visits
// don't burn through the 50 requests/hour free tier limit.
const CACHE_TTL = 60 * 60 * 1000; // 1 hour in milliseconds

function getCached(key) {
    try {
        const raw = localStorage.getItem('unsplash_' + key);
        if (!raw) return null;
        const entry = JSON.parse(raw);
        if (Date.now() - entry.timestamp > CACHE_TTL) {
            localStorage.removeItem('unsplash_' + key);
            return null;
        }
        return entry.data;
    } catch (e) {
        return null;
    }
}

function setCache(key, data) {
    try {
        localStorage.setItem('unsplash_' + key, JSON.stringify({
            timestamp: Date.now(),
            data: data
        }));
    } catch (e) {
        // localStorage full or unavailable — fail silently
    }
}

/* =======================================================================
   INIT - Run all features after DOM is ready
======================================================================= */
document.addEventListener('DOMContentLoaded', function () {

    // Original Unsplash features
    loadHeroBackground();
    loadDestinationImages();

    // New features
    initHamburger();
    initSmoothScroll();
    initContactValidation();
    initAdminAutoFill();
    initTripFilters();
    loadTripCardImages();
    initTripSearchSort();

});

/* =======================================================================
   UNSPLASH - HERO BACKGROUND (index.php)
   Loads a random travel photo as the hero section background.
   Photo credit is shown as required by Unsplash API guidelines.
======================================================================= */
async function loadHeroBackground() {
    const heroSection = document.getElementById('hero-section');
    if (!heroSection) return;

    if (UNSPLASH_ACCESS_KEY === 'YOUR_ACCESS_KEY_HERE') {
        console.log('Unsplash API key not configured. Using default gradient background.');
        return;
    }

    try {
        let cached = getCached('hero');
        if (!cached) {
            const response = await fetch(
                `https://api.unsplash.com/photos/random?query=travel,vacation,beach,destination&orientation=landscape&client_id=${UNSPLASH_ACCESS_KEY}`
            );
            if (!response.ok) throw new Error('Failed to fetch image from Unsplash');
            const data = await response.json();
            cached = {
                url:          data.urls.regular,
                photoUrl:     data.links.html,
                userName:     data.user.name,
                userUrl:      data.user.links.html,
            };
            setCache('hero', cached);
        }

        heroSection.style.backgroundImage =
            `linear-gradient(rgba(30, 58, 95, 0.7), rgba(42, 157, 143, 0.7)), url('${cached.url}')`;
        heroSection.style.backgroundSize     = 'cover';
        heroSection.style.backgroundPosition = 'center';
        heroSection.style.backgroundRepeat   = 'no-repeat';

        addPhotoCredit(cached.userName, cached.userUrl, cached.photoUrl);

    } catch (error) {
        console.error('Error loading Unsplash hero image:', error);
    }
}

/**
 * Add photographer attribution - required by Unsplash API guidelines.
 */
function addPhotoCredit(photographerName, photographerUrl, photoUrl) {
    const creditElement = document.getElementById('photo-credit');
    if (creditElement) {
        creditElement.innerHTML = `
            Photo by <a href="${photographerUrl}?utm_source=ts_travel&utm_medium=referral"
                        target="_blank" rel="noopener">${photographerName}</a>
            on <a href="https://unsplash.com?utm_source=ts_travel&utm_medium=referral"
                  target="_blank" rel="noopener">Unsplash</a>
        `;
        creditElement.style.display = 'block';
    }
}

/* =======================================================================
   UNSPLASH - DESTINATION IMAGES (destinations.php)
   Loads a relevant photo for each destination section using
   the data-destination attribute as the search query.
======================================================================= */
async function loadDestinationImages() {
    const imageContainers = document.querySelectorAll('.destination-image-container');
    if (!imageContainers.length) return;

    if (UNSPLASH_ACCESS_KEY === 'YOUR_ACCESS_KEY_HERE') {
        console.log('Unsplash API key not configured. Destination images will not load.');
        imageContainers.forEach(container => container.style.display = 'none');
        return;
    }

    const destinationQueries = {
        'caribbean': 'caribbean beach tropical island',
        'europe':    'europe landmark architecture',
        'cruises':   'cruise ship ocean',
        'romantic':  'maldives santorini romantic sunset',
    };

    imageContainers.forEach(async (container) => {
        const destinationType = container.getAttribute('data-destination');
        const query = destinationQueries[destinationType] || 'travel';
        const cacheKey = 'dest_' + destinationType;

        try {
            let cached = getCached(cacheKey);
            if (!cached) {
                const response = await fetch(
                    `https://api.unsplash.com/photos/random?query=${encodeURIComponent(query)}&orientation=landscape&client_id=${UNSPLASH_ACCESS_KEY}`
                );
                if (!response.ok) throw new Error('Failed to fetch image');
                const data = await response.json();
                cached = {
                    url:      data.urls.regular,
                    userName: data.user.name,
                    userUrl:  data.user.links.html,
                };
                setCache(cacheKey, cached);
            }

            const img    = container.querySelector('.destination-image');
            const credit = container.querySelector('.image-credit');

            img.src = cached.url;
            img.alt = `${destinationType} destination - Photo by ${cached.userName}`;
            img.onload = function () { img.classList.remove('loading-placeholder'); };

            if (credit) {
                credit.innerHTML = `
                    Photo by <a href="${cached.userUrl}?utm_source=ts_travel&utm_medium=referral"
                                target="_blank" rel="noopener">${cached.userName}</a>
                    on <a href="https://unsplash.com?utm_source=ts_travel&utm_medium=referral"
                          target="_blank" rel="noopener">Unsplash</a>
                `;
            }

        } catch (error) {
            console.error(`Error loading ${destinationType} image:`, error);
            container.style.display = 'none';
        }
    });
}

/* =======================================================================
   #11 - HAMBURGER MENU
   Toggles .active on .nav-links. Closes on outside click or link click.
   Added keyboard support (Enter / Space) for accessibility.
======================================================================= */
function initHamburger() {
    const hamburger = document.querySelector('.hamburger');
    const navLinks  = document.querySelector('.nav-links');
    const navbar    = document.querySelector('.navbar');

    if (!hamburger || !navLinks) return;

    hamburger.addEventListener('click', function () {
        const isOpen = navLinks.classList.toggle('active');
        hamburger.setAttribute('aria-expanded', isOpen);
    });

    hamburger.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            hamburger.click();
        }
    });

    navLinks.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
            navLinks.classList.remove('active');
            hamburger.setAttribute('aria-expanded', 'false');
        });
    });

    document.addEventListener('click', function (e) {
        if (navbar && !navbar.contains(e.target)) {
            navLinks.classList.remove('active');
            hamburger.setAttribute('aria-expanded', 'false');
        }
    });
}

/* =======================================================================
   #13 - SMOOTH SCROLL
   Intercepts same-page anchor link clicks and scrolls smoothly.
   Also smooth-scrolls if the page loads with a hash in the URL.
======================================================================= */
function initSmoothScroll() {
    document.querySelectorAll('a[href]').forEach(function (link) {
        const href = link.getAttribute('href');

        if (href && href.startsWith('#')) {
            link.addEventListener('click', function (e) {
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    history.pushState(null, '', href);
                }
            });
        }
    });

    if (window.location.hash) {
        const target = document.querySelector(window.location.hash);
        if (target) {
            setTimeout(function () {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 150);
        }
    }
}

/* =======================================================================
   #12 - CLIENT-SIDE CONTACT FORM VALIDATION
   Shows inline errors on blur before the PHP form submits.
   Also blocks submission if required fields are invalid.
   If the PHP-processed form (#contact-form) exists, attach blur
   validation. Otherwise fall back to the original alert-based handler.
======================================================================= */
function initContactValidation() {
    const contactForm = document.getElementById('contact-form');

    if (contactForm) {

        const rules = {
            name: {
                validate: function (v) { return v.trim().length >= 2 && v.trim().length <= 100; },
                message:  'Name must be between 2 and 100 characters.'
            },
            email: {
                validate: function (v) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim()); },
                message:  'Please enter a valid email address.'
            },
            phone: {
                validate: function (v) { return v.trim().length >= 7 && v.trim().length <= 20; },
                message:  'Please enter a valid phone number.'
            },
            service: {
                validate: function (v) { return v !== ''; },
                message:  'Please select a type of service.'
            },
        };

        function showInlineError(field, message) {
            var span = field.parentElement.querySelector('.client-error');
            if (!span) {
                span = document.createElement('span');
                span.className = 'error-msg client-error';
                field.parentElement.appendChild(span);
            }
            span.textContent = message;
            field.classList.add('input-error');
        }

        function clearInlineError(field) {
            var span = field.parentElement.querySelector('.client-error');
            if (span) span.remove();
            field.classList.remove('input-error');
        }

        Object.keys(rules).forEach(function (name) {
            var field = contactForm.querySelector('[name="' + name + '"]');
            if (!field) return;

            field.addEventListener('blur', function () {
                if (!rules[name].validate(field.value)) {
                    showInlineError(field, rules[name].message);
                } else {
                    clearInlineError(field);
                }
            });

            field.addEventListener('input', function () { clearInlineError(field); });
        });

        contactForm.addEventListener('submit', function (e) {
            var hasError = false;
            Object.keys(rules).forEach(function (name) {
                var field = contactForm.querySelector('[name="' + name + '"]');
                if (!field) return;
                if (!rules[name].validate(field.value)) {
                    showInlineError(field, rules[name].message);
                    if (!hasError) { field.focus(); hasError = true; }
                } else {
                    clearInlineError(field);
                }
            });
            if (hasError) e.preventDefault();
        });

        return; // PHP form found — skip the original alert-based handler
    }

    // ---------------------------------------------------------------
    // Original alert-based validation fallback (preserved)
    // ---------------------------------------------------------------
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            let isValid = true;
            const formGroups = form.querySelectorAll('.form-group');
            formGroups.forEach(group => { group.classList.remove('error'); });

            const name = document.getElementById('name');
            if (name && name.value.trim() === '') {
                showError(name, 'Please enter your name'); isValid = false;
            }

            const email = document.getElementById('email');
            if (email) {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (email.value.trim() === '') {
                    showError(email, 'Please enter your email address'); isValid = false;
                } else if (!emailPattern.test(email.value)) {
                    showError(email, 'Please enter a valid email address'); isValid = false;
                }
            }

            const phone = document.getElementById('phone');
            if (phone && phone.value.trim() === '') {
                showError(phone, 'Please enter your phone number'); isValid = false;
            }

            const service = document.getElementById('service');
            if (service && service.value === '') {
                showError(service, 'Please select a service type'); isValid = false;
            }

            if (isValid) {
                alert('Thank you! Your travel inquiry has been submitted. We will contact you within 24 hours.');
                form.reset();
            }
        });
    }
}

/* =======================================================================
   #14 - ADMIN FETCH AUTO-FILL
   When admin types an ID into the update form's Trip ID field
   (marked data-autofill="true"), fetches that trip's data from
   get_trip.php and populates all fields without a page reload.
======================================================================= */
function initAdminAutoFill() {
    const idField = document.querySelector('[data-autofill="true"]');
    if (!idField) return;

    const form = idField.closest('form');
    if (!form) return;

    let debounceTimer;

    idField.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        const id = idField.value.trim();
        if (!id || isNaN(id) || parseInt(id) < 1) return;

        debounceTimer = setTimeout(function () {
            fetch('get_trip.php?id=' + encodeURIComponent(id))
                .then(function (res) {
                    if (!res.ok) throw new Error('Not found');
                    return res.json();
                })
                .then(function (trip) {
                    var name  = form.querySelector('[name="upd_name"]');
                    var type  = form.querySelector('[name="upd_type"]');
                    var desc  = form.querySelector('[name="upd_desc"]');
                    var price = form.querySelector('[name="upd_price"]');
                    var max   = form.querySelector('[name="upd_max"]');

                    if (name)  name.value  = trip.trip_name;
                    if (type)  type.value  = trip.trip_type;
                    if (desc)  desc.value  = trip.description;
                    if (price) price.value = trip.price_per_person;
                    if (max)   max.value   = trip.max_travelers;

                    var section = document.getElementById('update-section');
                    if (section) section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                })
                .catch(function () {
                    ['upd_name','upd_type','upd_desc','upd_price','upd_max'].forEach(function (n) {
                        var el = form.querySelector('[name="' + n + '"]');
                        if (el) el.value = '';
                    });
                });
        }, 500);
    });
}

/* =======================================================================
   #15 - TRIP TYPE FILTER BUTTONS (destinations.php)
   Shows/hides trip cards by their data-type attribute without
   any page reload. Active button is highlighted with .active class.
======================================================================= */
function initTripFilters() {
    const filterBar = document.getElementById('filter-bar');
    const grid      = document.getElementById('trips-grid');

    if (!filterBar || !grid) return;

    const buttons = filterBar.querySelectorAll('.filter-btn');
    const cards   = grid.querySelectorAll('.trip-package-card');

    buttons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const filter = btn.dataset.filter;

            buttons.forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');

            cards.forEach(function (card) {
                if (filter === 'all' || card.dataset.type === filter) {
                    card.style.display = '';
                    card.style.opacity = '0';
                    requestAnimationFrame(function () {
                        card.style.transition = 'opacity 0.25s ease';
                        card.style.opacity = '1';
                    });
                } else {
                    card.style.display = 'none';
                }
            });

            const visible = grid.querySelectorAll('.trip-package-card:not([style*="display: none"])');
            let noResult  = grid.querySelector('.filter-no-result');
            if (visible.length === 0) {
                if (!noResult) {
                    noResult = document.createElement('p');
                    noResult.className = 'filter-no-result';
                    noResult.style.cssText = 'text-align:center;color:var(--text-dark);padding:20px;grid-column:1/-1;';
                    noResult.textContent = 'No packages found for this category.';
                    grid.appendChild(noResult);
                }
            } else if (noResult) {
                noResult.remove();
            }
        });
    });
}

/* =======================================================================
   HELPER - showError (original, preserved)
   Used by the fallback alert-based form validation above.
======================================================================= */
function showError(input, message) {
    const formGroup = input.closest('.form-group');
    formGroup.classList.add('error');

    let errorElement = formGroup.querySelector('.form-error');
    if (!errorElement) {
        errorElement = document.createElement('div');
        errorElement.className = 'form-error';
        formGroup.appendChild(errorElement);
    }

    errorElement.textContent = message;
}

/* =======================================================================
   UNSPLASH - TRIP CARD IMAGES (destinations.php)
   Loads a relevant photo for each trip card based on its trip type.
   Follows the same pattern as loadDestinationImages().
======================================================================= */
async function loadTripCardImages() {
    const cardImages = document.querySelectorAll('.card-trip-image');
    if (!cardImages.length) return;

    if (UNSPLASH_ACCESS_KEY === 'YOUR_ACCESS_KEY_HERE') {
        cardImages.forEach(img => img.style.display = 'none');
        return;
    }

    const typeQueries = {
        'adventure':  'adventure mountains hiking travel',
        'relaxation': 'tropical beach resort relaxation',
        'cultural':   'cultural city architecture travel',
        'family':     'family vacation fun travel',
    };

    // Deduplicate by type so we only make one API call per type
    const fetches = {};

    cardImages.forEach(function (img) {
        const type = img.getAttribute('data-type');
        if (!type || fetches[type]) return;

        const cacheKey = 'card_' + type;
        const cached   = getCached(cacheKey);

        if (cached) {
            fetches[type] = Promise.resolve(cached);
        } else {
            const query = typeQueries[type] || 'travel';
            fetches[type] = fetch(
                `https://api.unsplash.com/photos/random?query=${encodeURIComponent(query)}&orientation=landscape&client_id=${UNSPLASH_ACCESS_KEY}`
            ).then(function (res) {
                if (!res.ok) throw new Error('Failed');
                return res.json();
            }).then(function (data) {
                const entry = { url: data.urls.small, userName: data.user.name };
                setCache(cacheKey, entry);
                return entry;
            }).catch(function () { return null; });
        }
    });

    cardImages.forEach(async function (img) {
        const type = img.getAttribute('data-type');
        if (!type || !fetches[type]) return;

        try {
            const data = await fetches[type];
            if (!data) { img.style.display = 'none'; return; }
            img.src    = data.url;
            img.onload = function () { img.classList.remove('loading-placeholder'); };
        } catch (e) {
            img.style.display = 'none';
        }
    });
}

/* =======================================================================
   TRIP SEARCH + SORT BAR (destinations.php)
   Search filters cards by name in real time.
   Sort reorders cards in the grid by price.
   Both work together with the existing type filter buttons (#15).
======================================================================= */
function initTripSearchSort() {
    const searchInput = document.getElementById('trip-search');
    const sortSelect  = document.getElementById('trip-sort');
    const grid        = document.getElementById('trips-grid');

    if (!grid) return;

    function getActiveFilter() {
        const activeBtn = document.querySelector('#filter-bar .filter-btn.active');
        return activeBtn ? activeBtn.dataset.filter : 'all';
    }

    function applySearchAndSort() {
        const query      = searchInput ? searchInput.value.trim().toLowerCase() : '';
        const sortVal    = sortSelect  ? sortSelect.value : '';
        const typeFilter = getActiveFilter();

        const cards = Array.from(grid.querySelectorAll('.trip-package-card'));

        // Apply search + type filter visibility
        cards.forEach(function (card) {
            const name        = (card.querySelector('h3') || {}).textContent || '';
            const typeMatch   = typeFilter === 'all' || card.dataset.type === typeFilter;
            const searchMatch = query === '' || name.toLowerCase().includes(query);
            card.style.display = (typeMatch && searchMatch) ? '' : 'none';
        });

        // Sort visible cards by price by reinserting them in order
        if (sortVal) {
            const visible = cards.filter(c => c.style.display !== 'none');
            visible.sort(function (a, b) {
                const pa = parseFloat(a.dataset.price) || 0;
                const pb = parseFloat(b.dataset.price) || 0;
                return sortVal === 'price-asc' ? pa - pb : pb - pa;
            });
            visible.forEach(function (card) { grid.appendChild(card); });
        }

        // No-results message
        const anyVisible = cards.some(c => c.style.display !== 'none');
        let noResult = grid.querySelector('.filter-no-result');
        if (!anyVisible) {
            if (!noResult) {
                noResult = document.createElement('p');
                noResult.className = 'filter-no-result';
                noResult.style.cssText = 'text-align:center;color:var(--text-dark);padding:20px;grid-column:1/-1;';
                noResult.textContent = 'No packages match your search.';
                grid.appendChild(noResult);
            }
        } else if (noResult) {
            noResult.remove();
        }
    }

    if (searchInput) searchInput.addEventListener('input', applySearchAndSort);
    if (sortSelect)  sortSelect.addEventListener('change', applySearchAndSort);

    // Re-run after type filter buttons fire, so search stays active
    document.querySelectorAll('#filter-bar .filter-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            setTimeout(applySearchAndSort, 15);
        });
    });
}
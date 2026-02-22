// 
// File: script.js
// Author: Isaac Crft
// Date: February 14, 2026
// Description: Main JavaScript for T's Travel website – handles mobile menu toggle 
//              and basic client-side validation + submission feedback for the contact form
// AI help: Used AI to help structure the form validation logic, create the showError function, 
//          and make sure the hamburger menu toggle worked smoothly across pages.
// Updated: Added Unsplash API integration for dynamic hero backgrounds
//

// ===== UNSPLASH API CONFIGURATION =====
const UNSPLASH_ACCESS_KEY = 'BG94OREPs4vtLCNUMtKGsVfGUVsJIa_3hpKtMMPO9hs'; // Replace with your Unsplash API key

// Get your free API key at: https://unsplash.com/developers

document.addEventListener('DOMContentLoaded', function() {
    
    // ===== UNSPLASH API: Load dynamic hero background =====
    loadHeroBackground();
    
    // ===== UNSPLASH API: Load destination images =====
    loadDestinationImages();
    
    // ===== Mobile menu (hamburger) toggle =====
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');
    
    if (hamburger) {
        hamburger.addEventListener('click', function() {
            navLinks.classList.toggle('active');
        });
    }

    // ===== Contact form validation and submission handling =====
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent actual form submission (demo only)
            
            let isValid = true;
            const formGroups = form.querySelectorAll('.form-group');
            
            // Clear previous error states
            formGroups.forEach(group => {
                group.classList.remove('error');
            });
            
            // Validate name
            const name = document.getElementById('name');
            if (name && name.value.trim() === '') {
                showError(name, 'Please enter your name');
                isValid = false;
            }
            
            // Validate email
            const email = document.getElementById('email');
            if (email) {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (email.value.trim() === '') {
                    showError(email, 'Please enter your email address');
                    isValid = false;
                } else if (!emailPattern.test(email.value)) {
                    showError(email, 'Please enter a valid email address');
                    isValid = false;
                }
            }
            
            // Validate phone
            const phone = document.getElementById('phone');
            if (phone && phone.value.trim() === '') {
                showError(phone, 'Please enter your phone number');
                isValid = false;
            }
            
            // Validate service selection
            const service = document.getElementById('service');
            if (service && service.value === '') {
                showError(service, 'Please select a service type');
                isValid = false;
            }
            
            // If all required fields are valid
            if (isValid) {
                alert('Thank you! Your travel inquiry has been submitted. We will contact you within 24 hours.');
                form.reset(); // Clear the form
            }
        });
    }
});

// ===== UNSPLASH API FUNCTIONS =====

/**
 * Load a random travel-related photo from Unsplash for the hero section
 */
async function loadHeroBackground() {
    const heroSection = document.getElementById('hero-section');
    if (!heroSection) return;
    
    // If no API key is set, use a fallback gradient
    if (UNSPLASH_ACCESS_KEY === 'YOUR_ACCESS_KEY_HERE') {
        console.log('⚠️ Unsplash API key not configured. Using default gradient background.');
        console.log('Get your free API key at: https://unsplash.com/developers');
        return;
    }
    
    try {
        // Fetch a random travel photo from Unsplash
        const response = await fetch(
            `https://api.unsplash.com/photos/random?query=travel,vacation,beach,destination&orientation=landscape&client_id=${UNSPLASH_ACCESS_KEY}`
        );
        
        if (!response.ok) {
            throw new Error('Failed to fetch image from Unsplash');
        }
        
        const data = await response.json();
        
        // Apply the background image
        heroSection.style.backgroundImage = `linear-gradient(rgba(30, 58, 95, 0.7), rgba(42, 157, 143, 0.7)), url('${data.urls.regular}')`;
        heroSection.style.backgroundSize = 'cover';
        heroSection.style.backgroundPosition = 'center';
        heroSection.style.backgroundRepeat = 'no-repeat';
        
        // Add photo credit (required by Unsplash API guidelines)
        addPhotoCredit(data.user.name, data.user.links.html, data.links.html);
        
    } catch (error) {
        console.error('Error loading Unsplash image:', error);
        // Keep the default gradient background on error
    }
}

/**
 * Add photo attribution as required by Unsplash API guidelines
 */
function addPhotoCredit(photographerName, photographerUrl, photoUrl) {
    const creditElement = document.getElementById('photo-credit');
    if (creditElement) {
        creditElement.innerHTML = `
            Photo by <a href="${photographerUrl}?utm_source=ts_travel&utm_medium=referral" target="_blank" rel="noopener">${photographerName}</a> 
            on <a href="https://unsplash.com?utm_source=ts_travel&utm_medium=referral" target="_blank" rel="noopener">Unsplash</a>
        `;
        creditElement.style.display = 'block';
    }
}

/**
 * Load destination-specific images from Unsplash for destinations.html page
 */
async function loadDestinationImages() {
    const imageContainers = document.querySelectorAll('.destination-image-container');
    if (imageContainers.length === 0) return;
    
    // If no API key is set, hide the image containers
    if (UNSPLASH_ACCESS_KEY === 'YOUR_ACCESS_KEY_HERE') {
        console.log('⚠️ Unsplash API key not configured. Destination images will not load.');
        imageContainers.forEach(container => {
            container.style.display = 'none';
        });
        return;
    }
    
    // Map destination types to search queries
    const destinationQueries = {
        'caribbean': 'caribbean beach tropical island',
        'europe': 'europe landmark architecture',
        'cruise': 'cruise ship ocean',
        'romantic': 'maldives santorini romantic sunset'
    };
    
    // Load each destination image
    imageContainers.forEach(async (container) => {
        const destinationType = container.getAttribute('data-destination');
        const query = destinationQueries[destinationType] || 'travel';
        
        try {
            const response = await fetch(
                `https://api.unsplash.com/photos/random?query=${query}&orientation=landscape&client_id=${UNSPLASH_ACCESS_KEY}`
            );
            
            if (!response.ok) {
                throw new Error('Failed to fetch image from Unsplash');
            }
            
            const data = await response.json();
            const img = container.querySelector('.destination-image');
            const creditDiv = container.querySelector('.image-credit');
            
            // Set image source
            img.src = data.urls.regular;
            img.alt = `${destinationType} destination - Photo by ${data.user.name}`;
            
            // Remove loading placeholder once image loads
            img.onload = function() {
                img.classList.remove('loading-placeholder');
            };
            
            // Add photo credit
            if (creditDiv) {
                creditDiv.innerHTML = `
                    Photo by <a href="${data.user.links.html}?utm_source=ts_travel&utm_medium=referral" target="_blank" rel="noopener">${data.user.name}</a> 
                    on <a href="https://unsplash.com?utm_source=ts_travel&utm_medium=referral" target="_blank" rel="noopener">Unsplash</a>
                `;
            }
            
        } catch (error) {
            console.error(`Error loading ${destinationType} image:`, error);
            // Hide the container if image fails to load
            container.style.display = 'none';
        }
    });
}

// ===== HELPER FUNCTIONS =====

/**
 * Helper function to show error messages below inputs
 */
function showError(input, message) {
    const formGroup = input.closest('.form-group');
    formGroup.classList.add('error');
    
    // Find or create error message element
    let errorElement = formGroup.querySelector('.form-error');
    if (!errorElement) {
        errorElement = document.createElement('div');
        errorElement.className = 'form-error';
        formGroup.appendChild(errorElement);
    }
    
    errorElement.textContent = message;
}

/*
    Author: Isaac Craft
    Date: February 8, 2026
    Description: JavaScript for T's Travel Agency Website - Handles navigation 
    between different wireframe pages (homepage, services, contact) with smooth transitions 
    and scroll behavior.
    
    AI Usage: Claude AI (Anthropic) was used to assist with adding documentation comments 
    to this JavaScript file to improve code readability and maintainability.
*/

function showWireframe(pageId) {
    // Hide all wireframes
    const wireframes = document.querySelectorAll('.wireframe');
    wireframes.forEach(wf => wf.classList.remove('active'));

    // Remove active class from all tabs
    const tabs = document.querySelectorAll('.tab-btn');
    tabs.forEach(tab => tab.classList.remove('active'));

    // Show selected wireframe
    document.getElementById(pageId).classList.add('active');

    // Highlight active tab
    event.target.classList.add('active');

    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
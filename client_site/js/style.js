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
// Listen for the DOM to be fully loaded before running any scripts
document.addEventListener('DOMContentLoaded', function() {

    // --- Keyboard Shortcut for Search (Ctrl+K / Cmd+K) ---
    const searchInput = document.getElementById('search');

    // Only add the event listener if the search input exists on the current page
    if (searchInput) {
        window.addEventListener('keydown', function(event) {
            // Check for Ctrl+K on Windows/Linux or Cmd+K on macOS
            if ((event.ctrlKey || event.metaKey) && event.key === 'k') {
                event.preventDefault(); // Prevent the browser's default action
                searchInput.focus();    // Focus the search input field
            }
        });
    }

    // You can add other global JavaScript functions or initializations here
    // as the application grows.

});

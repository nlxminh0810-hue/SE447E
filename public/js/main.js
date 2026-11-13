// ENGLISHAI Main JavaScript

// Navigation dropdown
document.addEventListener('DOMContentLoaded', function() {
    // Add dropdown functionality if needed
    console.log('ENGLISHAI loaded');
});

// Search courses
function searchCourses() {
    const searchInput = document.getElementById('searchCourse');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            // TODO: Implement course search
            console.log('Search:', this.value);
        });
    }
}

// Filter courses
function filterCourses() {
    const filterLevel = document.getElementById('filterLevel');
    if (filterLevel) {
        filterLevel.addEventListener('change', function() {
            // TODO: Implement course filter
            console.log('Filter:', this.value);
        });
    }
}

// Initialize
searchCourses();
filterCourses();

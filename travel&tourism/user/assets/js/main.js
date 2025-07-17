// Trae Travel User Section - Main JavaScript File

document.addEventListener('DOMContentLoaded', function() {
    // Smooth scrolling for navigation links
    const navLinks = document.querySelectorAll('a.nav-link[href^="#"]');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });

    // Form validation helper
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Tooltip initialization (Bootstrap)
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Simple search functionality for tours/packages
    const searchInput = document.getElementById('search-input');
    const searchableItems = document.querySelectorAll('.searchable-item');

    if (searchInput && searchableItems.length) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            searchableItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }

    // Price range slider (if exists)
    const priceRangeSlider = document.getElementById('price-range');
    const priceDisplay = document.getElementById('price-display');

    if (priceRangeSlider && priceDisplay) {
        priceRangeSlider.addEventListener('input', function() {
            priceDisplay.textContent = `₹ ${this.value}`;
        });
    }

    // Simple image lazy loading
    const lazyImages = document.querySelectorAll('img.lazy');
    const lazyImageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const lazyImage = entry.target;
                lazyImage.src = lazyImage.dataset.src;
                lazyImage.classList.remove('lazy');
                observer.unobserve(lazyImage);
            }
        });
    });

    lazyImages.forEach(img => lazyImageObserver.observe(img));
}); 
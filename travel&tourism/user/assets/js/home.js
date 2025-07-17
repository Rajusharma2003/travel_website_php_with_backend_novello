// Home Page Specific JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Hero Carousel Auto-advance with pause on hover
    const heroCarousel = document.getElementById('heroCarousel');
    const carousel = new bootstrap.Carousel(heroCarousel, {
        interval: 5000,  // 5 seconds
        ride: 'carousel'
    });

    heroCarousel.addEventListener('mouseenter', () => {
        carousel.pause();
    });

    heroCarousel.addEventListener('mouseleave', () => {
        carousel.cycle();
    });

    // Featured Tours Hover Effect
    const tourCards = document.querySelectorAll('#featured-tours .card');
    tourCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
            this.style.zIndex = '10';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.zIndex = 'auto';
        });
    });

    // Contact Form Dynamic Validation
    const contactForm = document.getElementById('contact-form');
    const nameInput = contactForm.querySelector('input[name="name"]');
    const emailInput = contactForm.querySelector('input[name="email"]');
    const messageInput = contactForm.querySelector('textarea[name="message"]');

    function validateInput(input, regex, errorMessage) {
        const errorSpan = input.nextElementSibling || document.createElement('span');
        errorSpan.className = 'text-danger small';
        
        if (!regex.test(input.value)) {
            input.classList.add('is-invalid');
            errorSpan.textContent = errorMessage;
            input.parentNode.insertBefore(errorSpan, input.nextSibling);
            return false;
        } else {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            if (errorSpan) errorSpan.remove();
            return true;
        }
    }

    nameInput.addEventListener('input', function() {
        validateInput(this, /^[a-zA-Z\s]{2,50}$/, 'Name must be 2-50 characters long and contain only letters');
    });

    emailInput.addEventListener('input', function() {
        validateInput(this, /^[^\s@]+@[^\s@]+\.[^\s@]+$/, 'Please enter a valid email address');
    });

    messageInput.addEventListener('input', function() {
        validateInput(this, /^.{10,500}$/, 'Message must be between 10-500 characters');
    });

    contactForm.addEventListener('submit', function(e) {
        const isNameValid = validateInput(nameInput, /^[a-zA-Z\s]{2,50}$/, 'Name must be 2-50 characters long and contain only letters');
        const isEmailValid = validateInput(emailInput, /^[^\s@]+@[^\s@]+\.[^\s@]+$/, 'Please enter a valid email address');
        const isMessageValid = validateInput(messageInput, /^.{10,500}$/, 'Message must be between 10-500 characters');

        if (!isNameValid || !isEmailValid || !isMessageValid) {
            e.preventDefault();
        }
    });
}); 
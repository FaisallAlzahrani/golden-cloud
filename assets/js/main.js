// Main JavaScript for Golden Resort

document.addEventListener('DOMContentLoaded', function() {
    // Mobile Menu Toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mainNav = document.querySelector('.main-nav');
    
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
            const icon = mobileMenuToggle.querySelector('i');
            if (icon.classList.contains('fa-bars')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    }
    
    // Smooth scrolling for anchor links
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                // Close mobile menu if open
                if (mainNav.classList.contains('active')) {
                    mainNav.classList.remove('active');
                    const icon = mobileMenuToggle.querySelector('i');
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
                
                // Scroll to the target element
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Form validation for contact form
    const contactForm = document.getElementById('contactForm');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            let isValid = true;
            const nameInput = document.getElementById('name');
            const emailInput = document.getElementById('email');
            const messageInput = document.getElementById('message');
            
            // Validate name
            if (!nameInput.value.trim()) {
                isValid = false;
                showInputError(nameInput, 'Please enter your name');
            } else {
                removeInputError(nameInput);
            }
            
            // Validate email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailInput.value.trim() || !emailRegex.test(emailInput.value)) {
                isValid = false;
                showInputError(emailInput, 'Please enter a valid email address');
            } else {
                removeInputError(emailInput);
            }
            
            // Validate message
            if (!messageInput.value.trim()) {
                isValid = false;
                showInputError(messageInput, 'Please enter your message');
            } else {
                removeInputError(messageInput);
            }
            
            // Prevent form submission if validation fails
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    // Helper function to show input error
    function showInputError(input, message) {
        const formGroup = input.closest('.form-group');
        formGroup.classList.add('has-error');
        
        // Remove any existing error message
        const existingError = formGroup.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
        
        // Add new error message
        const errorElement = document.createElement('div');
        errorElement.className = 'error-message';
        errorElement.textContent = message;
        formGroup.appendChild(errorElement);
    }
    
    // Helper function to remove input error
    function removeInputError(input) {
        const formGroup = input.closest('.form-group');
        formGroup.classList.remove('has-error');
        
        const existingError = formGroup.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
    }
    
    // Image lightbox for gallery - Improved implementation
    const galleryItems = document.querySelectorAll('.gallery-item img');
    
    // Create a style element for lightbox styles
    const lightboxStyles = document.createElement('style');
    lightboxStyles.textContent = `
        .lightbox {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 999999 !important; /* Extra high z-index */
        }
        .lightbox-content {
            position: relative;
            max-width: 90%;
            max-height: 90%;
        }
        .lightbox-content img {
            max-width: 100%;
            max-height: 90vh;
            border: 3px solid #fff;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        }
        .lightbox-close {
            position: absolute;
            top: -40px;
            right: 0;
            color: #fff;
            font-size: 30px;
            cursor: pointer;
        }
    `;
    document.head.appendChild(lightboxStyles);
    
    galleryItems.forEach(item => {
        item.addEventListener('click', function(e) {
            // Prevent the default action (important for Fancybox conflict)
            e.preventDefault();
            e.stopPropagation();
            
            const lightbox = document.createElement('div');
            lightbox.className = 'lightbox';
            
            const lightboxContent = document.createElement('div');
            lightboxContent.className = 'lightbox-content';
            
            const img = document.createElement('img');
            img.src = this.src;
            
            const closeBtn = document.createElement('span');
            closeBtn.className = 'lightbox-close';
            closeBtn.innerHTML = '&times;';
            
            lightboxContent.appendChild(img);
            lightboxContent.appendChild(closeBtn);
            lightbox.appendChild(lightboxContent);
            
            // Append to body ensures it's outside any container that might have overflow:hidden
            document.body.appendChild(lightbox);
            
            // Force the lightbox to be on top of everything
            lightbox.style.display = 'flex';
            lightbox.style.zIndex = '999999';
            
            // Close lightbox when clicking close button or outside the image
            lightbox.addEventListener('click', function(e) {
                if (e.target === lightbox || e.target === closeBtn) {
                    document.body.removeChild(lightbox);
                }
            });
            
            // Close on escape key
            document.addEventListener('keydown', function escHandler(e) {
                if (e.key === 'Escape') {
                    document.body.removeChild(lightbox);
                    document.removeEventListener('keydown', escHandler);
                }
            });
        });
    });
});

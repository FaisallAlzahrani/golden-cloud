    </main>
<footer class="main-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3><?php echo __('Contact Us'); ?></h3>
                <p><i class="fas fa-map-marker-alt"></i> <?php echo getSetting('address'); ?></p>
                <p><i class="fas fa-phone"></i> <?php echo getSetting('contact_phone'); ?></p>
                <p><i class="fas fa-envelope"></i> <?php echo getSetting('contact_email'); ?></p>
            </div>
            <div class="footer-section">
                <h3><?php echo __('Quick Links'); ?></h3>
                <ul>
                    <li><a href="#home"><?php echo __('Home'); ?></a></li>
                    <li><a href="#about"><?php echo __('About'); ?></a></li>
                    <li><a href="#gallery"><?php echo __('Gallery'); ?></a></li>
                    <li><a href="#contact"><?php echo __('Contact'); ?></a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3><?php echo __('Follow Us'); ?></h3>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-pinterest"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
        
        <p>&copy; <?php echo date('Y'); ?> <?php echo getSetting('site_title'); ?>. <?php echo __('All Rights Reserved.'); ?></p>
         <p>&copy; <?php echo __('Devloped By FaisalAlzahrani.'); ?></p>
    </div>
    </div>
</footer>
    
<script src="<?php echo (dirname($_SERVER['PHP_SELF']) == '/' ? '' : dirname($_SERVER['PHP_SELF'])) ?>/assets/js/main.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<style>
    /* Critical FancyBox fixes applied directly in the footer */
    .fancybox__container {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100% !important;
        height: 100% !important;
        z-index: 99999 !important;
    }
    
    .fancybox__backdrop {
        z-index: 99990 !important;
        background-color: rgba(0, 0, 0, 0.9) !important;
    }
    
    .fancybox__carousel {
        z-index: 99995 !important;
    }
    
    .fancybox__button {
        opacity: 1 !important;
        visibility: visible !important;
    }
</style>
<script>
    // Initialize Fancybox with body-level attachment
    document.addEventListener('DOMContentLoaded', function() {
        // Remove any previous instances
        if (typeof Fancybox !== 'undefined' && Fancybox.getInstance()) {
            Fancybox.close(true);
        }
        
        // Configure with improved settings
        Fancybox.bind("[data-fancybox]", {
            // Force attachment to document body
            appendTo: document.body,
            
            // Enable navigation
            showClass: "fancybox-fadeIn",
            hideClass: "fancybox-fadeOut",
            
            // UI options
            hideScrollbar: true,
            compact: false,
            
            // Must have options
            Image: { zoom: true },
            Hash: false,
            
            Carousel: {
                infinite: true,
                Navigation: {
                    nextTpl: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>',
                    prevTpl: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-left"><polyline points="15 18 9 12 15 6"></polyline></svg>'
                }
            },
            
            // Localization
            l10n: {
                CLOSE: "<?php echo __('Close'); ?>",
                NEXT: "<?php echo __('Next'); ?>",
                PREV: "<?php echo __('Previous'); ?>",
                MODAL: "<?php echo __('You can close this modal content with the ESC key'); ?>",
                ERROR: "<?php echo __('Something Went Wrong, Please Try Again Later'); ?>",
                IMAGE_ERROR: "<?php echo __('Image Not Found'); ?>",
                ELEMENT_NOT_FOUND: "<?php echo __('Element Not Found'); ?>",
                AJAX_NOT_FOUND: "<?php echo __('Error Loading AJAX : Not Found'); ?>",
                AJAX_FORBIDDEN: "<?php echo __('Error Loading AJAX : Forbidden'); ?>",
                IFRAME_ERROR: "<?php echo __('Error Loading Page'); ?>"
            }
        });
    });
</script>
</body>
</html>

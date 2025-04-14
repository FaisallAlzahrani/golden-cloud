<?php
require_once __DIR__ . '/includes/functions.php';
include __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section id="home" class="hero" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('<?php echo getSetting('hero_image'); ?>');">
    <div class="container">
        <div class="hero-content">
           <h1><?php echo __('Welcome to Ghaima Resort'); ?></h1>
            <p><?php echo __('Experience luxury and comfort in our exclusive resort destination'); ?></p>
            
            <div class="button-wrapper">
                <a href="#contact" class="btn"><?php echo __('Book Now'); ?></a>
                <div class="visitor-badge">
                    <i class="fas fa-users"></i> <?php echo __('Visitors'); ?>: 
                    <span class="visitor-count-number">
                    <?php echo number_format(getVisitorCount()); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="section">
    <div class="container">
        <h2 class="section-title"><?php echo __('About Us'); ?></h2>
        <div class="about-content">
            <div class="about-text">
                <p><?php echo __('Welcome to Ghaima Resort – your ideal destination for relaxation and unforgettable moments. We offer a variety of accommodation options, including single rooms, one-bedroom suites, two-bedroom suites, and a traditional gathering lounge (Dewaniya) to ensure our guests enjoy a comfortable stay.'); ?></p>
                <p><?php echo __('Our commitment to excellence is reflected in every detail of our resort – from thoughtfully designed interiors to personalized services tailored to meet all your needs. Whether you are here for a romantic getaway, a family vacation, or a business trip, Ghaima Resort promises an experience that exceeds your expectations.'); ?></p>
                <p><?php echo __('Enjoy the highest levels of comfort and luxury. Our goal is to provide a unique and memorable experience, where guests can relax in a serene atmosphere surrounded by nature\'s beauty. We look forward to welcoming you to our resort!'); ?></p>
                <a href="#gallery" class="btn"><?php echo __('View Our Gallery'); ?></a>
            </div>
            <div class="about-image">
                <img src="<?php echo getSetting('about_image'); ?>" alt="Ghaima Resort Exterior" onerror="this.src='https://placehold.co/600x400/D4AF37/fff?text=Ghaima+Resort'">
            </div>
        </div>
    </div>
</section>

<!-- Gallery Section -->
<section id="gallery" class="section" style="background-color: var(--gray-light);">
    <div class="container">
        <h2 class="section-title"><?php echo __('Our Gallery'); ?></h2>
        <div class="gallery-container">
            <?php
            $galleryImages = getRecentGalleryImages(6);
            
            if (empty($galleryImages)) {
                // Display placeholder images if no images in database
                for ($i = 1; $i <= 6; $i++) {
                    echo '<div class="gallery-item">
                            <a href="https://placehold.co/1200x800/D4AF37/fff?text=Resort+Image+' . $i . '" data-fancybox="gallery" data-caption="' . __('Beautiful View') . ' ' . $i . '">
                                <img src="https://placehold.co/600x400/D4AF37/fff?text=Resort+Image+' . $i . '" alt="Resort Image">
                                <div class="gallery-caption">
                                    <h3>' . __('Beautiful View') . ' ' . $i . '</h3>
                                    <p>' . __('Experience the luxury of our resort') . '</p>
                                </div>
                            </a>
                        </div>';
                }
            } else {
                foreach ($galleryImages as $image) {
                    echo '<div class="gallery-item">
                            <a href="' . $image['image_path'] . '" data-fancybox="gallery" data-caption="' . $image['title'] . '">
                                <img src="' . $image['image_path'] . '" alt="' . $image['title'] . '">
                                <div class="gallery-caption">
                                    <h3>' . $image['title'] . '</h3>
                                    <p>' . $image['description'] . '</p>
                                </div>
                            </a>
                        </div>';
                }
            }
            ?>
        </div>
        <div style="text-align: center; margin-top: 30px;">
            <a href="#" class="btn"><?php echo __('View More'); ?></a>
        </div>
    </div>
</section>

<!-- Amenities Section -->
<section class="section">
    <div class="container">
        <h2 class="section-title"><?php echo __('Our Amenities'); ?></h2>
        <div class="amenities-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 30px; text-align: center;">
            <div class="amenity-item">
                <i class="fas fa-tree" style="font-size: 48px; color: var(--primary-color); margin-bottom: 15px;"></i>
                <h3><?php echo __('Outdoor Garden'); ?></h3>
                <p><?php echo __('Relax in our beautiful garden surrounded by nature'); ?></p>
            </div>
            <div class="amenity-item">
                <i class="fas fa-child" style="font-size: 48px; color: var(--primary-color); margin-bottom: 15px;"></i>
                <h3><?php echo __('Children\'s Playground'); ?></h3>
                <p><?php echo __('Fun and safe play area for your little ones'); ?></p>
            </div>
            <div class="amenity-item">
                <i class="fas fa-coffee" style="font-size: 48px; color: var(--primary-color); margin-bottom: 15px;"></i>
                <h3><?php echo __('Coffee Shop'); ?></h3>
                <p><?php echo __('Enjoy freshly brewed coffee and delicious snacks'); ?></p>
            </div>
            <div class="amenity-item">
                <i class="fas fa-fire" style="font-size: 48px; color: var(--primary-color); margin-bottom: 15px;"></i>
                <h3><?php echo __('Barbecue Area'); ?></h3>
                <p><?php echo __('Perfect spot for grilling and outdoor dining'); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="section" style="background-color: var(--gray-light);">
    <div class="container">
        <h2 class="section-title"><?php echo __('Contact Us'); ?></h2>
        <div class="contact-content">
            <div class="contact-info">
                <h3><?php echo __('Get In Touch'); ?></h3>
                <div class="contact-info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <h4><?php echo __('Location'); ?></h4>
                        <p><?php echo getSetting('address'); ?></p>
                    </div>
                </div>
                <div class="contact-info-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <h4><?php echo __('Phone'); ?></h4>
                        <p><?php echo getSetting('contact_phone'); ?></p>
                    </div>
                </div>
                <div class="contact-info-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <h4><?php echo __('Email'); ?></h4>
                        <p><?php echo getSetting('contact_email'); ?></p>
                    </div>
                </div>
                <div class="contact-info-item">
                    <i class="fab fa-whatsapp"></i>
                    <div>
                        <h4><?php echo __('WhatsApp'); ?></h4>
                        <p><?php echo getSetting('contact_whatsapp'); ?></p>
                    </div>
                </div>
            </div>
            <div class="contact-form">
                <form id="contactForm" method="post" action="process_contact.php">
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success"><?php echo __('Your message has been sent successfully. We\'ll get back to you soon!'); ?></div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <input type="text" id="name" name="name" class="form-control" placeholder="<?php echo __('Your Name'); ?>">
                    </div>
                    <div class="form-group">
                        <input type="email" id="email" name="email" class="form-control" placeholder="<?php echo __('Your Email'); ?>">
                    </div>
                    <div class="form-group">
                        <input type="text" id="subject" name="subject" class="form-control" placeholder="<?php echo __('Subject'); ?>">
                    </div>
                    <div class="form-group">
                        <textarea id="message" name="message" class="form-control" placeholder="<?php echo __('Your Message'); ?>"></textarea>
                    </div>
                    <button type="submit" class="btn"><?php echo __('Send Message'); ?></button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

# Golden Cloud Resort Website

A professional bilingual (English/Arabic) resort website with an admin dashboard, featuring gold accent colors and modern design.

## Features

- **Bilingual Support**: Full English and Arabic language support with RTL layout
- **Responsive Design**: Looks great on all devices (desktop, tablet, mobile)
- **Single Page Website**: All sections (Home, About, Gallery, Contact) in one page with smooth scrolling
- **Admin Dashboard**: Full control over website content and settings
- **User Management**: Create and manage users with different roles (admin/user)
- **Gallery Management**: Upload and manage images with modern lightbox view
- **Visitor Counter**: Track and display visitor counts with cookie-based unique visitor tracking
- **WhatsApp Integration**: Direct contact button in the bottom left corner
- **Custom Amenities**: Showcasing Outdoor Garden, Children's Playground, Coffee Shop, and Barbecue Area
- **Theme Customization**: Change colors and other settings

## Setup Instructions

### Requirements

- PHP 7.4 or higher
- MySQL database
- Web server (Apache, Nginx, etc.)

### Installation

1. **Database Setup**:
   - Create a MySQL database
   - Copy `config.example.php` to `config.php` and update with your credentials
   - Import the `database.sql` file (if available) or let the system create tables automatically

2. **Web Server Configuration**:
   - Place the files in your web server's document root
   - Ensure the web server has write permissions for the `assets/uploads` directory

3. **Initial Access**:
   - The system will automatically create an admin user on first access
   - Default admin credentials:
     - Username: admin
     - Password: admin123
   - **Important**: Change the default password immediately after first login

## Directory Structure

```
golden-resort/
├── admin/              # Admin dashboard files
├── assets/             # Static assets
│   ├── css/            # Stylesheets
│   ├── js/             # JavaScript files
│   ├── images/         # Static images
│   └── uploads/        # User uploaded images
├── config/             # Configuration files
├── includes/           # PHP include files
└── index.php           # Main website file
```

## Admin Dashboard Usage

1. **Login**: Access the admin panel via the login link or directly at `/login.php`
2. **Dashboard**: View statistics and recent activity
3. **Users**: Manage website users (create, edit, delete)
4. **Gallery**: Upload and manage images for the gallery section
5. **Settings**: Customize website colors, title, and contact information

## Security Notes

- Change the default admin password immediately after installation
- Regularly update permissions and passwords
- Consider implementing additional security measures for production use

## Customization

- Primary color and background colors can be changed in the admin settings
- Additional styling can be modified in `assets/css/style.css`
- JavaScript functionality can be extended in `assets/js/main.js`

---

Created for Golden Resort by Codeium

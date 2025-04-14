# GitHub Hosting Guide for Golden Cloud Resort Website

This guide explains how to use GitHub with your PHP-based, bilingual Golden Cloud resort website.

## Important: GitHub Pages Limitations

GitHub Pages **does not support PHP or MySQL databases**. Since your Golden Cloud website uses both PHP and MySQL, you have several options:

## Option 1: GitHub as Code Repository + Traditional Web Hosting

This is the recommended approach for your PHP-based website.

### Step 1: Create a GitHub Repository

1. Create a GitHub account if you don't have one at [github.com](https://github.com)
2. Create a new repository named "golden-cloud"
3. Don't initialize with README (you already have one)

### Step 2: Prepare Your Local Repository

```bash
# Navigate to your project directory
cd c:\xampp\htdocs\golden-cloud

# Initialize Git repository
git init

# Create a .gitignore file to exclude sensitive information
```

### Step 3: Create a .gitignore File

Create a `.gitignore` file in your project root with:

```
# Exclude configuration with sensitive data
config.php

# Exclude database dumps
*.sql

# Exclude user uploads (optional, if they're large)
# assets/uploads/*

# Exclude cache/tmp files
*.log
.DS_Store
Thumbs.db
```

### Step 4: Commit and Push Your Code

```bash
# Add all files
git add .

# Make initial commit
git commit -m "Initial commit of Golden Cloud website"

# Add GitHub as remote origin
git remote add origin https://github.com/YOUR-USERNAME/golden-cloud.git

# Push to GitHub
git push -u origin main
```

### Step 5: Deploy to a PHP-Compatible Host

Deploy your site to a hosting provider that supports PHP and MySQL, such as:

- [Hostinger](https://www.hostinger.com/)
- [HostGator](https://www.hostgator.com/)
- [Bluehost](https://www.bluehost.com/)
- [SiteGround](https://www.siteground.com/)
- [InMotion Hosting](https://www.inmotionhosting.com/)

Most of these providers support direct deployment from GitHub repositories.

## Option 2: GitHub + GitHub Actions + PHP Host

Use GitHub Actions to automatically deploy your code to a PHP-compatible host whenever you push changes.

### Setup GitHub Actions Workflow

Create a file `.github/workflows/deploy.yml`:

```yaml
name: Deploy to Web Host

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v2
    
    - name: FTP Deploy
      uses: SamKirkland/FTP-Deploy-Action@4.3.0
      with:
        server: ${{ secrets.FTP_SERVER }}
        username: ${{ secrets.FTP_USERNAME }}
        password: ${{ secrets.FTP_PASSWORD }}
        server-dir: public_html/
```

Then add your FTP credentials as GitHub secrets in your repository settings.

## Option 3: Convert to Static Site (Limited Functionality)

If you only need to showcase the website design without dynamic functionality:

1. Create static HTML versions of all pages
2. Remove PHP and database dependencies
3. Host directly on GitHub Pages

Note: This will remove functionality like:
- Visitor counter
- Admin panel
- Dynamic content management
- Contact form processing

## Maintaining Your Bilingual Website

When using GitHub for version control:

1. **Commits**: Make clear commit messages, especially for bilingual changes
2. **Branches**: Consider using branches for major changes or new features
3. **Documentation**: Keep documentation updated for both English and Arabic content
4. **Collaboration**: If others will help with development, provide clear guidelines for working with the bilingual system

## Backup Considerations

1. Regularly back up your database
2. Export the database and store it securely (not in the public GitHub repository)
3. Consider automated backup solutions for your production database

## Resources

- [GitHub Documentation](https://docs.github.com/)
- [Git for Beginners](https://www.atlassian.com/git/tutorials/what-is-version-control)
- [GitHub Actions Documentation](https://docs.github.com/en/actions)

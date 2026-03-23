# Tamil Nadu Vanigargalin Sangamam - Member ID Card System

## Overview

A Laravel-based membership management system with a WhatsApp-style chat interface for member registration. The system validates voters using EPIC numbers, generates digital member ID cards with QR codes, and provides an admin panel for management.

## Table of Contents

1. [Project Overview](#project-overview)
2. [Features](#features)
3. [Technology Stack](#technology-stack)
4. [Documentation Index](#documentation-index)
5. [Quick Links](#quick-links)

---

## Project Overview

**Project Name:** Tamil Nadu Vanigargalin Sangamam Membership System  
**Version:** 1.0  
**Framework:** Laravel 11  
**PHP Version:** 8.2+  
**Production URL:** https://vanigan.digital  
**Admin Panel:** https://vanigan.digital/admin/login

### Purpose

This system allows Tamil Nadu voters to:
- Register as members using their EPIC (Voter ID) number
- Verify their identity via OTP
- Upload their photo
- Generate a digital member ID card
- Access their card via QR code
- Refer other members

---

## Features

### User Features

1. **Mobile Registration**
   - WhatsApp-style chat interface
   - Mobile number validation (Indian format)
   - OTP verification via voice call

2. **EPIC Validation**
   - Real-time voter database lookup
   - Searches across 234 assembly constituencies
   - Validates against Tamil Nadu voter data

3. **Photo Upload**
   - Face detection validation
   - Cloudinary integration for storage
   - Image optimization

4. **Member Card Generation**
   - Digital ID card with photo
   - QR code for verification
   - Downloadable format
   - Unique member ID (VNG-XXXXXXX)

5. **Referral System**
   - Generate referral links
   - Track referrals
   - Referral rewards

### Admin Features

1. **Dashboard**
   - Total members count
   - Recent registrations
   - Statistics overview

2. **Member Management**
   - View all members
   - Search by unique ID
   - View member details
   - Member card preview

3. **Voter Lookup**
   - Search by EPIC number
   - View voter details
   - Assembly and district information

4. **Secure Authentication**
   - Password-protected admin panel
   - Session management

---

## Technology Stack

### Backend
- **Framework:** Laravel 11
- **Language:** PHP 8.2+
- **Package Manager:** Composer

### Databases
- **MySQL:** Voter data (read-only, 234 tables)
- **MongoDB:** Member data storage (Atlas)

### External Services
- **Cloudinary:** Image storage (2 accounts)
- **2Factor.in:** OTP service (voice call)
- **Upstash Redis:** Cache (optional)

### Frontend
- **CSS Framework:** Tailwind CSS (via CDN)
- **JavaScript:** Vanilla JS
- **UI Style:** WhatsApp-inspired chat interface

### Infrastructure
- **Server:** Cloudways
- **Web Server:** Apache/Nginx
- **SSL:** HTTPS enabled

---

## Documentation Index

### Getting Started
- [Installation Guide](INSTALLATION.md) - Setup and deployment
- [Configuration Guide](CONFIGURATION.md) - Environment setup
- [Quick Start](QUICK_START.md) - Get running in 5 minutes

### Development
- [Architecture Overview](ARCHITECTURE.md) - System design
- [Database Schema](DATABASE.md) - Database structure
- [API Documentation](API.md) - API endpoints
- [Code Structure](CODE_STRUCTURE.md) - File organization

### Operations
- [Deployment Guide](DEPLOYMENT.md) - Production deployment
- [Maintenance Guide](MAINTENANCE.md) - Ongoing maintenance
- [Troubleshooting](TROUBLESHOOTING.md) - Common issues
- [Backup & Recovery](BACKUP.md) - Data protection

### Reference
- [Environment Variables](ENV_VARIABLES.md) - All .env settings
- [Admin Guide](ADMIN_GUIDE.md) - Admin panel usage
- [User Guide](USER_GUIDE.md) - End-user instructions
- [Security Guide](SECURITY.md) - Security best practices

---

## Quick Links

### Production
- **Website:** https://vanigan.digital
- **Admin Panel:** https://vanigan.digital/admin/login
- **API Base:** https://vanigan.digital/api/vanigam

### Credentials
- **Admin Username:** admin
- **Admin Password:** admin
- **Database:** hkqbnymdjz (234 voter tables)
- **MongoDB:** vanigan database

### Key Files
- **Main Controller:** `app/Http/Controllers/VanigamController.php`
- **Admin Controller:** `app/Http/Controllers/AdminPanelController.php`
- **MongoDB Service:** `app/Services/MongoService.php`
- **Voter Helper:** `app/Helpers/VoterHelper.php`

---

## Project Statistics

- **Total Routes:** 35
- **API Endpoints:** 17
- **Admin Routes:** 8
- **Voter Tables:** 234 (Tamil Nadu assemblies)
- **Total Voters:** ~5 million records
- **Controllers:** 2 main controllers
- **Services:** 9 service classes
- **Models:** 8 models

---

## System Requirements

### Server Requirements
- PHP >= 8.2
- Composer
- MySQL 5.7+ or MariaDB 10.3+
- MongoDB PHP Extension
- GD or Imagick PHP Extension
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- Ctype PHP Extension
- JSON PHP Extension

### External Services
- MongoDB Atlas account
- Cloudinary account (2 accounts)
- 2Factor.in API key
- SSL certificate (for HTTPS)

---

## Support & Contact

### Documentation
All documentation is available in the `/docs` folder.

### Issues
For issues and bug reports, check the [Troubleshooting Guide](TROUBLESHOOTING.md).

### Updates
Check [CHANGELOG.md](CHANGELOG.md) for version history and updates.

---

## License

This project is proprietary software for Tamil Nadu Vanigargalin Sangamam.

---

## Version History

- **v1.0.0** (March 2026) - Initial production release
  - EPIC validation
  - Member card generation
  - Admin panel
  - QR code system
  - Referral system

---

**Last Updated:** March 19, 2026  
**Status:** Production Ready ✅

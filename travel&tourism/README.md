# Travel Management System Structure

## Overview
This document outlines the structure of a modern PHP travel management system with separate admin and user interfaces.

## Project Structure

/travel-management-system/
│
├── admin/
│   ├── index.php               - Admin login page
│   ├── dashboard.php           - Admin dashboard
│   ├── tours/                  - Tour management
│   │   ├── add_tour.php
│   │   ├── edit_tour.php
│   │   └── view_tours.php
│   ├── bookings/               - Booking management
│   │   ├── view_bookings.php
│   │   ├── booking_details.php
│   │   └── update_status.php   - For handling booking status
│   ├── users/                  - User management
│   │   ├── manage_users.php
│   │   └── user_details.php
│   ├── messages/               - Contact form messages
│   │   └── view_messages.php   - View user contact messages
│   ├── settings/               - System settings
│   │   ├── site_settings.php
│   │   └── payment_settings.php
│   └── includes/               - Admin includes
│       ├── admin_header.php
│       ├── admin_footer.php
│       ├── admin_functions.php
│       └── auth_functions.php  - Admin authentication
│
├── user/
│   ├── index.php              - Home page
│   ├── about.php              - About page
│   ├── packages.php           - Tour packages page
│   ├── gallery.php            - Photo gallery
│   ├── contact.php            - Contact page with form
│   ├── tours/                 - User tour section
│   │   ├── browse_tours.php
│   │   └── tour_details.php
│   ├── bookings/              - User bookings
│   │   ├── my_bookings.php
│   │   ├── book_tour.php
│   │   └── booking_confirmation.php
│   ├── account/               - User account
│   │   ├── login.php
│   │   ├── register.php
│   │   ├── profile.php
│   │   └── change_password.php
│   └── includes/              - User includes
│       ├── header.php
│       ├── footer.php
│       └── functions.php
│
├── assets/                    - Shared assets
│   ├── css/
│   ├── js/
│   ├── images/
│   └── uploads/               - For tour images and gallery
│
└── includes/                  - Global includes
    ├── config.php             - Database/config
    ├── db_connect.php         - DB connection
    └── global_functions.php   - Shared functions
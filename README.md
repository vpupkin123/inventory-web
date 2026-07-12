# 📋 Inventory Web

A lightweight, self-contained web application for tracking computer equipment in organizations. Optimized for Synology NAS with zero external dependencies.

## About the Project

**Inventory Web** solves the problem of IT asset management by automating the intake of hardware data. Client machines generate JSON reports containing complete hardware specifications, which are uploaded to the system. The application parses the data, filters out irrelevant devices (like USB drives), and queues the computers for administrative processing.

Administrators can review new hardware, assign equipment to employees, automatically create user accounts with standardized logins, and maintain a complete history of all hardware transfers. The system is designed to be simple, reliable, and easy to maintain.

## Key Features

#### Automated Intake

Parses JSON hardware reports and automatically filters out USB and removable drives.

#### 👤 Smart User Creation

Automatically generates user logins from full names using GOST R 7.0.34-2014 transliteration standards, with built-in collision handling.

#### 📋 Queue Processing

Dedicated interface for reviewing new hardware, adding comments, and configuring new users before finalizing assignment.

#### 🔄 Asset Tracking

Assign computers to specific employees or keep them on a virtual warehouse with full transfer history.

#### 📊 Excel Export

One-click export of the computer registry and transfer history for reporting purposes.

#### 🌍 Multi-language

Full support for English and Russian interfaces with easy switching.

## Repository Structure

The project follows a clean, MVC-like architecture with clear separation of concerns:

**admin-scripts/** — Shell scripts for server maintenance and permissions management  
**config/** — Application constants, database paths, and URL routing rules  
**controllers/** — PHP classes handling logic for each page (Auth, Dashboard, Reports, Processing, Computers, Users, etc.)  
**core/** — Application engine: custom router, database handler, authentication, autoloader, and internationalization  
**data/** — Runtime data folder with SQLite database, error logs, and uploaded JSON reports  
**docs/** — HTML documentation files for users and administrators (English and Russian)  
**lang/** — Translation files for English and Russian interfaces  
**public/** — Web server document root with single entry point and URL rewriting rules  
**services/** — Utility classes including JSON parser and GOST transliteration engine  
**views/** — HTML templates for all pages, organized by feature

## Documentation

Comprehensive documentation is provided in the **docs/** folder. All guides are standalone HTML files with built-in styling and navigation, available in both English and Russian.

#### 📖 User Guide

For daily users and administrators. Covers logging in, uploading reports, processing queue, transferring computers, managing users, and exporting data.

[🇬🇧 English Version](docs/en_user-guide.html) [🇷🇺 Russian Version](docs/ru_user-guide.html)

#### 🛠️ Admin Guide

For system administrators and developers. Covers technology stack, requirements, installation, database schema, permissions, backups, and troubleshooting.

[🇬🇧 English Version](docs/en_admin-guide.html) [🇷🇺 Russian Version](docs/ru_admin-guide.html)

## Administration Scripts

Routine maintenance tasks are handled by shell scripts in the **admin-scripts/** folder. These scripts are universal and can be configured via arguments or environment variables.

### Available Scripts

*   **fix-permissions.sh** — Restores correct file and folder ownership and permissions. Essential to run after pulling updates from the repository to ensure the web server can write to the database and uploads folder.
*   **reset-inventory.sh** — Clears the database of all test data (computers, reports, and transfer history) while preserving user accounts. Useful for resetting the environment during testing phases.

## Technology Stack

The application is built with minimal dependencies for maximum reliability and ease of deployment:

PHP 8.0+ SQLite 3 HTML5 CSS3 JavaScript

**No external dependencies.** No frameworks, no Composer, no CDN — everything works autonomously.

**Inventory Web** — Developed by [vpupkin123](https://github.com/vpupkin123)

Source code: [github.com/vpupkin123/inventory-web](https://github.com/vpupkin123/inventory-web)
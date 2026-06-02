# Daycare‑final‑new

A lightweight PHP web application for managing a daycare centre – staff, children, attendance, meals, invoices and notifications. The project provides separate admin and user interfaces, a simple relational database schema, and a clean CSS layout.

---

## Overview
`Daycare-final-new` is a complete, ready‑to‑run solution for small‑to‑medium daycare operations. It allows administrators to:

- Register and edit staff and child records  
- Track daily attendance and meal plans  
- Generate and view invoices and payments  
- Send and receive notifications  
- Manage user authentication and session handling  

The front‑end is built with plain HTML/CSS, while the back‑end logic resides in PHP scripts that interact with a MySQL database.

---

## Features
| ✅ | Feature |
|---|---|
| ✔️ | **Admin Dashboard** – Central hub (`admin_home.php`) with navigation, statistics and quick links |
| ✔️ | **Staff Management** – Add, edit, view, and delete staff (`add_staff.php`, `edit_staff.php`, `view_staff.php`) |
| ✔️ | **Child Management** – Register, edit, and view children (`add_child.php`, `edit_child.php`, `view_children.php`) |
| ✔️ | **Attendance & Meals** – Record daily attendance and meal choices (`view_attendance.php`, `meals.php`) |
| ✔️ | **Invoice & Payments** – Create, view and update invoices (`manage_invoice.php`, `payments.php`) |
| ✔️ | **Notifications** – Send admin‑to‑staff messages (`notifications.php`, `admin_reply.php`) |
| ✔️ | **Authentication** – Secure login/logout for admins and staff (`admin_login.php`, `login.php`, `logout.php`) |
| ✔️ | **Responsive Layout** – Simple CSS (`css/style.css`) with a reusable navigation bar (`navbar.php`, `admin_navbar.php`) |
| ✔️ | **Support Contact** – Form for users to request assistance (`contact_support.php`) |

---

## Tech Stack
| Layer | Technology |
|-------|------------|
| **Backend** | PHP ≥ 7.4 |
| **Database** | MySQL (SQL dump: `Database/daycarenew_db.sql`) |
| **Frontend** | HTML5, CSS3 |
| **Server** | Apache / Nginx (any LAMP stack) |
| **Version Control** | Git (GitHub) |

---

## Installation

### Prerequisites
1. **Web server** with PHP support (Apache, Nginx, etc.)  
2. **MySQL** server (or MariaDB)  
3. Composer (optional – only if you plan to add third‑party packages)

### Steps
1. **Clone the repository**  
   ```bash
   git clone https://github.com/yourusername/Daycare-final-new.git
   cd Daycare-final-new
   ```

2. **Create the database**  
   - Import the provided SQL dump:  
     ```bash
     mysql -u root -p < Database/daycarenew_db.sql
     ```
   - Adjust the database name, user and password in the configuration files:
     - `config.php`
     - `admin/config.php`

3. **Configure PHP**  
   Ensure the following extensions are enabled in `php.ini`:
   - `mysqli`
   - `openssl`
   - `session`

4. **Set file permissions** (if using Apache)  
   ```bash
   sudo chown -R www-data:www-data .
   sudo chmod -R 755 .
   ```

5. **Start the server**  
   - For a quick test, you can use PHP’s built‑in server:  
     ```bash
     php -S localhost:8000
     ```
   - Visit `http://localhost:8000/index.php` in your browser.

### Configuration
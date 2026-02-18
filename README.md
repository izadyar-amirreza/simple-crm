# Simple CRM (Laravel) — RBAC + Ownership + Soft Delete

A simple CRM built with Laravel that demonstrates **role-based access control (RBAC)**, **resource ownership enforcement**, and a clean CRUD experience for **Customers, Leads, Tasks**, plus an **Activity Log**.

> Designed as a portfolio project: fast setup, demo seed data, and automated tests / CI.

---

## ✨ Features

- Authentication (Laravel Breeze)
- Roles & permissions (Spatie Laravel Permission)
- Ownership rules:
  - Sales users can only access their own records
  - Admin can access all records
- CRUD modules:
  - Customers
  - Leads (with **Convert to Customer**)
  - Tasks (attach to Customer/Lead)
- Soft Deletes + Trash/Restore
- Activity Log
- Automated tests (feature tests proving RBAC/ownership)

---

## 🧱 Tech Stack

- **Laravel 12** (PHP 8.2+)
- Laravel Sail (Docker)
- Spatie Laravel Permission
- Laravel Breeze (auth scaffolding)
- Vite + TailwindCSS

---

## ✅ Demo Accounts (Seeded)

After seeding, you can log in with:

- **Admin**
  - Email: `izadyaramirreza0@gmail.com`
  - Password: `Admin@123456`

- **Sales**
  - Email: `sales@example.com`
  - Password: `Sales@123456`

---

## 🚀 Quick Start (Laravel Sail)

### 1) Clone and install dependencies
```bash
git clone https://github.com/izadyar-amirreza/simple-crm.git
cd simple-crm

composer install
npm install


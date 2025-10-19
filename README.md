# 🚀 Laravel + React + Docker Application

This repository contains a **full-stack Dockerized application** built with:
- **Laravel (PHP 8.3)** — Backend REST API  
- **React + Vite** — Frontend UI  
- **Docker Compose** — Container orchestration  

---

## 📁 Project Structure

```
root/
├── backend/    # Laravel 12 (PHP + Composer)
├── frontend/   # React + Vite (Node + npm)
└── docker/     # Nginx, MySQL configurations
```

---

## ⚙️ Environment Configuration

1. Go to the **backend** folder.
2. Duplicate `.env.example` and rename it to `.env`.
3. Open `.env` and fill in the following values:

```env
ZOHO_CLIENT_ID=
ZOHO_CLIENT_SECRET=
ZOHO_ORGANIZATION_ID= 
``` 

---

## 🐳 Docker Setup

**Step 1 — Build the Containers**
```bash
docker compose build
```
**Step 2 — Start the Containers**
```bash
docker compose up -d
```
**Step 3 — Enter the App Container**
```bash
docker compose exec app bash
```
**Step 4 — Run Migrations**
```bash
php artisan migrate:fresh
```

---

## 🧩 Available Services

| Service   | Description                    | Port  |
|-----------|-------------------------------|-------|
| app       | Laravel PHP-FPM application   | —     |
| nginx     | Web server for Laravel        | 8080  |
| mysql     | MySQL database                | 3306  |
| redis     | Redis cache                   | 6379  |
| frontend  | React + Vite dev server       | 5173  |

You can access Laravel at [http://localhost:8080](http://localhost:8080)  
And React at [http://localhost:5173](http://localhost:5173)

---

## 🔐 Zoho Integration

This project includes Zoho Books API integration.
Make sure to:
- Obtain your Client ID and Client Secret from the Zoho Developer Console.
- Retrieve your Organization ID from the Zoho Books settings page.
- Add these values to your `.env` file before starting the backend.

---

## 🛠 Common Commands

```bash
# Rebuild containers after changes
docker compose build --no-cache

# Restart containers
docker compose down && docker compose up -d

# Access MySQL CLI
docker compose exec mysql mysql -u root -p

# Clear Laravel cache
php artisan config:clear && php artisan cache:clear && php artisan route:clear
```

---

## 🧾 Notes
- Ensure Docker and Docker Compose are installed on your system.
- For frontend development, you can also run React locally using:

```bash
cd frontend
npm install
npm run dev
```
- All uploaded files and generated assets are stored under `backend/storage/`.

---

## 📄 License
This project is licensed under the MIT License.
Feel free to fork, modify, and contribute.

---

Developed by:
👤 Abid Maqbool  
Full Stack Software Developer
📧 [abidmaqbool20@gmail.com]  
🌐 https://github.com/abidmaqbool20






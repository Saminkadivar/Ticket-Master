# 🎟️ Ticket Master — Online Movie Ticket Booking Website

Welcome to **Ticket Master**, a user-friendly, modern, responsive movie ticket booking website where you can discover movies, check details, and book tickets with ease.

---

## 📌 Features

✨ **Key Features:**

- 🎬 Browse **Upcoming** & **Now Showing** movies
- 🗂️ See movie details — title, genre, release date
- 🖼️ Movie images & default fallback images
- 📅 Dynamic upcoming/released movie filtering
- 🛒 Ticket booking flow (extendable)
- 🛡️ Secure PHP sessions
- 🧑‍💻 Admin panel to manage:
  - Movies
  - Theaters
  - Showtimes
  - Seats
  - Reports
  - Booking details
- 📊 Booking & revenue reports
- 📱 Fully responsive layout using **Bootstrap 5**
- ⚡ Clean UI animations & hover effects

---

## 🛠️ Tech Stack

- **Frontend:** HTML5, CSS3, Bootstrap 5, JS
- **Backend:** PHP 7+ (or 8+ recommended)
- **Database:** MySQL
- **Extras:** jQuery (optional), Carousel, Font Awesome

---

## 📂 Folder Structure

/ticket-master
├── /admin # Admin dashboard & management
├── /includes # Header, footer, sidebar, DB connection
├── /uploads # Uploaded movie images & fallback images
├── carousel.php # Homepage carousel
├── connection.php # Database connection config
├── index.php # Homepage (Upcoming & Released Movies)
├── movie_details.php # Single movie page
└── README.md # This file!


---

## ⚙️ Installation & Setup

**Step 1:** 📥 Clone this repository

```bash
git clone https://github.com/YOUR_USERNAME/ticket-master.git
cd ticket-master

Step 2: 🗃️ Import the MySQL Database

Open phpMyAdmin or your MySQL tool.

Create a database called ticket_master.

Import the provided .sql file if available (e.g., ticket_master.sql).

Step 3: ⚙️ Configure Database Connection

Open connection.php and update with your DB credentials:

Step 4: 🚀 Run Locally

Place the project folder in htdocs (XAMPP) or www (WAMP).

Start Apache & MySQL.

Visit: http://localhost/ticket-master

💻 Usage
Visit the homepage to see Upcoming & Released movies.

Click any movie card for details.

Extend the booking logic as needed.

Use the /admin panel to add/edit/delete movies, manage theaters, and view bookings.

🧩 Screenshots
Homepage (Upcoming)	Movie Card

📷 Add your real screenshots in uploads/screens and link them here.

📝 License
This project is free for learning & personal use.
Please don’t use it for commercial purposes without permission.

👨‍💻 Author
Created by [YOUR NAME].
Connect with me on LinkedIn.
Feel free to reach out for any help or collaboration!

# Smart Library Management System

A complete, working **PHP (OOP) + MySQL** library management system with a modern
white/blue/gray dashboard UI, live AJAX search, borrowing workflows, student
profiles, a content-based book recommendation engine, similarity matching, and
Google Books API import.

Built as a second-year Object-Oriented Programming (OOP) academic project.

---

## 1. Requirements

- **PHP 8.0+** with the `pdo_mysql`, `curl`, and `fileinfo` extensions enabled
- **MySQL 5.7+** or **MariaDB 10.3+**
- A local server stack: **XAMPP**, **WAMP**, **MAMP**, or Laragon (Windows/Mac/Linux)
- A modern browser (Chrome, Edge, Firefox)

> This is a real server-side PHP application — it must run on a PHP+MySQL
> server (like XAMPP) on your own computer. It cannot run inside a plain
> browser or a chat window.

---

## 2. Installation (XAMPP — step by step)

1. **Install XAMPP** from https://www.apachefriends.org if you don't have it.
2. **Copy the project folder.** Place the entire `SmartLibrary` folder inside
   your XAMPP `htdocs` directory, e.g.:
   ```
   C:\xampp\htdocs\SmartLibrary        (Windows)
   /Applications/XAMPP/htdocs/SmartLibrary   (macOS)
   /opt/lampp/htdocs/SmartLibrary      (Linux)
   ```
3. **Start Apache and MySQL** from the XAMPP Control Panel.
4. **Create the database:**
   - Open **phpMyAdmin**: http://localhost/phpmyadmin
   - Click **Import** → **Choose File** → select
     `SmartLibrary/database/smart_library.sql`
   - Click **Go**. This creates the `smart_library` database, all tables,
     and sample data (books, students, borrowing history, admin accounts).

   Alternatively, from a terminal:
   ```bash
   mysql -u root -p < database/smart_library.sql
   ```

5. **Check the database config.** Open `config/config.php` and confirm these
   match your MySQL setup (XAMPP defaults shown, usually no change needed):
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'smart_library');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

6. **Set the base URL.** If your project folder is not named `SmartLibrary`,
   or you're using a virtual host, update:
   ```php
   define('BASE_URL', '/SmartLibrary'); // change to match your folder name
   ```

7. **Make the uploads folders writable** (usually automatic on
   Windows/XAMPP; on Linux/Mac run):
   ```bash
   chmod -R 775 uploads/
   ```

8. **Open the app** in your browser:
   ```
   http://localhost/SmartLibrary/
   ```
   You'll be redirected to the login page.

---

## 3. Default Login Credentials

The sample data includes ready-to-use accounts:

| Role              | Email                          | Password       |
|-------------------|----------------------------------|----------------|
| Admin / Librarian | `admin@library.edu`              | `admin123`     |
| Admin / Librarian | `librarian@library.edu`          | `librarian123` |
| Student           | `juan.delacruz@student.edu`      | `student123`   |
| Student           | `maria.santos@student.edu`       | `student123`   |
| Student           | `angelo.reyes@student.edu`       | `student123`   |
| Student           | `kristine.lim@student.edu`       | `student123`   |
| Student           | `paolo.cruz@student.edu`         | `student123`   |
| Student           | `nadine.torres@student.edu`      | `student123`   |

Admin/Librarian accounts can manage the full catalog, students, and imports.
Student accounts can browse, search, borrow/return their own books, and view
personalized recommendations.

---

## 4. Google Books API Configuration (optional but recommended)

The **Import from Google Books** page (admin only) uses the public Google
Books API. Basic searching works out of the box with **no API key**, but
Google rate-limits anonymous requests. For heavier use:

1. Go to https://console.cloud.google.com/
2. Create a project → enable **"Books API"**
3. Create an **API key** (Credentials → Create Credentials → API key)
4. Open `config/config.php` and paste it in:
   ```php
   define('GOOGLE_BOOKS_API_KEY', 'YOUR_API_KEY_HERE');
   ```

If your server has no internet access, or the key/network isn't configured,
the search will show a friendly "could not reach Google Books API" message —
you can still add books manually through **Book Catalog → Add Book**.

---

## 5. Feature Overview

- **Authentication** — secure bcrypt password hashing, PHP sessions, CSRF
  tokens on all state-changing requests, admin vs. student roles.
- **Dashboard** — live stats (total/available/borrowed/returned books,
  active students, overdue loans), recent activity feed, quick actions.
- **Book Catalog** — full CRUD (add/edit/delete/view), cover image upload,
  category tagging, live AJAX search & filter, pagination.
- **Borrowing** — borrow/return workflow with automatic copy-count
  adjustment, due dates, automatic overdue detection, fine calculation.
- **Student Profiles** — student info, borrowing history, favorite
  categories (derived from history), personalized recommendations.
- **Search** — instant AJAX search by title, author, ISBN, publisher, or
  category from both the top navbar and the catalog page.
- **Recommendation Dashboard** — personalized picks, popular books,
  recently borrowed, and trending categories (bar chart).
- **Content-Based Filtering & Similarity Matching** — a weighted
  similarity algorithm (`classes/Recommendation.php`) compares category,
  author, keywords/tags, publisher, and description to compute a
  similarity percentage and rank the Top 5 similar books per title.
- **Google Books Integration** — search Google's catalog and import a
  book (with cover, ISBN, description, categories) into your library in
  one click.
- **Security** — 100% prepared statements (PDO), `password_hash()` /
  `password_verify()`, CSRF tokens, input sanitization/escaping (`e()`),
  MIME-type validated file uploads.

---

## 6. Project Structure

```
SmartLibrary/
├── config/          Global config + PDO Database singleton
├── classes/         OOP models: User, Book, Student, Borrow, Recommendation, GoogleBooks
├── includes/         Shared partials: header, sidebar, footer, auth guard, helpers
├── api/              JSON AJAX endpoints (CRUD, search, borrow/return, recommendations)
├── views/            Page templates (dashboard, catalog, borrowing, students, etc.)
├── assets/css/js/    Stylesheet + JavaScript (vanilla JS, no build step)
├── assets/images/    Default cover/avatar placeholders (SVG)
├── uploads/          User-uploaded book covers & profile pictures (+ sample covers)
├── database/         smart_library.sql — full schema + seed data
├── index.php         Entry point (redirects to login or dashboard)
├── login.php-like    views/login.php — login form
└── logout.php        Destroys session and redirects to login
```

### OOP design notes
- **Encapsulation** — each class (`Book`, `Student`, `Borrow`, …) owns its
  private `PDO` connection and exposes only clean public methods.
- **Abstraction** — controllers/views never write raw SQL; they call model
  methods like `$bookModel->search()` or `$borrowModel->borrowBook()`.
- **Single Responsibility** — `GoogleBooks` only talks to the external API,
  `Recommendation` only computes scores, `Borrow` only manages loan state.
- **Reusability** — shared helpers (`e()`, `formatDate()`, `statusBadge()`,
  `calculateFine()`) live in `includes/functions.php` and are used everywhere.

---

## 7. Troubleshooting

| Problem | Likely fix |
|---|---|
| Blank page / "Database connection failed" | Check `config/config.php` DB credentials; make sure MySQL is running. |
| CSS/JS not loading, broken layout | `BASE_URL` in `config/config.php` doesn't match your folder/vhost name. |
| "Access denied" on admin pages while logged in as student | Expected — those pages require an admin/librarian account. |
| Cover image won't upload | Check `uploads/covers` folder permissions (`chmod 775` on Linux/Mac). |
| Google Books search fails | No internet access on the server, or `curl` extension disabled in `php.ini`. |
| Login fails with correct password | Re-import `database/smart_library.sql` — password hashes must match exactly. |

---

## 8. License / Use

This project was generated as an educational OOP academic project. Feel free
to modify, extend, and reuse it for coursework or portfolio purposes.
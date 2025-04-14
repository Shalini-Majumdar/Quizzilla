# Quizzilla

Quizzilla is a PHP-MySQL based dynamic quiz platform where users can create, attempt, and manage quizzes. Designed with Bootstrap for a clean user interface, it's perfect for educators, trainers, or anyone looking to gamify knowledge sharing.

---

## Features

- User registration & login with session management
- Dashboard with quiz search, cards, and add button
- Create/Edit quizzes with unlimited questions
- Dynamic question addition/removal with validation
- Attempt quizzes with multiple-choice questions
- Instant score and answer review after submission
- User profile management with profile picture upload
- View quiz history and results
- Responsive layout using Bootstrap 5
- Secure (prepared statements, input sanitization)

---

## Tech Stack

- PHP (Core)
- MySQL (via `mysqli`)
- HTML5
- CSS3
- JavaScript
- Bootstrap 5

---

## Setup Instructions

1. **Clone the repo or copy files:**
   ```bash
   git clone https://github.com/Shalini-Majumdar/Quizzilla.git
2. **Create the MySQL database:**   
    Use the provided `quiz_app.sql` to import tables.

3. **Update DB credentials in db_connect.php:**   
    $conn = new mysqli('localhost', 'root', '', 'quiz_app');

4. **Start local server (e.g., XAMPP/WAMP):**   
    Access at http://localhost/quizzilla

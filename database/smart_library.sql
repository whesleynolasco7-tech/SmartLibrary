-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 02, 2026 at 08:35 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smart_library`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(10) UNSIGNED NOT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `year_published` smallint(5) UNSIGNED DEFAULT NULL,
  `language` varchar(50) DEFAULT 'English',
  `total_copies` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `available_copies` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `cover_image` varchar(255) DEFAULT NULL,
  `tags` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `isbn`, `title`, `author`, `publisher`, `category_id`, `description`, `year_published`, `language`, `total_copies`, `available_copies`, `cover_image`, `tags`, `created_at`, `updated_at`) VALUES
(1, '9780061120084', 'To Kill a Mockingbird', 'Harper Lee', 'J. B. Lippincott & Co.', 2, 'A gripping tale of racial injustice and childhood innocence in the American South, told through the eyes of Scout Finch.', 1960, 'English', 4, 2, 'cover_01.svg', 'classic, justice, coming-of-age, southern-gothic', '2026-07-03 02:12:58', '2026-07-03 02:17:13'),
(2, '9780451524935', '1984', 'George Orwell', 'Secker & Warburg', 3, 'A dystopian vision of a totalitarian future where the Party watches everything and independent thought is a crime.', 1949, 'English', 3, 2, 'cover_02.svg', 'dystopia, totalitarianism, surveillance, politics', '2026-07-03 02:12:58', '2026-07-03 02:12:58'),
(3, '9780141439518', 'Pride and Prejudice', 'Jane Austen', 'T. Egerton', 2, 'A witty exploration of manners, marriage, and morality in Georgian England, centered on Elizabeth Bennet and Mr. Darcy.', 1813, 'English', 3, 3, 'cover_03.svg', 'romance, classic, wit, society', '2026-07-03 02:12:58', '2026-07-03 02:12:58'),
(4, '9780743273565', 'The Great Gatsby', 'F. Scott Fitzgerald', 'Charles Scribner\'s Sons', 2, 'A tragic story of wealth, love, and the American Dream set in the roaring twenties on Long Island.', 1925, 'English', 3, 2, 'cover_04.svg', 'classic, american-dream, jazz-age, tragedy', '2026-07-03 02:12:58', '2026-07-03 02:12:58'),
(5, '9780262033848', 'Introduction to Algorithms', 'Thomas H. Cormen', 'MIT Press', 5, 'The comprehensive, rigorous reference on algorithms and data structures, widely used in computer science courses.', 2009, 'English', 5, 4, 'cover_05.svg', 'algorithms, data-structures, programming, computer-science', '2026-07-03 02:12:58', '2026-07-03 02:22:15'),
(6, '9780132350884', 'Clean Code', 'Robert C. Martin', 'Prentice Hall', 5, 'A handbook of agile software craftsmanship, teaching principles and practices for writing readable, maintainable code.', 2008, 'English', 4, 3, 'cover_06.svg', 'programming, software-engineering, best-practices, clean-code', '2026-07-03 02:12:58', '2026-07-03 02:12:58'),
(7, '9780547928227', 'The Hobbit', 'J.R.R. Tolkien', 'George Allen & Unwin', 4, 'Bilbo Baggins is swept into an epic quest to reclaim a lost kingdom from the dragon Smaug.', 1937, 'English', 4, 3, 'cover_07.svg', 'fantasy, adventure, quest, dragons', '2026-07-03 02:12:58', '2026-07-03 02:12:58'),
(8, '9780553380163', 'A Brief History of Time', 'Stephen Hawking', 'Bantam Books', 7, 'An accessible exploration of cosmology, black holes, and the nature of time and the universe.', 1988, 'English', 3, 3, 'cover_08.svg', 'physics, cosmology, science, universe', '2026-07-03 02:12:58', '2026-07-03 02:23:32'),
(9, '9780316769488', 'The Catcher in the Rye', 'J.D. Salinger', 'Little, Brown and Company', 2, 'Holden Caulfield\'s cynical, restless journey through New York City after being expelled from prep school.', 1951, 'English', 3, 2, 'cover_09.svg', 'classic, coming-of-age, alienation, youth', '2026-07-03 02:12:58', '2026-07-03 02:12:58'),
(10, '9780133970777', 'Database System Concepts', 'Abraham Silberschatz', 'McGraw-Hill', 5, 'A foundational textbook covering relational databases, SQL, transactions, and database system architecture.', 2019, 'English', 4, 2, 'cover_10.svg', 'database, sql, computer-science, systems', '2026-07-03 02:12:58', '2026-07-03 02:17:23'),
(11, '9780062316097', 'Sapiens: A Brief History of Humankind', 'Yuval Noah Harari', 'Harvill Secker', 9, 'A sweeping narrative of how Homo sapiens came to dominate the world, from the Cognitive Revolution to today.', 2011, 'English', 3, 2, 'cover_11.svg', 'history, anthropology, evolution, civilization', '2026-07-03 02:12:58', '2026-07-03 02:12:58'),
(12, '9780062315007', 'The Alchemist', 'Paulo Coelho', 'HarperTorch', 8, 'A shepherd boy\'s journey to fulfill his personal legend teaches timeless lessons about following one\'s dreams.', 1988, 'English', 4, 3, 'cover_12.svg', 'self-help, inspiration, journey, philosophy', '2026-07-03 02:12:58', '2026-07-03 02:12:58'),
(13, '9780201633610', 'Design Patterns', 'Erich Gamma, Richard Helm, Ralph Johnson, John Vlissides', 'Addison-Wesley', 5, 'The classic \"Gang of Four\" catalog of reusable object-oriented software design patterns.', 1994, 'English', 3, 2, 'cover_13.svg', 'programming, oop, design-patterns, software-architecture', '2026-07-03 02:12:58', '2026-07-03 02:12:58'),
(14, '9781305952300', 'Physics for Scientists and Engineers', 'Raymond A. Serway', 'Cengage Learning', 7, 'A comprehensive introductory physics textbook covering mechanics, thermodynamics, and electromagnetism.', 2018, 'English', 3, 3, 'cover_14.svg', 'physics, science, mechanics, engineering', '2026-07-03 02:12:58', '2026-07-03 02:12:58'),
(15, '9780439708180', 'Harry Potter and the Sorcerer\'s Stone', 'J.K. Rowling', 'Scholastic', 4, 'An orphaned boy discovers he is a wizard and begins his magical education at Hogwarts School.', 1997, 'English', 5, 4, 'cover_15.svg', 'fantasy, magic, adventure, coming-of-age', '2026-07-03 02:12:58', '2026-07-03 02:12:58'),
(16, '9780735211292', 'Atomic Habits', 'James Clear', 'Avery', 8, 'A practical, proven framework for building good habits and breaking bad ones, one tiny change at a time.', 2018, 'English', 4, 3, 'cover_16.svg', 'self-help, habits, productivity, psychology', '2026-07-03 02:12:58', '2026-07-03 02:12:58'),
(17, '9780307474278', 'The Da Vinci Code', 'Dan Brown', 'Doubleday', 10, 'A murder in the Louvre and clues hidden in the works of Da Vinci lead to a religious mystery of global scale.', 2003, 'English', 3, 2, 'cover_17.svg', 'mystery, thriller, conspiracy, religion', '2026-07-03 02:12:58', '2026-07-03 02:12:58'),
(18, '9780321929123', 'Learning Web Design', 'Jennifer Robbins', 'O\'Reilly Media', 11, 'A beginner-friendly guide to HTML, CSS, JavaScript, and modern responsive web design fundamentals.', 2018, 'English', 3, 2, 'cover_18.svg', 'web-design, html, css, ui-ux', '2026-07-03 02:12:58', '2026-07-03 02:12:58');

-- --------------------------------------------------------

--
-- Table structure for table `borrowing_records`
--

CREATE TABLE `borrowing_records` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `book_id` int(10) UNSIGNED NOT NULL,
  `borrow_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `status` enum('borrowed','returned','overdue') NOT NULL DEFAULT 'borrowed',
  `fine_amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `fine_status` enum('none','unpaid','paid') NOT NULL DEFAULT 'none',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrowing_records`
--

INSERT INTO `borrowing_records` (`id`, `student_id`, `book_id`, `borrow_date`, `due_date`, `return_date`, `status`, `fine_amount`, `fine_status`, `created_at`) VALUES
(1, 1, 5, '2026-06-30', '2026-07-07', NULL, 'borrowed', 0.00, 'none', '2026-07-03 02:12:58'),
(2, 1, 7, '2026-06-13', '2026-06-20', '2026-06-21', 'returned', 5.00, 'paid', '2026-07-03 02:12:58'),
(3, 1, 13, '2026-05-24', '2026-05-31', '2026-05-31', 'returned', 0.00, 'none', '2026-07-03 02:12:58'),
(4, 2, 6, '2026-07-01', '2026-07-08', NULL, 'borrowed', 0.00, 'none', '2026-07-03 02:12:58'),
(5, 2, 10, '2026-06-18', '2026-06-25', '2026-06-26', 'returned', 5.00, 'paid', '2026-07-03 02:12:58'),
(6, 2, 15, '2026-05-04', '2026-05-11', '2026-05-14', 'returned', 15.00, 'paid', '2026-07-03 02:12:58'),
(7, 3, 8, '2026-06-21', '2026-06-28', '2026-07-03', 'returned', 25.00, 'unpaid', '2026-07-03 02:12:58'),
(8, 3, 14, '2026-06-03', '2026-06-10', '2026-06-11', 'returned', 5.00, 'paid', '2026-07-03 02:12:58'),
(9, 4, 16, '2026-07-02', '2026-07-09', NULL, 'borrowed', 0.00, 'none', '2026-07-03 02:12:58'),
(10, 4, 12, '2026-06-08', '2026-06-15', '2026-06-16', 'returned', 5.00, 'paid', '2026-07-03 02:12:58'),
(11, 5, 15, '2026-06-28', '2026-07-05', NULL, 'borrowed', 0.00, 'none', '2026-07-03 02:12:58'),
(12, 5, 5, '2026-05-19', '2026-05-26', '2026-05-28', 'returned', 10.00, 'paid', '2026-07-03 02:12:58'),
(13, 6, 17, '2026-06-29', '2026-07-06', NULL, 'borrowed', 0.00, 'none', '2026-07-03 02:12:58'),
(14, 6, 1, '2026-05-29', '2026-06-05', '2026-06-06', 'returned', 5.00, 'paid', '2026-07-03 02:12:58'),
(15, 6, 9, '2026-04-24', '2026-05-01', '2026-05-02', 'returned', 5.00, 'paid', '2026-07-03 02:12:58'),
(16, 6, 4, '2026-04-04', '2026-04-11', '2026-04-13', 'returned', 10.00, 'paid', '2026-07-03 02:12:58'),
(17, 1, 1, '2026-07-03', '2026-07-10', NULL, 'borrowed', 0.00, 'none', '2026-07-03 02:17:13'),
(18, 1, 10, '2026-07-03', '2026-07-10', NULL, 'borrowed', 0.00, 'none', '2026-07-03 02:17:23'),
(19, 3, 5, '2026-07-03', '2026-07-10', '2026-07-03', 'returned', 0.00, 'none', '2026-07-03 02:21:58');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'Fiction', 'Novels and fictional literature'),
(2, 'Classic Literature', 'Timeless literary classics'),
(3, 'Science Fiction', 'Speculative and futuristic fiction'),
(4, 'Fantasy', 'Fantasy and magical worlds'),
(5, 'Computer Science', 'Programming, algorithms, and software engineering'),
(6, 'Mathematics', 'Mathematics and applied math'),
(7, 'Science', 'Physics, biology, and general science'),
(8, 'Self-Help', 'Personal development and self-improvement'),
(9, 'History', 'Historical non-fiction'),
(10, 'Mystery & Thriller', 'Mystery, crime, and thriller novels'),
(11, 'Design', 'Web design, UI/UX and visual design'),
(12, 'Philosophy', 'Philosophy and critical thought');

-- --------------------------------------------------------

--
-- Table structure for table `imported_books`
--

CREATE TABLE `imported_books` (
  `id` int(10) UNSIGNED NOT NULL,
  `book_id` int(10) UNSIGNED NOT NULL,
  `google_volume_id` varchar(100) DEFAULT NULL,
  `imported_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `imported_books`
--

INSERT INTO `imported_books` (`id`, `book_id`, `google_volume_id`, `imported_at`) VALUES
(1, 11, 'FSKFDwAAQBAJ', '2026-07-03 02:12:58'),
(2, 16, 'sVs7EAAAQBAJ', '2026-07-03 02:12:58');

-- --------------------------------------------------------

--
-- Table structure for table `recommendations`
--

CREATE TABLE `recommendations` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `book_id` int(10) UNSIGNED NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `score` decimal(5,2) NOT NULL DEFAULT 0.00,
  `generated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recommendations`
--

INSERT INTO `recommendations` (`id`, `student_id`, `book_id`, `reason`, `score`, `generated_at`) VALUES
(1, 1, 6, 'Similar to previously borrowed Computer Science titles', 62.50, '2026-07-03 02:12:58'),
(2, 1, 10, 'Matches your interest in Computer Science', 55.00, '2026-07-03 02:12:58'),
(3, 2, 13, 'Popular among students in your course', 58.00, '2026-07-03 02:12:58'),
(4, 3, 5, 'Matches your interest in Computer Science', 60.00, '2026-07-03 02:12:58'),
(5, 4, 8, 'Trending in Self-Help category', 40.00, '2026-07-03 02:12:58'),
(6, 5, 13, 'Similar to previously borrowed Fantasy/Programming titles', 58.75, '2026-07-03 02:12:58'),
(7, 6, 3, 'Matches your interest in Classic Literature', 66.00, '2026-07-03 02:12:58');

-- --------------------------------------------------------

--
-- Table structure for table `similarity_scores`
--

CREATE TABLE `similarity_scores` (
  `id` int(10) UNSIGNED NOT NULL,
  `book_id_a` int(10) UNSIGNED NOT NULL,
  `book_id_b` int(10) UNSIGNED NOT NULL,
  `score` decimal(5,2) NOT NULL DEFAULT 0.00,
  `computed_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `similarity_scores`
--

INSERT INTO `similarity_scores` (`id`, `book_id_a`, `book_id_b`, `score`, `computed_at`) VALUES
(1, 5, 6, 62.50, '2026-07-03 02:12:58'),
(2, 5, 10, 55.00, '2026-07-03 02:12:58'),
(3, 5, 13, 58.75, '2026-07-03 02:12:58'),
(4, 6, 13, 71.25, '2026-07-03 02:12:58'),
(5, 7, 15, 68.00, '2026-07-03 02:12:58'),
(6, 1, 9, 47.50, '2026-07-03 02:12:58'),
(7, 1, 4, 45.00, '2026-07-03 02:12:58');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `student_number` varchar(30) NOT NULL,
  `course` varchar(150) DEFAULT NULL,
  `year_level` varchar(30) DEFAULT NULL,
  `contact_number` varchar(30) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `user_id`, `student_number`, `course`, `year_level`, `contact_number`, `profile_picture`, `status`, `created_at`, `updated_at`) VALUES
(1, 3, '2023-00123', 'BS Computer Science', '3rd Year', '09171234567', 'avatar_01.svg', 'active', '2026-07-03 02:12:58', '2026-07-03 02:12:58'),
(2, 4, '2023-00456', 'BS Information Technology', '2nd Year', '09182345678', 'avatar_02.svg', 'active', '2026-07-03 02:12:58', '2026-07-03 02:12:58'),
(3, 5, '2022-00789', 'BS Computer Engineering', '4th Year', '09193456789', 'avatar_03.svg', 'active', '2026-07-03 02:12:58', '2026-07-03 02:12:58'),
(4, 6, '2024-00234', 'BS Psychology', '1st Year', '09204567890', 'avatar_04.svg', 'active', '2026-07-03 02:12:58', '2026-07-03 02:12:58'),
(5, 7, '2023-00567', 'BS Computer Science', '3rd Year', '09215678901', 'avatar_05.svg', 'active', '2026-07-03 02:12:58', '2026-07-03 02:12:58'),
(6, 8, '2022-00890', 'BS Literature', '4th Year', '09226789012', 'avatar_06.svg', 'active', '2026-07-03 02:12:58', '2026-07-03 02:12:58');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','student') NOT NULL DEFAULT 'student',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Admin Librarian', 'admin@library.edu', '$2b$12$WGC2JljA9Qu6oyCtFh8eYOP3viZ2FwdBHWR0pDBUn5VZ9Xys1gqfi', 'admin', '2026-07-03 02:12:58', '2026-07-03 02:12:58'),
(2, 'Ma. Teresa Santos', 'librarian@library.edu', '$2b$12$QT9z4PMLf8nSG97d6jyV6.gHxOz.BirWltVB.IVVKKFkE8JFtOS/m', 'admin', '2026-07-03 02:12:58', '2026-07-03 02:12:58'),
(3, 'Juan Dela Cruz', 'juan.delacruz@student.edu', '$2y$10$G.3ukOWz1gyYQIA76ySTeeiP1OB5YHSSoIQiKR8Uq/h6iDwPT6G6q', 'student', '2026-07-03 02:12:58', '2026-07-03 02:17:48'),
(4, 'Maria Santos', 'maria.santos@student.edu', '$2b$12$rTOfQFjfBL4n/PV4ZDGGJOGYJjZK/0nwQdNL7L2Edkp52d0m6CbU2', 'student', '2026-07-03 02:12:58', '2026-07-03 02:12:58'),
(5, 'Angelo Reyes', 'angelo.reyes@student.edu', '$2b$12$rTOfQFjfBL4n/PV4ZDGGJOGYJjZK/0nwQdNL7L2Edkp52d0m6CbU2', 'student', '2026-07-03 02:12:58', '2026-07-03 02:12:58'),
(6, 'Kristine Lim', 'kristine.lim@student.edu', '$2b$12$rTOfQFjfBL4n/PV4ZDGGJOGYJjZK/0nwQdNL7L2Edkp52d0m6CbU2', 'student', '2026-07-03 02:12:58', '2026-07-03 02:12:58'),
(7, 'Paolo Cruz', 'paolo.cruz@student.edu', '$2b$12$rTOfQFjfBL4n/PV4ZDGGJOGYJjZK/0nwQdNL7L2Edkp52d0m6CbU2', 'student', '2026-07-03 02:12:58', '2026-07-03 02:12:58'),
(8, 'Nadine Torres', 'nadine.torres@student.edu', '$2b$12$rTOfQFjfBL4n/PV4ZDGGJOGYJjZK/0nwQdNL7L2Edkp52d0m6CbU2', 'student', '2026-07-03 02:12:58', '2026-07-03 02:12:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_books_category` (`category_id`),
  ADD KEY `idx_books_title` (`title`),
  ADD KEY `idx_books_author` (`author`),
  ADD KEY `idx_books_isbn` (`isbn`);
ALTER TABLE `books` ADD FULLTEXT KEY `ft_books_search` (`title`,`author`,`description`,`tags`);

--
-- Indexes for table `borrowing_records`
--
ALTER TABLE `borrowing_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_borrow_student` (`student_id`),
  ADD KEY `fk_borrow_book` (`book_id`),
  ADD KEY `idx_borrow_status` (`status`),
  ADD KEY `idx_borrow_dates` (`borrow_date`,`due_date`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `imported_books`
--
ALTER TABLE `imported_books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_imported_book` (`book_id`);

--
-- Indexes for table `recommendations`
--
ALTER TABLE `recommendations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_rec_student_book` (`student_id`,`book_id`),
  ADD KEY `fk_rec_book` (`book_id`);

--
-- Indexes for table `similarity_scores`
--
ALTER TABLE `similarity_scores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_sim_pair` (`book_id_a`,`book_id_b`),
  ADD KEY `fk_sim_book_b` (`book_id_b`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_number` (`student_number`),
  ADD KEY `fk_students_user` (`user_id`),
  ADD KEY `idx_students_status` (`status`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `borrowing_records`
--
ALTER TABLE `borrowing_records`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `imported_books`
--
ALTER TABLE `imported_books`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `recommendations`
--
ALTER TABLE `recommendations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `similarity_scores`
--
ALTER TABLE `similarity_scores`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `fk_books_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `borrowing_records`
--
ALTER TABLE `borrowing_records`
  ADD CONSTRAINT `fk_borrow_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_borrow_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `imported_books`
--
ALTER TABLE `imported_books`
  ADD CONSTRAINT `fk_imported_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recommendations`
--
ALTER TABLE `recommendations`
  ADD CONSTRAINT `fk_rec_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rec_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `similarity_scores`
--
ALTER TABLE `similarity_scores`
  ADD CONSTRAINT `fk_sim_book_a` FOREIGN KEY (`book_id_a`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sim_book_b` FOREIGN KEY (`book_id_b`) REFERENCES `books` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `fk_students_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

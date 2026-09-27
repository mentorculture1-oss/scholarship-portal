-- ============================================
-- Online Scholarship Portal - Database
-- ============================================

CREATE DATABASE IF NOT EXISTS scholarship_portal 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE scholarship_portal;

-- USERS TABLE
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    gender VARCHAR(10) NOT NULL,
    date_of_birth DATE NOT NULL,
    national_id VARCHAR(20) NOT NULL UNIQUE,
    county VARCHAR(100) NOT NULL,
    institution VARCHAR(200) NOT NULL,
    course VARCHAR(200) NOT NULL,
    year_of_study INT NOT NULL,
    status VARCHAR(20) DEFAULT 'active',
    suspension_reason TEXT DEFAULT NULL,
    suspended_at DATETIME DEFAULT NULL,
    suspended_by INT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ADMINS TABLE
CREATE TABLE admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- SCHOLARSHIPS TABLE
CREATE TABLE scholarships (
    scholarship_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    eligibility TEXT NOT NULL,
    amount DECIMAL(12,2) DEFAULT NULL,
    slots INT DEFAULT NULL,
    category VARCHAR(100) NOT NULL,
    provider VARCHAR(200) NOT NULL,
    application_deadline DATE NOT NULL,
    status VARCHAR(20) DEFAULT 'open',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- APPLICATIONS TABLE
CREATE TABLE applications (
    application_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    scholarship_id INT NOT NULL,
    personal_statement TEXT NOT NULL,
    financial_need TEXT NOT NULL,
    gpa DECIMAL(3,2) DEFAULT NULL,
    transcript_path VARCHAR(255) DEFAULT NULL,
    supporting_doc_path VARCHAR(255) DEFAULT NULL,
    recommendation_path VARCHAR(255) DEFAULT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    admin_notes TEXT DEFAULT NULL,
    reviewed_by INT DEFAULT NULL,
    reviewed_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (scholarship_id) REFERENCES scholarships(scholarship_id) ON DELETE CASCADE,
    UNIQUE KEY unique_application (user_id, scholarship_id)
) ENGINE=InnoDB;

-- NOTIFICATIONS TABLE
CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(50) DEFAULT 'info',
    is_read TINYINT(1) DEFAULT 0,
    is_done TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- CONTACT MESSAGES TABLE
CREATE TABLE contact_messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- DEFAULT ADMIN (password: "password")
INSERT INTO admins (username, email, password, full_name) VALUES
('admin', 'admin@scholarshipportal.co.ke', 
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Super Admin');

-- SAMPLE SCHOLARSHIPS
INSERT INTO scholarships (title, description, eligibility, amount, slots, category, provider, application_deadline, status) VALUES
('Kenya Government Scholarship', 'Full undergraduate scholarship for bright students from disadvantaged backgrounds.', 'Must be a Kenyan citizen, completed KCSE with minimum B+ grade.', 500000.00, 500, 'Government', 'Ministry of Education', '2026-12-31', 'open'),
('Equity Bank Wings to Fly', 'Comprehensive scholarship program for bright but needy students.', 'KCSE mean grade A- and above, from needy background.', 400000.00, 1000, 'Private', 'Equity Group Foundation', '2026-11-15', 'open'),
('Mastercard Foundation Scholarship', 'Full scholarship including tuition, accommodation, and stipend.', 'Academic excellence and demonstrated financial need.', 750000.00, 200, 'International', 'Mastercard Foundation', '2026-10-30', 'open'),
('STEM Scholarship for Women', 'Scholarship for female students pursuing STEM courses.', 'Female students in STEM programs at recognized universities.', 350000.00, 300, 'Private', 'Women in STEM Foundation', '2026-07-15', 'open');
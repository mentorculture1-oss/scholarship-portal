<?php
function isLoggedIn() { return isset($_SESSION['user_id']); }
function isAdminLoggedIn() { return isset($_SESSION['admin_id']); }

function requireLogin() {
    if (!isLoggedIn()) {
        setFlash('warning', 'Please login to access this page.');
        redirect(APP_URL . 'login.php');
    }
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        setFlash('warning', 'Please login as admin.');
        redirect(APP_URL . 'admin/login.php');
    }
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function getCurrentAdmin() {
    if (!isAdminLoggedIn()) return null;
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE admin_id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    return $stmt->fetch();
}

function registerUser($data) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ? OR national_id = ?");
    $stmt->execute([$data['email'], $data['national_id']]);
    if ($stmt->fetch()) throw new Exception('Email or National ID already registered.');

    $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO users 
        (full_name, email, phone, password, gender, date_of_birth, national_id, county, institution, course, year_of_study) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $data['full_name'], $data['email'], $data['phone'], $hashedPassword,
        $data['gender'], $data['date_of_birth'], $data['national_id'],
        $data['county'], $data['institution'], $data['course'], $data['year_of_study']
    ]);
    $userId = $pdo->lastInsertId();
    sendNotification($userId, 'Welcome to Scholarship Portal!',
        'Thank you for registering. Start exploring scholarships today!', 'success');
    return $userId;
}

function loginUser($email, $password) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($password, $user['password'])) {
        throw new Exception('Invalid email or password.');
    }
    if ($user['status'] === 'suspended') {
        $reason = $user['suspension_reason'] ?? 'No reason provided';
        throw new Exception("Your account has been suspended. Reason: {$reason}");
    }
    $_SESSION['user_id']    = $user['user_id'];
    $_SESSION['user_name']  = $user['full_name'];
    $_SESSION['user_email'] = $user['email'];
    return $user;
}

function loginAdmin($username, $password) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $admin = $stmt->fetch();
    if (!$admin || !password_verify($password, $admin['password'])) {
        throw new Exception('Invalid credentials.');
    }
    $_SESSION['admin_id']   = $admin['admin_id'];
    $_SESSION['admin_name'] = $admin['full_name'];
    return $admin;
}

function logout() {
    session_unset();
    session_destroy();
}
?>
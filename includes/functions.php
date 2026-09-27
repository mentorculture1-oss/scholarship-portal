<?php
/**
 * Sanitize input data
 */
function sanitize($data) {
    if ($data === null) return '';
    return htmlspecialchars(strip_tags(trim((string)$data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to URL
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Set flash message
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function displayFlash() {
    $flash = getFlash();
    if ($flash) {
        $alertClass = 'alert-' . $flash['type'];
        echo "<div class='alert {$alertClass} alert-dismissible fade show' role='alert'>
                {$flash['message']}
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
              </div>";
    }
}

/**
 * Format currency
 */
function formatCurrency($amount) {
    return 'KES ' . number_format((float)$amount, 2);
}

/**
 * Format date
 */
function formatDate($date, $format = 'M d, Y') {
    if (!$date) return 'N/A';
    return date($format, strtotime($date));
}

/**
 * Time ago
 */
function timeAgo($datetime) {
    if (!$datetime) return '';
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    return formatDate($datetime);
}

/**
 * Status badge
 */
function getStatusBadge($status) {
    $badges = [
        'pending'      => 'warning',
        'under_review' => 'info',
        'shortlisted'  => 'primary',
        'awarded'      => 'success',
        'rejected'     => 'danger',
        'open'         => 'success',
        'closed'       => 'secondary',
        'active'       => 'success',
        'suspended'    => 'danger'
    ];
    $class = $badges[strtolower($status)] ?? 'secondary';
    return "<span class='badge bg-{$class}'>" . ucfirst(str_replace('_', ' ', $status)) . "</span>";
}

/**
 * Upload file
 */
function uploadFile($file, $subfolder = '') {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return null;
    }
    $uploadDir = UPLOAD_DIR . $subfolder . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    if ($file['size'] > MAX_FILE_SIZE) {
        throw new Exception('File size exceeds maximum limit of 5MB.');
    }
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        throw new Exception('File type not allowed. Allowed: ' . implode(', ', ALLOWED_EXTENSIONS));
    }
    $filename = uniqid('doc_', true) . '.' . $extension;
    $filepath = $uploadDir . $filename;
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Failed to upload file.');
    }
    return $subfolder . '/' . $filename;
}

/**
 * Kenyan counties
 */
function getKenyanCounties() {
    return [
        'Baringo','Bomet','Bungoma','Busia','Elgeyo-Marakwet','Embu',
        'Garissa','Homa Bay','Isiolo','Kajiado','Kakamega','Kericho',
        'Kiambu','Kilifi','Kirinyaga','Kisii','Kisumu','Kitui',
        'Kwale','Laikipia','Lamu','Machakos','Makueni','Mandera',
        'Marsabit','Meru','Migori','Mombasa',"Murang'a",'Nairobi',
        'Nakuru','Nandi','Narok','Nyamira','Nyandarua','Nyeri',
        'Samburu','Siaya','Taita-Taveta','Tana River','Tharaka-Nithi',
        'Trans Nzoia','Turkana','Uasin Gishu','Vihiga','Wajir','West Pokot'
    ];
}

/**
 * Scholarship categories
 */
function getScholarshipCategories() {
    return ['Government','Private','International','NGO','Corporate','Other'];
}

/**
 * Check if deadline passed
 */
function isDeadlinePassed($deadline) {
    return strtotime($deadline) < strtotime(date('Y-m-d'));
}
?>
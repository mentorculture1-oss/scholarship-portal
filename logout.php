<?php
require_once 'includes/config.php';
logout();
setFlash('info', 'You have been logged out successfully.');
redirect(APP_URL . 'index.php');
?>
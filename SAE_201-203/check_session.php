<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

function requireRole($role) {
    if (!hasRole($role)) {
        redirect('login.php');
    }
}
?>
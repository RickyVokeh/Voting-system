<?php
function isLoggedIn() {
    return isset($_SESSION['user_type']);
}
function isAdmin() {
    return (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin');
}
?>
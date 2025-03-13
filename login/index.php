<?php
session_start();
if (!isset($_SESSION['js_valid'])) {
    $_SESSION['js_valid'] = bin2hex(random_bytes(8));
}
if (!isset($_SESSION['form_token'])) {
    $_SESSION['form_token'] = bin2hex(random_bytes(16));
    $_SESSION['form_start'] = time();
}
?>

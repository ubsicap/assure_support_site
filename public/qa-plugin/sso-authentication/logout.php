<?php
require_once 'sso-authentication-login.php';

session_start();

$_SESSION['logout'] = 'true';
// redirect user to home page to continue logout algorithm 
$redirect_uri = isset($_SESSION['return_url']) ? $_SESSION['return_url'] : 'http://' . $_SERVER['HTTP_HOST'] . '/';
header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
<?php
session_start();
if (isset($_GET['code'])) {
	require_once 'helper-functions.php';
	$isGoogle = strpos($_GET['scope'], 'google');
	$redirect_uri = isset($_SESSION['return_url']) ? $_SESSION['return_url'] : 'http://' . $_SERVER['HTTP_HOST'] . '/';

	try {
		if (!isset($_GET['state']) && !$isGoogle) {
			// Invalid state parameter, possible CSRF attack
			echo 'Error: Authentication failed! (Invalid state)';
			exit;
		}
		// store parameter code to proccess it when redirected to home page
		$_SESSION['code'] = urlencode($_GET['code']);
		logInWithSSO($isGoogle);
	} catch (Exception $e) {
		echo 'Error?: ' . $e->getMessage();
		header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
		exit();
	}
} 



/*
	Omit PHP closing tag to help avoid accidental output
*/
<?php

require_once 'sso-authentication-login.php';

session_start();
$redirect_uri = isset($_SESSION['return_url']) ? $_SESSION['return_url'] : 'http://' . $_SERVER['HTTP_HOST'] . '/';

if (isset($_GET['code'])) {
	$scope = $_GET['scope'];
	try {
		// store parameter code to proccess it when redirected to home page
		$_SESSION['code'] = $_GET['code'];
		// both google & fb has parameter state, but we made our own state for Google
		$isGoogle = isset($scope) && strpos($scope, 'google');
		if (!isset($_GET['state']) || (isset($_GET['state']) && strcmp($_GET['state'], $_SESSION['state']) !== 0 && $isGoogle)) {
			// Invalid state parameter, possible CSRF attack
			echo 'Error: Authentication failed! (Invalid state)';
			exit;
		}

		ssoLogin($isGoogle);
	} catch (Exception $e) {
		echo 'Error?: ' . $e->getMessage();
		header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
		exit();
	}
} else {
	// Handle error response
	echo 'Error: Authentication failed! (Invalid code)';
	header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
	exit;
}

// helper functions
function ssoLogin($isGoogle)
{
	// Store scope so that we know we will log in with Google when redirect to home page
	if ($isGoogle) $_SESSION['scope'] = $_GET['scope'];

	// bring users to previous page they are at before logged in
	$redirect_uri = isset($_SESSION['return_url']) ? $_SESSION['return_url'] : 'http://' . $_SERVER['HTTP_HOST'] . '/';
	header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}


/*
	Omit PHP closing tag to help avoid accidental output
*/
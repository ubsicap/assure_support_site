<?php

require_once 'sso-authentication-login.php';

session_start();

if (isset($_GET['code'])) {
	$scope = $_GET['scope'];
	if (isset($scope) && strpos($scope, 'google')) {
		try {
			loginWithGoogle();
		} catch (Google_Exception $e) {
			// Handle exception
			echo 'Error?: ' . $e->getMessage();
			exit;
		}
	} else {
		//with fb
	}
} else {
	// Handle error response
	echo 'Error: Authentication failed!';
	exit;
}

// If the "logout" query string parameter is present, log the user out
if (isset($_GET['logout'])) {
	$redirect_uri = isset($_SESSION['return_url']) ? $_SESSION['return_url'] : 'http://' . $_SERVER['HTTP_HOST'] . '/';
	header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
  }

// helper functions
function loginWithGoogle()
{
	if (isset($_GET['state']) && strcmp($_GET['state'], $_SESSION['state']) !== 0) {
		// Invalid state parameter, possible CSRF attack
		echo 'Error: Authentication failed!!';
		exit;
	} else {
		// store parameter code to proccess it when redirected to home page
		$_SESSION['code'] = $_GET['code'];
		$_SESSION['scope'] = $_GET['scope'];
		// bring users to previous page they are at before logged in
		$redirect_uri = isset($_SESSION['return_url']) ? $_SESSION['return_url'] : 'http://' . $_SERVER['HTTP_HOST'] . '/';
  		header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
	}
	
}

/*
	Omit PHP closing tag to help avoid accidental output
*/
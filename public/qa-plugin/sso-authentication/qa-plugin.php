<?php

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}
//layer
qa_register_plugin_layer('sso-authentication-layer.php', 'SSO Authentication');

qa_register_plugin_module('login', 'sso-authentication-login.php', 'sso_authentication_login', 'signin');

// Register all language files
qa_register_plugin_phrases('lang/sso-auth-lang-*.php', 'sso-auth');

// Account Recovery and Reclaim pages
qa_register_plugin_module('page', 'sso-recover-page.php', 'sso_recover_page', 'Recover Account Pages');

/*
	Omit PHP closing tag to help avoid accidental output
*/
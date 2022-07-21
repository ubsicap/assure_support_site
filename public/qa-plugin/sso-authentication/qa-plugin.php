<?php

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}
//layer
qa_register_plugin_layer('sso-authentication-layer.php', 'SSO Authentication');

qa_register_plugin_module('login', 'sso-authentication-login.php', 'sso_authentication_login', 'signin');
// qa_register_plugin_module('login', 'sso-authentication-login.php', 'sso_authentication_login', 'facebook');
// qa_register_plugin_module('login', 'sso-authentication-login.php', 'sso_authentication_login', 'Paratext');
// Register all language files

qa_register_plugin_phrases('lang/sso-auth-lang-*.php', 'sso-auth');

/*
	Omit PHP closing tag to help avoid accidental output
*/
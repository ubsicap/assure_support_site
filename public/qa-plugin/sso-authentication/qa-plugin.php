<?php

	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../../');
		exit;
	}
	//layer
	qa_register_plugin_layer('sso-authentication-layer.php', 'SSO Authentication');
	// admin
	qa_register_plugin_module('module', 'sso-authentication-admin.php', 'sso_authentication_admin', 'SSO Authentication');
        

/*
	Omit PHP closing tag to help avoid accidental output
*/
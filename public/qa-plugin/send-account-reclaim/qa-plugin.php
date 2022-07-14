<?php

/*
	Plugin Name: Send Email For Account Reclaim
	Plugin Description: add ^accountreclaim users to mailing in admin
	Plugin Version: 1.0
	Plugin Date: 2022-07-13	
	Licence: Copyright © q2apro.com - All rights reserved

*/

	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../../');
		exit;
	}

	// language file
	qa_register_plugin_phrases('account-reclaim-lang-*.php', 'account_reclaim_lang');

	// admin
	qa_register_plugin_module('module', 'account-reclaim-admin.php', 'account_reclaim_admin', 'Account Reclaim');
        

/*
	Omit PHP closing tag to help avoid accidental output
*/
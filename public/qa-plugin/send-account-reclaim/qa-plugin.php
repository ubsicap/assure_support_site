<?php

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
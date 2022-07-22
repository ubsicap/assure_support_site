<?php

	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../../');
		exit;
	}

	// language file
	qa_register_plugin_phrases('post-validator-lang-*.php', 'post_validator_lang');

	qa_register_plugin_layer('post-validator-layer.php', 'Post Validator Layer');
	// admin
	qa_register_plugin_module('module', 'post-validator-admin.php', 'post_validator_admin', 'Post Validator');
        

/*
	Omit PHP closing tag to help avoid accidental output
*/
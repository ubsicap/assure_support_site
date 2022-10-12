<?php

	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../../');
		exit;
	}

	// language file
	qa_register_plugin_phrases('random-verse-lang-*.php', 'random_verse_lang');

	// admin
	qa_register_plugin_module('module', 'random-verse.php', 'random_verse', 'Random Verse');

	//layer
	qa_register_plugin_layer('random-verse-layer.php', 'Random Verse Layer');
        

/*
	Omit PHP closing tag to help avoid accidental output
*/
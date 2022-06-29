<?php

/*
	Plugin Name: Pretty Tags
	Plugin URI: http://www.q2apro.com/plugins/pretty-tags
	Plugin Description: Provides a pretty autocomplete for tags on the ask page
	Plugin Version: 1.0
	Plugin Date: 2014-10-05
	Plugin Author: q2apro.com
	Plugin Author URI: http://www.q2apro.com
	Plugin Minimum Question2Answer Version: 1.5
	Plugin Update Check URI: http://www.q2apro.com/pluginupdate?id=59
	
	Licence: Copyright © q2apro.com - All rights reserved

*/

	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../../');
		exit;
	}

	// language file
	qa_register_plugin_phrases('q2apro-prettytags-lang-*.php', 'q2apro_prettytags_lang');

	// layer
	qa_register_plugin_layer('q2apro-prettytags-layer.php', 'Q2APRO Prettytags Layer');

	// admin
	qa_register_plugin_module('module', 'q2apro-prettytags-admin.php', 'q2apro_prettytags_admin', 'Q2APRO Prettytags Admin');
        

/*
	Omit PHP closing tag to help avoid accidental output
*/
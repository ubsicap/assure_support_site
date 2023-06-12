<?php

/*
	Plugin Name: Feedback Page
	Plugin Description: Adds custom feedback page that connects user feedback to jetbrains
*/


if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
    header('Location: ../../');
    exit;
}

/*
qa_register_plugin_phrases('lang/feedback-lang-*.php', 'feedback');
*/
qa_register_plugin_module('widget', 'qa-feedback-widget.php', 'qa_feedback_widget', 'Give Feedback Widget');
/*
qa_register_plugin_module('module', 'qa-feedback-admin.php', 'qa_feedback_admin', 'Feedback Admin');
	*/

/*
	Omit PHP closing tag to help avoid accidental output
*/

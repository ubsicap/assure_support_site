<?php

/*
	Question2Answer (c) Gideon Greenspan

	http://www.question2answer.org/


	File: qa-plugin/akismet-spam-filter/qa-plugin.php
	Version: See define()s at top of qa-include/qa-base.php
	Description: Initiates Akismet Spam Filter plugin
*/

/*
	Plugin Name: Akismet Spam Filter
	Plugin URI: http://qcybb.com/question2answer-akismet-plugin/
	Plugin Description: Uses Akismet to check questions, answers and comments for spam.
	Plugin Version: 2.0
	Plugin Date: 2017-05-04
	Plugin Author: Dave Hannon
	Plugin Author URI: http://www.qcybb.com
	Plugin License: GPLv2
	Plugin Minimum Question2Answer Version: 1.5
	Plugin Minimum PHP Version: 5
	Plugin Update Check URI:
*/


	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../../');
		exit;
	}

	qa_register_plugin_module('filter', 'qa-akismet.php', 'qa_akismet', 'Akismet Spam Filter');
	//qa_register_plugin_overrides('qa-akismet-overrides.php');
	//qa_register_plugin_layer('qa-akismet-layer.php', 'Akismet Spam Filter Layer');

/*
	Omit PHP closing tag to help avoid accidental output
*/

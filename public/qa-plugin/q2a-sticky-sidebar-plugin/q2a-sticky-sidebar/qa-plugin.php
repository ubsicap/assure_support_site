<?php
/*
	Plugin Name: Simple Sticky Sidebar
	Plugin URI: https://www.question2answer.org/qa/71614/new-free-plugin-sticky-sidebar
	Plugin Description: Implementing "abouolia sticky sidebar" script. By default works with SnowFlat and similar themes. You can tune for your theme in options panel.
	Plugin Version: 0.1
	Plugin Date: 2018-01-05
	Plugin Author: Stefan Marjanov
	Plugin Author URI: https://www.saznajnovo.com/
	Plugin License: GPLv2
	Plugin Minimum Question2Answer Version: 1.5
	Plugin Update Check URI: 
*/
/*
	Thanks to https://abouolia.github.io/sticky-sidebar/
*/
if (!defined('QA_VERSION')) { 
	header('Location: ../../');
	exit;
}

qa_register_plugin_layer(
	'sticky-sidebar-layer.php',
	'Sticky Sidebar Layer'
);

qa_register_plugin_module(
	'module',
	'sticky-sidebar-module.php',
	'sticky_sidebar_module',
	'Sticky Sidebar Options'
);

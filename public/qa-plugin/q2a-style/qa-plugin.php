<?php

/*
        Plugin Name: Q2A Style
        Plugin Description: Modifies the style of the ask ebeneezer site in various ways
        Plugin Version: 1.1
        Plugin Date: 2022-07-20
        Plugin Author: Paratext Support team
        Plugin License: GPLv2
*/


if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
        header('Location: ../../');
        exit;
}

qa_register_plugin_layer('qa-style-layer.php', 'CSS Override Layer');

//search bar widget
qa_register_module('widget', 'qa-style-widget.php', 'qa_search_bar', 'Search Bar');

/*
	Omit PHP closing tag to help avoid accidental output
*/

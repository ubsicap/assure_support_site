<?php
/*
	Question2Answer Tagging Tools plugin
	Copyright (C) 2011 Scott Vivian

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	More about this license: https://www.gnu.org/licenses/gpl.html
*/

if (!defined('QA_VERSION')) exit;

qa_register_plugin_module('filter', 'qa-tagging-tools.php', 'qa_tagging_tools', 'Tagging Tools');
qa_register_plugin_module('page', 'qa-tt-ajax.php', 'qa_tagging_tools_ajax', 'Tagging Tools AJAX handler');
qa_register_plugin_module('process', 'qa-tt-redirect.php', 'qa_tagging_tools_redirect', 'Tagging Tools tag redirect');
qa_register_plugin_layer('qa-tt-layer.php', 'Tagging Tools Layer');
qa_register_plugin_phrases('qa-tt-lang-*.php', 'taggingtools');

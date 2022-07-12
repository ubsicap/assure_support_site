<?php

/*
	Plugin Name: PUPI - Maximum Tag Length
	Plugin URI: http://github.com/pupi1985/q2a-maximum-tag-length
	Plugin Description: Limits the amount of characters allowed in question tags
	Plugin Version: 1.0.0
	Plugin Date: 2014-12-01
	Plugin Author: Gabriel Zanetti
	Plugin Author URI: http://question2answer.org/qa/user/pupi1985
	Plugin License: GPLv3
	Plugin Minimum Question2Answer Version: 1.6
	Plugin Minimum PHP Version: 5.1.2
	Plugin Update Check URI: https://raw.githubusercontent.com/pupi1985/q2a-maximum-tag-length/master/metadata.json
*/

/*
	This file is part of PUPI - Maximum Tag Length, a Question2Answer plugin
	that limits the amount of characters allowed per each question tag

	Copyright (C) 2014 Gabriel Zanetti <http://github.com/pupi1985>

	PUPI - Maximum Tag Length is free software: you can redistribute it and/or
	modify it under the terms of the GNU General Public License as published
	by the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	PUPI - Maximum Tag Length is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General
	Public License for more details.

	You should have received a copy of the GNU General Public License along
	with PUPI - Maximum Tag Length. If not, see <http://www.gnu.org/licenses/>.
*/

if (!defined('QA_VERSION')) { // don't allow this file to be requested directly from browser
	header('Location: ../../');
	exit;
}

qa_register_plugin_module('module', 'pupi_mtl_module_admin.php', 'PUPI_MTL_Module_Admin', 'PUPI MTL Module Admin');
qa_register_plugin_module('filter', 'pupi_mtl_module_filter.php', 'PUPI_MTL_Module_Filter', 'PUPI MTL Module Filter');
qa_register_plugin_phrases('lang/pupi_mtl_*.php', 'pupi_mtl');

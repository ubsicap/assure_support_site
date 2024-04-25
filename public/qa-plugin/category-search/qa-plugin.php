<?php



	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
			header('Location: ../../');
			exit;
	}
	
	qa_register_plugin_layer('category-search-layer.php', 'Category Search Layer');	
	qa_register_plugin_module('module', 'category-search-admin.php', 'category_search_admin', 'Category Search Admin');
	qa_register_plugin_module('search', 'category-search-module.php', 'category_search_module', 'Category Search Module');
	qa_register_plugin_module('page', 'category-search-page.php', 'category_search_page', 'Category Search Page');

/*
	Omit PHP closing tag to help avoid accidental output
*/

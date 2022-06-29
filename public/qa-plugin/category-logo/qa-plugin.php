<?php
/*
  Plugin Name: Category Logo
  Plugin URI:
  Plugin Description: Allows logo to be displayed next to a category
  Plugin Version: 1.0
  Plugin Date: 2022-06-27
  Plugin Author:
  Plugin Author URI:
  Plugin License: 
  Plugin Minimum Question2Answer Version: 
  Plugin Update Check URI:
*/

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
  header('Location: ../../');
  exit;
}

qa_register_plugin_layer('qa-category-logo-layer.php', 'Category Logo');
qa_register_plugin_module('module', 'qa-category-logo.php', 'qa_category_logo', 'Category Logo');
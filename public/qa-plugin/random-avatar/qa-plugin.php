<?php

/*
    Plugin registration for the random avatar plugin
*/
if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
    header('Location: ../../');
    exit;
}

// Register all function overrides
qa_register_plugin_overrides('qa-ra-overrides.php');

/*

Register the plugin's many modules
Parameters are:
    - Plugin module type
    - Location of the module's class declaration
    - Name of the class declaration
    - Module name

// Account Recovery and Reclaim pages
qa_register_plugin_module('page', 'qa-ar-page.php', 'qa_ar_page', 'Recover/Reclaim Account Pages');

// Warn users if they attempt to register with an archived account
qa_register_plugin_module('filter', 'qa-ar-email.php', 'qa_ar_filter', 'Archived User Registration Warning');

// Admin page for this plugin
qa_register_plugin_module('module', 'qa-ar-admin.php', 'qa_ar_admin', 'Reclaim Account Admin Page');

// Event module to delete archived accounts once reclaimed
qa_register_plugin_module('event', 'qa-ar-event.php', 'qa_ar_archive_cleanup', 'Archived User Cleanup');
*/
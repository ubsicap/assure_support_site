<?php

/*
    Outlines the process of reclaiming an account and migrating
    it to the new database.

    This plugin provides the following modules:
        - A page module for the Account Reclaim page
        - A module for managing an Account Reclaim table in the database
        - An override module for handling user account resetting
        - A language module
        - A filter module for alerting users if they try to login with an archived account
*/
if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
    header('Location: ../../');
    exit;
}

// Register all language files
qa_register_plugin_phrases('lang/qa-ar-lang-*.php', 'qa-ar');

// Register all function overrides
qa_register_plugin_overrides('qa-ar-overrides.php');

// Register layer for registration page warning
qa_register_plugin_layer('qa-ar-layer.php', 'Registration Page Warning');

/*
Register the plugin's many modules
Parameters are:
    - Plugin module type
    - Location of the module's class declaration
    - Name of the class declaration
    - Module name
*/
// Account Recovery and Reclaim pages
qa_register_plugin_module('page', 'qa-ar-page.php', 'qa_ar_page', 'Recover/Reclaim Account Pages');

// Warn users if they attempt to register with an archived account
//qa_register_plugin_module('filter', 'qa-ar-email.php', 'qa_ar_filter', 'Archived User Registration Warning');

// Admin page for this plugin
qa_register_plugin_module('module', 'qa-ar-admin.php', 'qa_ar_admin', 'Reclaim Account Admin Page');

// Event module to delete archived accounts once reclaimed
qa_register_plugin_module('event', 'qa-ar-event.php', 'qa_ar_archive_cleanup', 'Archived User Cleanup');
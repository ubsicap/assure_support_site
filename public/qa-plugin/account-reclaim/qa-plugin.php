<?php

/*
    Outlines the process of reclaiming an account and migrating
    it to the new database.

    This plugin provides the following modules:
        - A page module for the Account Reclaim page
        - A module for managing an Account Reclaim table
            in the database
*/
if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
    header('Location: ../../');
    exit;
}

// Register all language files
qa_register_plugin_phrases('lang/qa-ar-lang-*.php', 'qa-ar');

// Register all function overrides
qa_register_plugin_overrides('qa-ar-overrides.php');

/*
Register the plugin's "Reclaim Account" pages
Parameters are:
    - Plugin module type
    - Location of the module's class declaration
    - Name of the class declaration
    - Module name
*/
qa_register_plugin_module('page', 'qa-ar-page.php', 'qa_ar_page', 'Reclaim Account');
qa_register_plugin_module('filter', 'qa-ar-email.php', 'qa_ar_filter', 'Archived User Warning');
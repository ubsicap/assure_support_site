<?php

/*
    Provides a process of automatically marking accounts for deletion if they have
        not verified their account after a configurable amount of time and removing
        them based off of certain event triggers.
*/



if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
    header('Location: ../../');
    exit;
}

// Register all language files
qa_register_plugin_phrases('lang/qa-apa-lang-*.php', 'qa-apa');

/*
Register the plugin's many modules
Parameters are:
    - Plugin module type
    - Location of the module's class declaration
    - Name of the class declaration
    - Module name
*/
// Admin page for this plugin
qa_register_plugin_module('module', 'qa-apa-admin.php', 'qa_apa_admin', 'Admin page for Auto-Prune plugin');

// Filter module
qa_register_plugin_module('filter', 'qa-apa-filters.php', 'qa_apa_filters', 'Delete unconfirmed accounts when attempting to register new ones');

// Event module
qa_register_plugin_module('event', 'qa-apa-events.php', 'qa_apa_events', 'Delete unconfirmed accounts on events such as login and register');

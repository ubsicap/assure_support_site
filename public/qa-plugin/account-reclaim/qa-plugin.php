<?php

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
    header('Location: ../../');
    exit;
}

// Register all language files
qa_register_plugin_phrases('lang/qa-account-reclaim-lang-*.php', 'accountreclaim');

/*
Register the actual plugin.
Parameters are:
    - Plugin module type
    - Location of the module's class declaration
    - Name of the class declaration
    - Module name
*/
qa_register_plugin_module('page', 'qa-account-reclaim-page.php', 'qa_account_reclaim', 'Account Reclaim Page');
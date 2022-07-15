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
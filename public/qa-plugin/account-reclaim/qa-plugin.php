<?php

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
    header('Location: ../../');
    exit;
}


qa_register_plugin_module('page', 'qa-account-reclaim-page.php', 'qa_account_reclaim', 'Account Reclaim Page');
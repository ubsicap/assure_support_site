<?php

/*
    Displays role names next to users in posts
*/


if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
    header('Location: ../../');
    exit;
}


qa_register_plugin_layer('qa-roleshow-layer.php', 'Role Show');
//qa_register_plugin_module('module', 'qa-roleshow-admin-form.php', 'qa_roleshow_admin_form', 'Role Show');

<?php

/*
    File: qa-plugin/auto-prune-accounts/qa-apa-overrides.php
    Description: Contains Q2A function overrides for the Auto-Prune Accounts plugin
*/



require_once QA_PLUGIN_DIR . 'auto-prune-accounts/qa-apa-functions.php';



/**
 * This is an OVERRIDE. The original function is:
 *      qa-include/app/users-edit.php:qa_start_reset_user($userid)
 */

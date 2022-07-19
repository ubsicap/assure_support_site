<?php

/*
    File: qa-plugin/auto-prune-accounts/qa-ar-overrides.php
    Description: Contains Q2A function overrides for the Account Reclaim plugin
*/



/**
 * Return the userid of the currently logged in user, or null if none logged in.
 * 
 * Modification of this function focuses on the issue of an account being deleted and thus
 *  the `qa_db_single_select()` returning `null`
 * 
 * This is an OVERRIDE. The original function is:
 *      qa-include/app/users.php:qa_get_logged_in_userid()
 * 
 * @return mixed
 */
function qa_get_logged_in_userid()
{

    // Most of this is copied directly from a section of the original function
    if (!empty($_COOKIE['qa_session'])) {
        @list($handle, $sessioncode, $remember) = explode('/', $_COOKIE['qa_session']);

        require_once QA_INCLUDE_DIR . 'db/selects.php';

        $userinfo = qa_db_single_select(qa_db_user_account_selectspec($handle, false)); // don't get any pending

        // This is the only new functionality provided by the override
        if (empty($userinfo)) {
            return;
        }
    }

    return qa_get_logged_in_userid_base();
}

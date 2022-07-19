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

    if (!empty($_COOKIE['qa_session'])) {
        @list($handle, $sessioncode, $remember) = explode('/', $_COOKIE['qa_session']);

        require_once QA_INCLUDE_DIR . 'db/selects.php';

        $userinfo = qa_db_single_select(qa_db_user_account_selectspec($handle, false)); // don't get any pending

        if (empty($userinfo)) {
            return;
        }
    }

    return qa_get_logged_in_userid_base();

    /*
    global $qa_logged_in_userid_checked;

    $suffix = qa_session_var_suffix();

    if (!$qa_logged_in_userid_checked) { // only check once
        qa_start_session(); // this will load logged in userid from the native PHP session, but that's not enough

        $sessionuserid = @$_SESSION['qa_session_userid_' . $suffix];

        if (isset($sessionuserid)) // check verify code matches
            if (!hash_equals(qa_session_verify_code($sessionuserid), @$_SESSION['qa_session_verify_' . $suffix]))
                qa_clear_session_user();

        if (!empty($_COOKIE['qa_session'])) {
            @list($handle, $sessioncode, $remember) = explode('/', $_COOKIE['qa_session']);

            if ($remember)
                qa_set_session_cookie($handle, $sessioncode, $remember); // extend 'remember me' cookies each time

            $sessioncode = trim($sessioncode); // trim to prevent passing in blank values to match uninitiated DB rows

            // Try to recover session from the database if PHP session has timed out
            if (!isset($_SESSION['qa_session_userid_' . $suffix]) && !empty($handle) && !empty($sessioncode)) {
                require_once QA_INCLUDE_DIR . 'db/selects.php';

                $userinfo = qa_db_single_select(qa_db_user_account_selectspec($handle, false)); // don't get any pending

                if (!empty($userinfo) && strtolower(trim($userinfo['sessioncode'])) == strtolower($sessioncode))
                    qa_set_session_user($userinfo['userid'], $userinfo['sessionsource']);
                else
                    qa_clear_session_cookie(); // if cookie not valid, remove it to save future checks
            }
        }

        $qa_logged_in_userid_checked = true;
    }

    return @$_SESSION['qa_session_userid_' . $suffix];
    */
}

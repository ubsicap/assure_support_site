<?php

/*
    File: qa-plugin/account-reclaim/qa-ar-overrides.php
    Description: Contains Q2A function overrides for the Account Reclaim plugin
*/



/**
 * Start the 'Reclaim Account' process for $userid, sending reset code
 * 
 * This is an OVERRIDE. The original function is:
 *      qa-include/app/users-edit.php:qa_start_reset_user($userid)
 * 
 * @param $userid
 * @return mixed
 */
function qa_start_reset_user($userid, $reclaim = false)
{
    if ($reclaim) {
        require_once QA_INCLUDE_DIR . 'db/users.php';
        require_once QA_INCLUDE_DIR . 'app/options.php';
        require_once QA_INCLUDE_DIR . 'app/emails.php';
        require_once QA_INCLUDE_DIR . 'db/selects.php';

        qa_db_user_set($userid, 'emailcode', qa_db_user_rand_emailcode());

        $userinfo = qa_db_select_with_pending(qa_db_user_account_selectspec($userid, true));

        if (!qa_send_notification($userid, $userinfo['email'], $userinfo['handle'], qa_lang('emails/reset_subject'), qa_lang('emails/reset_body'), array(
            '^code' => $userinfo['emailcode'],
            '^url' => qa_path_absolute('reset', array('c' => $userinfo['emailcode'], 'e' => $userinfo['email'])),
        ))) {
            qa_fatal_error('Could not send reset password email');
        }
    } else {
        qa_start_reset_user_base($userid);
    }
}

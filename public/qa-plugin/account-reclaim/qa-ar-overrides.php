<?php

/*
    File: qa-plugin/account-reclaim/qa-ar-overrides.php
    Description: Contains Q2A function overrides for the Account Reclaim plugin
*/



require_once QA_PLUGIN_DIR . 'account-reclaim/qa-ar-functions.php';



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

        // Generates a random email code for the reclaim process
        // Note that this uses our modified function
        // Note that this also CRASHES and I have no idea why
        //qa_ar_db_user_set($userid, 'reclaimcode', qa_db_user_rand_emailcode());

        // Generate a random email code for the reclaim process
        $sql = 'UPDATE ^accountreclaim SET ^accountreclaim.reclaimcode=$ WHERE ^accountreclaim.userid=$';
        qa_db_query_sub($sql, qa_db_user_rand_emailcode(), $userid);

        // This query gets us the correct row from qa_accountreclaim
        $userinfo = qa_db_single_select(array(
            'columns' => array('^accountreclaim.userid', '^accountreclaim.email', '^accountreclaim.reclaimcode'),
            'source' => '^accountreclaim WHERE ^accountreclaim.userid=$',
            'arguments' => array($userid),
            'single' => true,
        ));

        // Send an email to the user. Note we don't have their username, so that's null
        if (!qa_send_notification($userid, $userinfo['email'], null, qa_lang('qa-ar/recover_subject'), qa_lang('qa-ar/recover_body'), array(
            '^code' => $userinfo['reclaimcode'],
            '^url' => qa_path_absolute('reset', array('c' => $userinfo['reclaimcode'], 'e' => $userinfo['email'])),
        ))) {
            qa_fatal_error('Could not send reset password email');
        }
    } else {
        qa_start_reset_user_base($userid);
    }
}

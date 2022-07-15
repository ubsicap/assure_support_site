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

        // Send an email to the user.
        if (!qa_send_notification($userid, $userinfo['email'], qa_lang('qa-ar/recover_name'), qa_lang('qa-ar/recover_subject'), qa_lang('qa-ar/recover_body'), array(
            '^code' => $userinfo['reclaimcode'],
            '^url' => qa_path_absolute('account-reclaim', array('c' => $userinfo['reclaimcode'], 'e' => $userinfo['email'])),
        ))) {
            qa_fatal_error('Could not send Account Recovery email');
        }
    } else {
        qa_start_reset_user_base($userid);
    }
}



/**
 * Successfully finish the 'Reclaim Account' process for $userid, cleaning their reclaim table entry and logging in the user
 * 
 *  * This is an OVERRIDE. The original function is:
 *      qa-include/app/users-edit.php:qa_finish_reset_user($userid, $newPassword)
 * 
 * @param mixed $userId The userid identifiying the user who will have the password reset
 * @param string $newPassword The new password for the user
 * @param string $newEmail Email address used in account reclaim
 * @param string $newUsername The new username of the account, if applicable
 * @return void
 */
function qa_finish_reset_user($userId, $newPassword, $newEmail = null, $newUsername = null)
{
    if ($newEmail != null && $newUsername != null) {
        // For qa_db_user_set_password(), qa_db_user_set()
        require_once QA_INCLUDE_DIR . 'db/users.php';

        // For qa_set_logged_in_user()
        require_once QA_INCLUDE_DIR . 'app/options.php';

        // For qa_cookie_get()
        require_once QA_INCLUDE_DIR . 'app/cookies.php';

        // For qa_db_select_with_pending(), qa_db_user_account_selectspec()
        require_once QA_INCLUDE_DIR . 'db/selects.php';

        // For qa_set_logged_in_user()
        require_once QA_INCLUDE_DIR . 'app/users.php';

        //swap all the instances of the old username to the new one
        debug_to_console(qa_ar_db_get_anon($userId));
        debug_to_console($userId);
        debug_to_console($newUsername);
        $oldName = qa_ar_db_get_anon($userId);
        $newName = $newUsername;

        debug_to_console("UPDATE ^posts SET content=REGEXP_REPLACE(content, $oldName, $newName), title=REGEXP_REPLACE(title, $oldName, $newName)
            WHERE content like '%$oldName%' or title like '%$oldName%'");
        debug_to_console("UPDATE ^words SET word=REGEXP_REPLACE(word, $oldName, $newName)$ WHERE word like '%$oldName%'");

        qa_ar_db_swap_name(qa_ar_db_get_anon($userId), $newUsername);

        // Setting the password is a different process than setting other fields
        qa_db_user_set_password($userId, $newPassword);

        // Set the fields of the account to the newly provided values
        qa_db_user_set($userId, array(
            'email' => $newEmail,       // Update the email address so the account is valid
            'handle' => $newUsername,   // Update the username to no longer be `anon######`
            'emailcode' => '',          // Prevent re-use of the code, if it exists
        ));

        // Delete this user's entry in the account reclaim table
        $sql = 'DELETE FROM ^accountreclaim WHERE ^accountreclaim.userid=$';
        qa_db_query_sub($sql, $userId);

        // Remove the 'This is an archived user' blurb from their profile
        $sql = 'UPDATE ^userprofile SET ^userprofile.content=\'This user reclaimed their account!\' WHERE ^userprofile.userid=$ AND ^userprofile.title=$';
        qa_db_query_sub($sql, $userid, 'about');

        $userInfo = qa_db_select_with_pending(qa_db_user_account_selectspec($userId, true));

        qa_set_logged_in_user($userId, $userInfo['handle'], false, $userInfo['sessionsource']); // reinstate this specific session

        // Report that a 'user reclaim' event has occurred (for event modules)
        qa_report_event('u_reclaim', $userId, $userInfo['handle'], qa_cookie_get(), array(
            'email' => $userInfo['email'],
        ));
    } else {
        qa_finish_reset_user_base($userId, $newPassword);
    }
}




/**
 * This is an OVERRIDE. The original function is:
 * qa-include/app/users-edit.php:qa_create_new_user(...) 
 * 
 * The only edit is removing the user from the accountreclaim table if it exists
 * 
 * Create a new user (application level) with $email, $password, $handle and $level.
 * Set $confirmed to true if the email address has been confirmed elsewhere.
 * Handles user points, notification and optional email confirmation.
 * @param $email
 * @param $password
 * @param $handle
 * @param int $level
 * @param bool $confirmed
 * @return mixed
 */
function qa_create_new_user($email, $password, $handle, $level = QA_USER_LEVEL_BASIC, $confirmed = false)
{
    //remove if an archived account exists with that email
    if ($email != null && qa_ar_db_is_archived_email($email))
        qa_ar_db_remove_email($email);
    //then create the user as normal using the base function
    return qa_create_new_user_base($email, $password, $handle, $level, $confirmed);
}

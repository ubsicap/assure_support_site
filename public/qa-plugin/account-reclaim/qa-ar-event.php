<?php

/*
    File: qa-plugin/account-reclaim/qa-ar-event.php
    Description: Deletes entries in the archived table once archived accounts have been reclaimed
*/



require_once QA_PLUGIN_DIR . 'account-reclaim/qa-ar-functions.php';



class qa_ar_archive_cleanup
{
    /**
     * Handles events for registration, confirmation, and reclaimation of archived accounts.
     * 
     * See the following page for more details on event types and parameters:
     *      https://docs.question2answer.org/plugins/modules-event/
     * 
     * @param string $event Identifies the event being triggered
     * @param string $userid UserID of the account that triggered the event (may be null)
     * @param string $handle Username of the account that triggered the event (may be null)
     * @param string $cookieid Cookie ID of the account that triggered the event (may be null)
     * @param mixed $params Depends on the event; may contain additional event info
     */
    function process_event($event, $userid, $handle, $cookieid, $params)
    {
        // This only handles events regarding archived accounts
        if (isset($params['email']) && qa_ar_db_is_archived_email($params['email'])) {
            switch ($event) {
                case 'u_register':
                    // When a new user registers. The email is in $params['email'] and the privilege level in $params['level'].
                    self::notify_archived_user($params['email'], $handle);
                    break;
                case 'u_confirmed':
                    // When a user successfully confirms their email address, given in $params['email'].
                    self::delete_archived_entry($params['email']);
                    break;
                case 'u_reclaim':
                    // When a user has finished reclaiming an archived account and is now logged in. The email is in $params['email']
                    self::delete_archived_entry($params['email']);
                    self::delete_alt_accounts($userid, $params['email']);
                    self::update_reclaimed_profile($userid, $handle, $params['email']);
                    break;
                default:
                    break;
            }
        }
    }



    /**
     * Notifies an archived user if their email was used to register a new account.
     * 
     * @param string $email The email of the archived account to remove.
     * @param string $handle Username of the newly created account.
     */
    function notify_archived_user($email, $handle)
    {
        // This query gets us the correct row from qa_accountreclaim
        $userinfo = qa_db_single_select(array(
            'columns' => array('^accountreclaim.userid', '^accountreclaim.email', '^accountreclaim.reclaimcode'),
            'source' => '^accountreclaim WHERE ^accountreclaim.email=$',
            'arguments' => array($email),
            'single' => true,
        ));

        // Send an email to the archived user's address
        if (!qa_send_notification($userinfo['userid'], $email, qa_lang('qa-ar/archive_notify_name'), qa_lang('qa-ar/archive_notify_subject'), qa_lang('qa-ar/archive_notify_body'), array(
            '^username' => $handle,
            '^timestamp' => date("h:i:sa"),
            '^interval' => '8', // TODO: Replace this with a qa_opt() of the interval for how often unconfirmed accounts are deleted.
        ))) {
            qa_fatal_error('Could not send Archive Registration Notification email');
        }
    }



    /**
     * Deletes the entry in ^accountreclaim corresponding to the email provided.
     * 
     * @param string $email The email of the archived account to remove.
     */
    function delete_archived_entry($email)
    {
        // Delete this user's entry in the account reclaim table
        $sql = 'DELETE FROM ^accountreclaim WHERE ^accountreclaim.email=$';
        qa_db_query_sub($sql, $email);
    }



    /**
     * Deletes any falsely created accounts that use the same email as the
     *  reclaimed account.
     * 
     * Currently, it is possible for someone to create an account using an email
     *  that is associated with an archived account, even if they cannot confirm
     *  that email. So, if an archived user reclaims their account, we need to
     *  delete any accounts that have been created with that email address.
     * 
     * @param string $userid UserID of the (correct) reclaimed account.
     * @param string $email Email address of the reclaimed account.
     */
    function delete_alt_accounts($userid, $email)
    {
        // Do not delete the correct account!
        $sql = 'DELETE FROM ^users WHERE ^users.userid!=# AND ^users.email=$';
        qa_db_query_sub($sql, $userid, $email);
    }



    /**
     * Updates a users profile after they have reclaimed their account.
     *
     * Removes the 'This is an archived user' blurb.
     * 
     * @param string $userid UserID of the account reclaimed.
     * @param string $handle New username of the account.
     * @param string $email Email address associated with the account.
     */
    function update_reclaimed_profile($userid, $handle, $email)
    {
        // Remove the 'This is an archived user' blurb from their profile
        $sql = 'UPDATE ^userprofile SET ^userprofile.content=\'This user reclaimed their account!\' WHERE ^userprofile.userid=$ AND ^userprofile.title=\'about\'';
        qa_db_query_sub($sql, $userid);
    }
}

<?php

/*
    File: qa-plugin/auto-prune-accounts/qa-apa-event.php
    Description: Handles events related to automatic removal of accounts.
*/




// For qa_db_read_all_assoc(), qa_db_query_sub()
require_once QA_INCLUDE_DIR . 'db/users.php';
// For qa_delete_user()
require_once QA_INCLUDE_DIR . 'app/users-edit.php';



class qa_apa_events
{
    // The CRON job string to be executed
    private $cron_job = '* * * * *';

    /**
     * Handles events relating to Automatic Account Pruning
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
        switch ($event) {
            case 'u_register':
                // Start some kind of countdown to delete their account
                self::delete_unconfirmed_accounts(qa_opt('qa_apa_timeout_minutes'), $userid);
                break;
            case 'u_confirmed':
                // Stop the aforementioned countdown
                self::delete_unconfirmed_accounts(qa_opt('qa_apa_timeout_minutes'), $userid);
                break;
            case 'u_login':
                self::delete_unconfirmed_accounts(qa_opt('qa_apa_timeout_minutes'), $userid);
            default:
                break;
        }
    }



    /**
     * Starts the Auto-Prune process CRON job.
     */
    static function start_autoprune()
    {
        /*
        CRON jobs have the following parameters:

            * * * * * "command to be executed"
            | | | | |
            | | | | ----- Day of week (0 - 7) (Sunday=0 or 7)
            | | | ------- Month (1 - 12)
            | | --------- Day of month (1 - 31)
            | ----------- Hour (0 - 23)
            ------------- Minute (0 - 59)
        */
    }


    /**
     * Stops the Auto-Prune process CRON job.
     */
    static function stop_autoprune()
    {
    }



    /**
     * Deletes all users who have not confirmed their account within the specified
     *  amount of time
     * 
     * @param string $timeout Maximum amount of time before an unconfirmed account is deleted.
     * @param string $userid UserID to NOT delete, because this user just created an account!
     */
    function delete_unconfirmed_accounts($timeout, $userid)
    {
        // Fetch all users who have not confirmed their email within the timeout
        // More on DATE_SUB(): https://www.w3schools.com/sql/func_mysql_date_sub.asp
        $sql = 'SELECT * FROM ^users WHERE level = 0 AND DATE_SUB(now(), INTERVAL $ MINUTE) > created AND flags & $ AND userid!=$';
        $unconfirmed_users = qa_db_read_all_assoc(qa_db_query_sub($sql, $timeout, QA_USER_FLAGS_MUST_CONFIRM, $userid));

        // $sql = 'SELECT userid, email FROM ^accountreclaim';
        // $archived_users = qa_db_read_all_assoc(qa_db_query_sub($sql));

        // Delete everyone; Recall that the query only returned accounts who are older than the timeout
        foreach ($unconfirmed_users as $user) {
            qa_delete_user($user['userid']);
        }
    }
}

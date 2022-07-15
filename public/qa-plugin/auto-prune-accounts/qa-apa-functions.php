<?php
/*
    File: qa-plugin/auto-prune-accounts/qa-apa-functions.php
    Description: Contains custom functions and replacements for non-overridable
        functions used in the Auto-Prune process, such as functions that
        originally modified user-related tables and now modify custom tables.
*/



// For qa_db_read_all_assoc(), qa_db_query_sub()
require_once QA_INCLUDE_DIR . 'db/users.php';
// For qa_delete_user
require_once QA_INCLUDE_DIR . 'app/users-edit.php';



/**
 * Starts the Auto-Prune process CRON job.
 */
function start_autoprune()
{
}


/**
 * Stops the Auto-Prune process CRON job.
 */
function stop_autoprune()
{
}



/**
 * Deletes all users who have not confirmed their account within the specified
 *  amount of time
 * 
 * @param int $timeout  
 * @param int $units Units to measure the timeout in. Valued values: https://www.w3schools.com/sql/func_mysql_date_sub.asp
 */
function delete_uncomfirmed_accounts($timeout, $units)
{
    $sql = 'SELECT userid,handle,email FROM ^users WHERE level = 0 AND date_sub(now(), INTERVAL $ $) > created AND NOT flags & 1';
    $unconfirmed_users = qa_db_read_all_assoc(qa_db_query_sub($sql, $timeout, $units));

    // $sql = 'SELECT userid, email FROM ^accountreclaim';
    // $archived_users = qa_db_read_all_assoc(qa_db_query_sub($sql));
    foreach ($unconfirmed_users as $user) {
        qa_delete_user($user['userid']);
    }
}

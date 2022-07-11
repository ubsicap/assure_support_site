<?php

require_once QA_INCLUDE_DIR . 'db/users.php';

// Fetch a user from the Account Reclaim table by their email
function qa_db_ac_user_find_by_email($email) {

    print("TODO: Make sure the table name is correct!!! Also that it contains the parameter `" . $email . "`");

    return qa_db_read_all_values(qa_db_query_sub(
            'SELECT userid FROM ^accountreclaim WHERE email=$',
            $email
        ));
}


/**
 * Start the 'Reclaim Account' process for $userid, sending reset code
 * 
 * Original function in qa-include/app/users-edit.php
 * 
 * @param $userid
 * @return mixed
 */
function qa_ar_start_reclaim_user($userid) {

}
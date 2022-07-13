<?php
/*
    File: qa-plugin/account-reclaim/qa-ar-functions.php
    Description: Contains custom functions and replacements for non-overridable
        functions used in the Account Reclaim process, such as functions that
        originally modified user-related tables and now modify custom tables.
*/



require_once QA_INCLUDE_DIR . 'db/users.php';



/**
 * Return the ids of all users in the Account Reclaim table which match $email (should be one or none)
 * 
 * Modified Function. Original:
 *      qa-include\db\users.php:qa_db_user_find_by_email($email)
 *
 * @param $email
 * @return array
 */
function qa_ar_db_user_find_by_email($email)
{
    return qa_db_read_all_values(qa_db_query_sub(
        'SELECT userid FROM ^accountreclaim WHERE email=$',
        $email
    ));
}

/**
 * Return if the email matches an entry on the archived email list
 *
 * @param $email
 * @return bool
 */
function qa_ar_db_is_archived_email($email)
{
    $returnVal = qa_db_read_one_value(qa_db_query_sub(
		'SELECT COUNT(*) FROM ^accountreclaim WHERE email=$', $email
	));
    return $returnVal;
}

/**
 * Return the flag value of the lastcreate of the given email, WARNING: Assumes the email is in the list
 *
 * @param $email
 * @return array
 */
function qa_ar_db_get_email_flag($email)
{
    return qa_db_read_one_value(qa_db_query_sub(
		'SELECT lastcreate FROM ^accountreclaim WHERE email=$', $email
	));
}

/**
 * Remove an account by email from qa_accountreclaim
 *
 * @param $email
 * @return none
 */
function qa_ar_db_remove_email($email)
{
    qa_db_query_sub(
		'DELETE FROM ^accountreclaim WHERE email=#',
		$email
	);
}



/**
 * Set $field of $userid to $value in the database users table. If the $fields parameter is an array, the $value
 * parameter is ignored and each element of the array is treated as a key-value pair of user fields and values.
 * 
 *  Modified Function. Original:
 *      qa-include\db\users.php:qa_db_user_set($userid, $fields, $value = null)
 * 
 * @param mixed $userid
 * @param string|array $fields
 * @param string|null $value
 */
function qa_ar_db_user_set($userid, $fields, $value = null)
{
    if (!is_array($fields)) {
        $fields = array(
            $fields => $value,
        );
    }

    $sql = 'UPDATE ^accountreclaim SET ';
    foreach ($fields as $field => $fieldValue) {
        $sql .= qa_db_escape_string($field) . ' = $, ';
    }
    $sql = substr($sql, 0, -2) . ' WHERE userid = $';

    $params = array_values($fields);
    $params[] = $userid;

    qa_db_query_sub_params($sql, $params);
}

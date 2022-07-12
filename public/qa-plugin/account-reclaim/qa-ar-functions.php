<?php
/*
    File: qa-plugin/account-reclaim/qa-ar-functions.php
    Description: Contains custom replacements for non-overridable functions used
        in the Account Reclaim process, such as functions that originally
        modified user-related tables and now modify custom tables.
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
function qa_db_ac_user_find_by_email($email) {
    return qa_db_read_all_values(qa_db_query_sub(
            'SELECT userid FROM ^accountreclaim WHERE email=$',
            $email
        ));
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

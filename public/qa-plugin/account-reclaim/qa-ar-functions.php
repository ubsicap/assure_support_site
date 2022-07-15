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
 * Return the flag value of the lastreclaim of the given email, WARNING: Assumes the email is in the list
 *
 * @param $email
 * @return DateTime
 */
function qa_ar_db_get_email_flag($email)
{
    $dateTime = qa_db_read_one_value(qa_db_query_sub(
		'SELECT lastreclaim FROM ^accountreclaim WHERE email=$', $email
	));

    if($dateTime == null) //null, return earliest time
        return (new DateTime())->setTimestamp(0); //1970
    else //return as a DateTime object
        return new DateTime($dateTime);
}

/**
 * Return the flag value of the lastreclaim of the given email, WARNING: Assumes the email is in the list
 *
 * @param $email
 * @return array
 */
function qa_ar_db_update_email_flag($email)
{
    $time = new DateTime(); //current time
    return qa_db_query_sub(
		'UPDATE ^accountreclaim SET lastreclaim=NOW() WHERE email=$', $email
	);
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
		'DELETE FROM ^accountreclaim WHERE email=$',
		$email
	);
}

/**
 * Swap the old username to the new username when an account is migrated
 * 
 * @param $oldName (i.e. anon123456)
 * @param $newName (i.e. danielM)
 * @return none
 */
function qa_ar_db_swap_name($oldName, $newName)
{
    debug_to_console("UPDATE ^posts SET content=REGEXP_REPLACE(content, $oldName, $newName)");
    //any post with a title/content with the old name gets swapped with the new name
    //this relies on that the only time the old name is obscure enough that any match actually refers to the username
    //qa_db_query_sub(
	//	"UPDATE ^posts SET content=REGEXP_REPLACE(content, $oldName, $newName), title=REGEXP_REPLACE(title, $oldName, $newName)
    //    WHERE content like '%$oldName%' or title like '%$oldName%'"
	//);

    debug_to_console("UPDATE ^words SET word=REGEXP_REPLACE(word, $oldName, $newName)$ WHERE word like '%$oldName%'");
    //now that the posts have changed we have to modify the wordid in the words table
    //qa_db_query_sub(
    //    "UPDATE ^words SET word=REGEXP_REPLACE(word, $oldName, $newName)$ WHERE word like '%$oldName%'"
    //);
}

/**
 * Get the handle of the anonymous user with this userid
 * 
 * @param $userid
 * @return string (the handle of the anon name, e.g. anon123456)
 */
function qa_ar_db_get_anon($userId)
{
    return qa_db_read_one_value(qa_db_query_sub(
		'SELECT handle FROM ^users WHERE userid=$', $userId
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


/**
 * Simple helper to debug to the console
 *
 * @param $data object, array, string $data
 * @param $context string  Optional a description.
 *
 * @return string
 */
function debug_to_console($data, $context = 'Debug in Console') 
{

    // Buffering to solve problems frameworks, like header() in this and not a solid return.
    ob_start();

    $output  = 'console.info(\'' . $context . ':\');';
    $output .= 'console.log(' . json_encode($data) . ');';
    $output  = sprintf('<script>%s</script>', $output);

    echo $output;
}
<?php
/*
    File: qa-plugin/auto-prune-accounts/qa-apa-functions.php
    Description: Contains custom functions and replacements for non-overridable
        functions used in the Auto-Prune process, such as functions that
        originally modified user-related tables and now modify custom tables.
*/





/**
 * Determines if the specified user has confirmed their email and verified their account.
 * 
 * @param string $userid The UserID of the account to check.
 * @return bool True if the user has confirmed their email, else false.
 */
function has_confirmed_email($userid)
{
    $sql = 'SELECT flags FROM ^users WHERE userid=$';
    $flags = qa_db_query_sub($sql, $userid)['flags'];

    return !(!($flags & QA_USER_FLAGS_EMAIL_CONFIRMED) && ($flags & QA_USER_FLAGS_MUST_CONFIRM));
}

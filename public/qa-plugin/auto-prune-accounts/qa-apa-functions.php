<?php
/*
    File: qa-plugin/auto-prune-accounts/qa-apa-functions.php
    Description: Contains custom functions and replacements for non-overridable
        functions used in the Auto-Prune process, such as functions that
        originally modified user-related tables and now modify custom tables.
*/



require_once QA_INCLUDE_DIR . 'db/users.php';



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

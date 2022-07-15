<?php

/*
    File: qa-plugin/auto-prune-accounts/qa-apa-event.php
    Description: Handles events related to automatic removal of accounts.
*/



require_once QA_PLUGIN_DIR . 'account-reclaim/qa-apa-functions.php';



class qa_apa_archive_cleanup
{
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
        // TODO: Replace this with a condition to only handle events relating to unconfirmed accounts
        if (true) {
            switch ($event) {
                case 'u_register':
                    break;
                case 'u_confirmed':
                    break;
                default:
                    break;
            }
        }
    }
}

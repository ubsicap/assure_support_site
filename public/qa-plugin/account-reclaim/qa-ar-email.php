<?php
/*
    File: qa-plugin/account-reclaim/qa-ar-email.php
    Description: Warns users if they register with an archived account
*/

//database reading/setting functions
require_once QA_PLUGIN_DIR . 'account-reclaim/qa-ar-functions.php';

class qa_ar_filter
    {
    /**
     * Check if an email matches an archived account
     * 
     * @param $email the email entered by the user
     * @return error Message if email belongs to an archived user, otherwise nothing is returned
    */
    public function filter_email(&$email, $olduser)
    {
        return ('test <br> test');
        //check if email belongs to an archived account
        if(qa_ar_db_is_archived_email($email))
        {
            $lastAttemptTime = qa_ar_db_get_email_flag($email); //null or last register time attempt
            $minD = ((new DateTime())->getTimestamp() - $lastAttemptTime->getTimestamp())/60; //difference in Minutes (unix compare)            
            if($minD >= 3) //not a recent login attempt
            {
                qa_ar_db_update_email_flag($email); //update the flag
                return qa_lang('qa-ar/archived_warning'); //warn the user
            }
            //otherwise a recent login attempt, let the user register.
        }
        //otherwise user is valid
        //override qa_create_new_user to remove the email from the archived list
    }
}

/**
 * Simple helper to debug to the console
 *
 * @param $data object, array, string $data
 * @param $context string  Optional a description.
 *
 * @return string
 */
function debug_to_console($data, $context = 'Debug in Console') {

    // Buffering to solve problems frameworks, like header() in this and not a solid return.
    ob_start();

    $output  = 'console.info(\'' . $context . ':\');';
    $output .= 'console.log(' . json_encode($data) . ');';
    $output  = sprintf('<script>%s</script>', $output);

    echo $output;
}


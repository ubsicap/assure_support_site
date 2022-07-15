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
        //check if email belongs to an archived account
        if(qa_ar_db_is_archived_email($email))
        {
            $lastAttemptTime = qa_ar_db_get_email_flag($email); //null or last register time attempt
            $minD = ((new DateTime())->getTimestamp() - $lastAttemptTime->getTimestamp())/60; //difference in Minutes (unix compare)            
            if($minD >= qa_opt('qa_ar_register_archived_timeout')) //not a recent login attempt
            {
                qa_ar_db_update_email_flag($email); //update the flag
                return qa_lang('qa-ar/archived_warning'); //warn the user
            }
            //otherwise a recent login attempt, let the user register.
        }
        //otherwise user is valid
    }
}

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
        return qa_ar_db_is_archived_email($email);
        if(qa_ar_db_is_archived_email($email))
        {
            //if so check the last login attempt time, if it was recent (< 10 minutes)
            return qa_lang('qa-ar/archived_warning'); //user exists
        }
        //otherwise user is valid, override qa_create_new_user to remove the email from the archived list
    }
}



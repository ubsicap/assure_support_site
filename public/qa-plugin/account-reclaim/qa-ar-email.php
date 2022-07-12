<?php
/*
        File: qa-plugin/account-reclaim/qa-ar-email.php
        Description: Warns users if they register with an archived account
*/

//needs access to database to read from 

class qa_ar_filter
{
    /**
     * Check 
     * 
     * @param $email the email entered by the user
     * @return error Message if email belongs to an archived user, otherwise nothing is returned
     */
    public function filter_email(&$email, $olduser)
	{
        return qa_lang_html('qa-ar/archived_warning'); //user exists
	}
}



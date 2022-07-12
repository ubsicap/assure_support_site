<?php
/*
        File: qa-plugin/account-reclaim/qa-ar-email.php
        Description: Warns users if they register with an archived account
*/

//needs access to database to read from 

class qa_ar_filter
{
    static $callCount = 0; //how many times the function has been called

    /**
     * Check 
     * 
     * @param $email the email entered by the user
     * @return error Message if email belongs to an archived user, otherwise nothing is returned
     */
    public function filter_email(&$email, $olduser)
	{
        //check if email belongs to an archived account
        //if so check the last login attempt
        //return nothing otherwise

        self::$callCount++;
        return strval(self::$callCount);
        //return qa_lang('qa-ar/archived_warning'); //user exists
	}
}



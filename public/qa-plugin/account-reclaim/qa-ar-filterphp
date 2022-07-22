<?php
/*
    File: qa-plugin/account-reclaim/qa-ar-filter.php
    Description: Warns users if they register with an archived account
*/



// For qa_ar_db_is_archived_email()
require_once QA_PLUGIN_DIR . 'account-reclaim/qa-ar-functions.php';



class qa_ar_filter
{
    /**
     * Returns an error if the email address belongs to an archived account and
     *  if the confirmation box has been fulfilled.
     * 
     * @param string $email The email entered by the user
     * @return mixed Message if email belongs to an archived user, otherwise nothing is returned
     */
    public function filter_email(&$email, $olduser)
    {
        // This doesn't apply for users who are attempting to change their email
        if (empty($olduser)) {
            // Check if email belongs to an archived account and if the confirmation box has been filled
            if (qa_ar_db_is_archived_email($email) && qa_post_text('custom_confirm') !== qa_lang('qa-ar/do_not_reclaim')) {
                return qa_lang_sub('qa-ar/archived_warning', $email);
            }
        }
    }
}

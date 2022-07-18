
<?php

/*
    File: qa-plugin/auto-prune-accounts/qa-apa-filters.php
    Description: Automatically deletes old, unverified accounts if a user attempts
        to register with the same email again.
*/



// For qa_delete_user()
require_once QA_INCLUDE_DIR . 'app/users-edit.php';
// For qa_db_single_select()
require_once QA_INCLUDE_DIR . 'db/selects.php';



class qa_apa_filters
{
    /**
     * Checks to see if the email being used to register an account belongs to an existing account.
     *  If it does, it deletes the existing account if it is unverified and old.
     * 
     * @param string $email Email entered by the user during registration
     * @param mixed $olduser Previous user info, if this function is being called when a user updates their email
     * @return mixed
     */
    public function filter_email(&$email, $olduser)
    {
        // This filter doesn't apply to users changing their email, so quickly exit that case
        if (!empty($olduser) || !qa_opt('qa_apa_enable_autoprune')) {
            return;
        }

        // This query attempts to fetch the existing user with this email address
        $existingUser = qa_db_single_select(array(
            'columns' => array('^users.userid', '^users.created', '^users.flags'),
            'source' => '^users WHERE ^users.email=$',
            'arguments' => array($email),
            'single' => true,
        ));

        // If the user exists and their account must confirm email, check how old the account is
        if (!empty($existingUser) && ($existingUser['flags'] & QA_USER_FLAGS_MUST_CONFIRM)) {
            $created = new DateTime($existingUser['created']);
            $difference_minutes = ((new DateTime())->getTimestamp() - $created->getTimestamp()) / 60;

            // If their account has been unconfirmed long enough, just delete it
            if ($difference_minutes >= qa_opt('qa_apa_timeout_minutes')) {
                qa_delete_user($existingUser['userid']);

                // No error has occurred, so we don't need to return anything
            } else {
                // Otherwise, the existing account can still be verified, so return an error
                return qa_lang_sub('qa-apa/unconfirmed_email_exists', qa_opt('qa_apa_timeout_minutes'));
            }
        }
    }
}


<?php

/*
    English language file for Account Reclaim Plugin
*/

return array(
    'recover_page_title' => 'Reclaim an Existing Discourse Account',
    'email_label' => 'Enter the email address associated with your original account',
    'send_recover_note' => 'An email will be sent to you with further instructions',
    'send_recover_button' => 'Send Recover Account Email',
    'recover_page_description' => 'In here goes the text that describes the account reclaim process.
<br>Things to include: <br>1 - Explain what "Account Recover" fully means and how this process is going to work.
<br>2 - Remind them that if they choose to continue, their existing account will be lost. Prompt them to the opportunity to go back(?).
<br>3 - When they reclaim, they will have access to all of their previous posts and data.
<br>4 - Their data is currently anonymized, and they will be given the option to change their username.
<br>5 - Format this text cleanly, like ending with a horizontal line.
<br>6 - Explain that they must be LOGGED OUT to reclaim an account',
    'already_logged_in' => 'You cannot reclaim an account- you are already logged in!',
    'recover_body' => "Please click below to recover your previous Discourse account and migrate it to ^site_title.\n\n^url\n\nAlternatively, enter the code below into the field provided.\n\nCode: ^code\n\nIf you did not ask to reset your password, please ignore this message.\n\nThank you,\n^site_title",
    'recover_subject' => '^site_title - Recover Existing Account', 
    'reclaim_page_title' => 'Finish Reclaiming Your Account',
    'archived_warning' => 'Email belongs to an archived account',
);

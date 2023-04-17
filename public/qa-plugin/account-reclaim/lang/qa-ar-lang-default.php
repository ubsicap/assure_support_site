
<?php

/*
    English language file for Account Reclaim Plugin
*/

return array(
    'recover_page_title' => 'Reclaim your existing Paratext Support account!',
    'recover_email_label' => 'Enter the email address that you used with your support.paratext.org account:',
    'reclaim_email_label' => 'A recovery code has been sent to the following email address:',
    'reclaim_code_label' => 'Enter the recovery code here:',
    'recover_user_not_found' => 'Could not find account associated with that email. Please try another address.',
    'send_recover_note' => 'An email will be sent to you with further instructions.',
    'send_recover_button' => 'Send Recover Account Email',
    'recover_page_description' => "The Paratext Support Forum (support.paratext.org) was a secure forum. However, anyone can view all posts on SITE_NAME without a login. All posts and comments from the Paratext Support Forum have been moved to SITE_NAME. To protect your security, we have removed names from all posts and comments. On SITE_NAME, it is possible to reclaim your support.paratext.org account and retain access to your posts, comments, and badges—or to start afresh with a new account.<br><br><b>To reclaim your history of posts, comments and badges from support.paratext.org</b>, you will need to enter the email address that you used with your support.paratext.org account in the box below. We will send you a confirmation email describing the next steps. You will have the option to either remain anonymous or to specify an identifiable user name according to your security needs.<br><br>If you do not wish to reclaim your history of posts, comments and badges, you may do so by clicking <a href='https://staging.support.bible/index.php?qa=register' rel='noopener noreferrer'>create an account</a>. Your original posts will still be visible on SITE_NAME but they will not be associated with your name or email address and you will not be able to modify them.<br><b>NOTE:</b> If you create an account using the same email address that you used with your support.paratext.org account rather than reclaiming an account, you will lose access to the history from your support.paratext.org account.<hr>",
    'already_logged_in' => 'You cannot reclaim an account- you are already logged in!',
    'recover_name' => 'Paratext Support User',
    'recover_body' => "Please click below to recover your previous support.paratext.org account and migrate it to ^site_title.\n\n^url\n\nAlternatively, enter the code below into the field provided.\n\nCode: ^code\n\nIf you did not ask to reset your password, please ignore this message.\n\nThank you,\n^site_title",
    'recover_subject' => '^site_title - Recover Existing Account',
    'reclaim_page_title' => 'Finish Reclaiming Your Account',
    'reclaim_enter_new_username' => 'We recommend that you update your username. The current username for your account is provided below. You may choose to keep this username, or create a new one. Any occurrences of your original username on this forum will be replaced by the username you choose here. This <b>cannot</b> be undone, even if you change your username later.<br><br>Please note that this forum is publicly available, so be mindful about choosing a username that contains any identifying information.',
    'reclaim_set_new_pass' => 'Set a new password for this account.',
    'reclaim_finish' => 'Update Username and Password',
    'admin_redirect_page' => 'Location to redirect users after account reclaim. Leave blank to redirect to homepage.',
    'admin_register_archived_timeout' => "Number of minutes that a user has to register with an archived email before they are warned again.",
    'admin_captcha_on_recover' => 'Use CAPTCHA on account recovery',
    'archived_warning' => "This email address is used on support.paratext.org.<br><br>If you would like to keep your history from this account, <b><a style='color:inherit' href='https://staging.support.bible/index.php?qa=recover-account' rel='noopener noreferrer'>reclaim your support.paratext.org account</a></b>.<br><br>You will permanently lose acccess to your history from your support.paratext.org account if you continue and create a new account. To lose your support.paratext.org history, you must type the following in the box below: <b>^</b>",
    'archive_notify_name' => 'Paratext Support Forum User',
    'archive_notify_subject' => '^site_title - An account has been registered with your email',
    'archive_notify_body' => "An account has been registered at ^site_url using this email address. Details are listed below.\n\n\tEmail: ^email\n\tUsername: ^username\n\tTimestamp: ^timestamp (UTC)\n\nIf this was your doing, please disregard this message.\n\nIf you did not authorize this, do not worry. All unverified accounts are deleted after ^interval hours.",
    'do_not_reclaim' => 'DO NOT RECLAIM',
);

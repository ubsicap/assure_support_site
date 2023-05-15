
<?php

/*
    English language file for SSO Authentication plugin
*/
$provider = isset($_SESSION['provider']) ? $_SESSION['provider'] : "Google or Facebook";

return array(
    'client_id' => 'Client id:',
    'client_secret' => 'Client secret',
    'google_login' => 'Log in with Google',
    'google_login_enabled' => 'Enable Google login',
    'facebook_login' => 'Log in with Facebook',
    'facebook_login_enabled' => 'Enable facebook login',
    'paratext_login' => 'Log in with Paratext',
    'paratext_login_enabled' => 'Enable Paratext login',
    'finish_creating_account' => 'Finish Creating Account',
    'recover_page_title' => 'Reclaim Your Account or Create A New One',
    'create_enter_new_username' => '<p>To sign up using '.$provider.', please provide a username to finish creating your account.<br><br>We recommend that you update your username. The current username for your account is provided below. You may choose to keep this username, or create a new one. <br><br>Please note that this forum is publicly available, so be mindful about choosing a username that contains any identifying information. <br><br><b>Username:<b></p>',
    'already_logged_in_new' => 'You cannot create an account- you are already logged in!',
    'invalid_authorization_code' => 'Your authorization code for logging in is invalid or has expired!',
    'already_logged_in' => 'You cannot reclaim your account- you are already logged in!',
    'reclaim_account' => 'Reclaim Your Account',
    'reclaim_enter_new_username' => '<p>We found that this email address is used on <a href="https://support.paratext.org" rel="noopener noreferrer">Support Paratext.</a><br><br>If you would like to keep your history from this account, you can click the <b>Reclaim Your Account</b> button after filling the form. Otherwise, click the <b>Create New Account</b> button to  lose access to your history from your support.paratext.org account.<br><br>We recommend that you update your username. The current username for your account is provided below. You may choose to keep this username, or create a new one. Any occurrences of your original username on this forum will be replaced by the username you choose here. This <b>cannot</b> be undone, even if you change your username later.<br><br>Please note that this forum is publicly available, so be mindful about choosing a username that contains any identifying information. <br><br><b>Username:<b></p>',
    'create_account' => 'Create New Account',
    'connect_email' => 'Connect your account to an email address.',
);

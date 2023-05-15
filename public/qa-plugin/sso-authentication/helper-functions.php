<?php
const REDIRECT_URL = 'qa-plugin/sso-authentication/login-callback.php';
const IS_GOOGLE = true;

// helper function
function getClient($isGoogle)
{
    require_once $_SERVER['DOCUMENT_ROOT'] . '/qa-include/qa-base.php';
    require_once  $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
    if ($isGoogle) {

        $redirect_uri = qa_opt('site_url') . REDIRECT_URL;
        $googleClient = new Google_Client();
        $googleClient->setClientId(urlencode(qa_opt('google_authentication_client_id')));
        $googleClient->setClientSecret(urlencode(qa_opt('google_authentication_client_secret')));
        $googleClient->setRedirectUri($redirect_uri);
        $googleClient->addScope("email");
        $googleClient->addScope("profile");

        return $googleClient;
    } else {
        $fbClient = new Facebook\Facebook([
            'app_id' => qa_opt('facebook_authentication_client_id'),
            'app_secret' => qa_opt('facebook_authentication_client_secret'),
            'default_graph_version' => 'v2.10',
        ]);

        return $fbClient;
    }
}

// generate authorization url for user to login with SSO
function generateAuthUrl($isGoogle)
{
    require_once $_SERVER['DOCUMENT_ROOT'] . '/qa-include/qa-base.php';
    $redirect_uri = qa_opt('site_url') . REDIRECT_URL;
    // make sure state in session will not change all the time

    if ($isGoogle) {
        // generate authorization url for google
       
        $scope = urlencode('email profile');
        return 'https://accounts.google.com/o/oauth2/v2/auth?'
            . 'response_type=code'
            . '&client_id=' . qa_opt('google_authentication_client_id')
            . '&redirect_uri=' . $redirect_uri
            . '&scope=' . $scope
            . '&prompt=consent'
            . '&access_type=offline';
    } else {
        $fbClient =  getClient(!IS_GOOGLE);
        $fbHelper = $fbClient->getRedirectLoginHelper();
        $loginUrl = $fbHelper->getLoginUrl($redirect_uri, ['email', 'public_profile']);
        return $loginUrl;
    }
}


// validate user's access token for Facebook
function validateFBRequest($request)
{
    $response = file_get_contents($request);

    if ($response === false) {
        return null;
    } else {
        $data = json_decode($response, true);
        return isset($data['error']) ? null : $data;
    }
}


// after user grants login, use accesstoken to get users' info
function logInWithSSO($isGoogle)
{
    require_once $_SERVER['DOCUMENT_ROOT'] . '/qa-include/qa-base.php';
    require_once  $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
    if ($isGoogle) {
        // retrieve google client
        require_once $_SERVER['DOCUMENT_ROOT'] . '/qa-include/qa-base.php';
        require_once  $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
        $client = new Google\Client();
        $client->setAuthConfig('google-auth.json');
        $client->addScope(['email', 'profile']);
        $redirect_uri = qa_opt('site_url') . REDIRECT_URL;
        $client->setRedirectUri($redirect_uri);
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

        if (!isset($token['access_token'])) {
            // to handle case when auth code is invalid, ask user to grant permission again
            header('Location: ' . filter_var(generateAuthUrl(IS_GOOGLE), FILTER_SANITIZE_URL));
        }
        $client->setAccessToken($token['access_token']);
        $_SESSION['google_token'] = $token;

        // Make the HTTP GET request to the API endpoint to get user info
        $url = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . urlencode($token['access_token']);
        $response = file_get_contents($url);
        $userInfo = json_decode($response, true);
        // register user in Support.Bible
        registerUser($userInfo, 'google');
    } else {
        // get user accesstoken
        $auth = 'client_id=' . qa_opt("facebook_authentication_client_id") . '&redirect_uri=' . qa_opt('site_url') . REDIRECT_URL . '&client_secret=' . qa_opt("facebook_authentication_client_secret") . '&code=' . $_SESSION["code"];

        $token = validateFBRequest('https://graph.facebook.com/v14.0/oauth/access_token?' . $auth);
        if (is_null($token)) {
            echo 'Error: Authentication failed! (Failed to get user access token.)';
            exit;
        }
        $_SESSION['fb_token'] = $token;

        // get user data from Facebook using user accesstoken
        $data = validateFBRequest('https://graph.facebook.com/v14.0/me?' . $auth . '&access_token=' . $token['access_token'] . '&fields=id,name,email');
        if (is_null($data)) {
            echo 'Error: Log in failed! (Failed to get user data.)';
            exit;
        }
        registerUser($data, 'facebook');
    }
}

// register user to Support.Bible site
function registerUser($user_info, $provider)
{
    require_once $_SERVER['DOCUMENT_ROOT'] . '/qa-include/qa-base.php';
    require_once  $_SERVER['DOCUMENT_ROOT'] . '/qa-plugin/account-reclaim/qa-ar-functions.php';
    require_once  $_SERVER['DOCUMENT_ROOT'] . '/qa-include/db/users.php';

    // Check if the user already has an account on the site  
    $_SESSION['provider'] = $provider;

    // make sure we get username and id from Google/FB
    if (!isset($user_info['name']) || !isset($user_info['id'])) {
        qa_fatal_error('Authentication failed! (failed to get name and id)');
    }

    $_SESSION['username'] = $user_info['name'];
    $_SESSION['id'] = $user_info['id'];

    // for users log in with FB but do not connect emails to their FB
    if (!isset($user_info['email'])) {
        registerFBUsersWithNoEmail($provider, $user_info['id']);
    } else {
        $existingAccountIds = qa_db_user_find_by_email($user_info['email']);
        // in case users logged with FB, and their FB do not connect to email
        $_SESSION['user_email'] = $user_info['email'];

        // No account exists; create a new one
        if (empty($existingAccountIds)) {
            // Check if the user is archived
            $matchingUsers = qa_ar_db_user_find_by_email($_SESSION['user_email']);

            // Make sure there is only one match
            if (count($matchingUsers) == 1) {
                $_SESSION['id'] = $matchingUsers[0];
                header('Location: ' . filter_var(qa_opt('site_url') . 'recover-account-sso', FILTER_SANITIZE_URL));
            } else {
                header('Location: ' . filter_var(qa_opt('site_url') . 'create-account-sso', FILTER_SANITIZE_URL));
            }
        } else {
            require_once  $_SERVER['DOCUMENT_ROOT'] . '/qa-include/app/users.php';
            // The user already has an account on the site; log them in with provider
            $users = array_values(qa_db_user_get_userid_handles($existingAccountIds));
            qa_set_logged_in_user($existingAccountIds[0], $users[0], true, $provider);
            header('Location: ' . filter_var(qa_opt('site_url'), FILTER_SANITIZE_URL));
        }
    }
}

function registerFBUsersWithNoEmail($source, $identifier)
{
    require_once $_SERVER['DOCUMENT_ROOT'] . '/qa-include/qa-base.php';
    require_once  $_SERVER['DOCUMENT_ROOT'] . '/qa-include/db/users.php';
    require_once  $_SERVER['DOCUMENT_ROOT'] . '/qa-include/app/users.php';
    $users = qa_db_user_login_find($source, $identifier);
    $countusers = count($users);
    switch ($countusers) {
        case 0:
            // new users
            header('Location: ' . filter_var(qa_opt('site_url') . 'create-account-sso', FILTER_SANITIZE_URL));
            break;
        case 1:
            // user exsits so log then in
            qa_set_logged_in_user($users[0]['userid'], $users[0]['handle'], true, $source);
            header('Location: ' . filter_var(qa_opt('site_url'), FILTER_SANITIZE_URL));
            break;
        default:
            // map to more than one user, should never happen
            qa_fatal_error('External login mapped to more than one user');
    }
}

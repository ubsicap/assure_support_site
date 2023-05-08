<?php

class sso_authentication_login
{
	// used to check if users log in with Google, true for Google, false for Facebook
	const IS_GOOGLE = true;
	const REDIRECT_URL = 'qa-plugin/sso-authentication/login-callback.php';
	private $googleClient = null; // to make sure client will not be created multiple times
	private $fbClient = null;

	// called early on during every page request 
	// if a user is not currently logged in
	function check_login()
	{
		if (isset($_SESSION['code'])) {
			if (isset($_SESSION['scope']) && strpos($_SESSION['scope'], 'google'))
				$this->logInWithSSO(self::IS_GOOGLE);
			else
				$this->logInWithSSO(!self::IS_GOOGLE);
		}
	}

	// called early on during every page request 
	// after a user is logged in
	function match_source($source)
	{
		if (isset($_SESSION['logout'])) {
			if (strcmp($source, 'google') === 0) {
				$access_token = openssl_decrypt($_COOKIE['google_access_token'], 'aes-256-cbc', file_get_contents('token.key', true), OPENSSL_RAW_DATA, '');
				setcookie('google_access_token', '', time() - 3600);
				// retrieve google client
				$this->googleClient =  $this->getClient(self::IS_GOOGLE);
				$this->googleClient->setAccessToken($access_token);
				// expire token so that user needs to grant permission next time for google
				$this->googleClient->revokeToken();
			} else if (strcmp($source, 'facebook') === 0 && isset($_COOKIE['fb_access_token'])) {
				// decipher access token got from cookie
				$access_token = openssl_decrypt($_COOKIE['fb_access_token'], 'aes-256-cbc', file_get_contents('token.key', true), OPENSSL_RAW_DATA, '');

				// remove cookie for access token
				setcookie('fb_access_token', '', time() - 3600);

				// check if access token is still valid in case user has already logged out 
				// or access token has expired
				$check_access_token = $this->validateFBRequest("https://graph.facebook.com/me?access_token={$access_token}");

				// if access token of Facebook is not null, then expire it
				// or else, Facebook will throw error regarding invalid token
				if (!is_null($check_access_token)) {
					$this->fbClient =  $this->getClient(!self::IS_GOOGLE);
					$this->fbClient->delete('/me/permissions', [], $access_token); // revoke token
				}
			}
			// log user out of Support.Bible
			if (qa_is_logged_in()) {
				qa_set_logged_in_user(null);
			}
			session_destroy();
			//redirect to home page
			qa_redirect('');
		}

		return true;
	}


	function login_html($tourl, $context)
	{
		$label = qa_lang('sso-auth/google_login');
		// Generate the authentication URL
		$googleUrl = $this->generateAuthUrl(self::IS_GOOGLE);
		// store user's current page so they can come back after logged in
		$_SESSION['return_url'] = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		// login html
		echo <<<HTML
					<div class="empty-div"> </div>
					<a class="google-signin" href="$googleUrl" name="login-google">
							<span class="google-signin-icon"></span>
						<span class="signin-text"> $label </span>
					</a>
					HTML;
		$fbUrl = $this->generateAuthUrl(!self::IS_GOOGLE);
		$label = qa_lang('sso-auth/facebook_login');
		echo <<<HTML
					<a class="facebook-signin" href="$fbUrl">
							<span class="facebook-signin-icon"></span>
						<span class="signin-text"> $label </span>
					</a>
					HTML;
	}

	function logout_html($tourl)
	{
		echo '<script type="text/JavaScript"> 
		var aTag = document.getElementsByClassName("fa fa-sign-out")[0].parentElement;
		aTag.href="https://staging.support.bible/qa-plugin/sso-authentication/logout.php";		
		aTag.innerHTML = "<span class=\"fa fa-sign-out\"></span> Log out ";
     </script>';
	}


	function admin_form(&$qa_content)
	{
		// process the admin form if admin hit Save-Changes-button
		$ok = null;
		if (qa_clicked('sso_authentication_enabled')) {
			qa_opt('google_authentication_enabled', (bool)qa_post_text('google_authentication_enabled')); // empty or 1
			qa_opt('google_authentication_client_id', qa_post_text('google_authentication_client_id'));
			qa_opt('google_authentication_client_secret', qa_post_text('google_authentication_client_secret'));
			qa_opt('facebook_authentication_enabled', (bool)qa_post_text('facebook_authentication_enabled')); // empty or 1
			qa_opt('facebook_authentication_client_id', qa_post_text('facebook_authentication_client_id'));
			qa_opt('facebook_authentication_client_secret', qa_post_text('facebook_authentication_client_secret'));
			qa_opt('paratext_authentication_enabled', (bool)qa_post_text('paratext_authentication_enabled')); // empty or 1
			$ok = 'Options saved';
		}

		return array(
			'ok' => ($ok && !isset($error)) ? $ok : null,
			'fields' => array(
				array(
					'type' => 'checkbox',
					'label' => qa_lang('sso-auth/google_login_enabled'),
					'tags' => 'name="google_authentication_enabled"',
					'value' => qa_opt('google_authentication_enabled'),
				),
				array(
					'label' => qa_lang('sso-auth/client_id'),
					'type' => 'text',
					'value' => qa_opt('google_authentication_client_id'),
					'tags' => 'name="google_authentication_client_id"',
				),
				array(
					'label' => qa_lang('sso-auth/client_secret'),
					'type' => 'text',
					'value' => qa_opt('google_authentication_client_secret'),
					'tags' => 'name="google_authentication_client_secret"',
				),
				array(
					'type' => 'checkbox',
					'label' => qa_lang('sso-auth/facebook_login_enabled'),
					'tags' => 'name="facebook_authentication_enabled"',
					'value' => qa_opt('facebook_authentication_enabled'),
				),
				array(
					'label' => qa_lang('sso-auth/client_id'),
					'type' => 'text',
					'value' => qa_opt('facebook_authentication_client_id'),
					'tags' => 'name="facebook_authentication_client_id"',
				),
				array(
					'label' => qa_lang('sso-auth/client_secret'),
					'type' => 'text',
					'value' => qa_opt('facebook_authentication_client_secret'),
					'tags' => 'name="facebook_authentication_client_secret"',
				),
				array(
					'type' => 'checkbox',
					'label' => qa_lang('sso-auth/paratext_login_enabled'),
					'tags' => 'name="paratext_authentication_enabled"',
					'value' => qa_opt('paratext_authentication_enabled'),
				),
			),
			'buttons' => array(
				array(
					'label' => 'Save options',
					'tags' => 'name="sso_authentication_enabled"',
				)
			),
		);
	}


	// helper function
	function getClient($isGoogle)
	{

		require_once QA_BASE_DIR . 'vendor/autoload.php';
		if ($isGoogle) {
			if ($this->googleClient == null) {
				$redirect_uri = qa_opt('site_url') . self::REDIRECT_URL;
				$this->googleClient = new Google_Client();
				$this->googleClient->setClientId(qa_opt('google_authentication_client_id'));
				$this->googleClient->setClientSecret(qa_opt('google_authentication_client_secret'));
				$this->googleClient->setRedirectUri($redirect_uri);
				$this->googleClient->addScope("email");
				$this->googleClient->addScope("profile");
			}
			return $this->googleClient;
		} else {
			if ($this->fbClient == null) {


				$this->fbClient = new Facebook\Facebook([
					'app_id' => qa_opt('facebook_authentication_client_id'),
					'app_secret' => qa_opt('facebook_authentication_client_secret'),
					'default_graph_version' => 'v2.10',
				]);
			}
			return $this->fbClient;
		}
	}

	// generate authorization url for user to login with SSO
	function generateAuthUrl($isGoogle)
	{
		$redirect_uri = qa_opt('site_url') . self::REDIRECT_URL;
		// make sure state in session will not change all the time

		if ($isGoogle) {
			if (!isset($_SESSION['state'])) {
				// add unique state to url to prevent cross origin attack
				$state = urlencode(time() . bin2hex(random_bytes(5)));
				$_SESSION['state'] = $state;
			}
			// generate authorization url for google
			$scope = urlencode('email profile');
			return 'https://accounts.google.com/o/oauth2/v2/auth?'
				. 'response_type=code'
				. '&client_id=' . urlencode(qa_opt('google_authentication_client_id'))
				. '&redirect_uri=' . urlencode($redirect_uri)
				. '&scope=' . $scope
				. '&state=' . $_SESSION['state']
				. '&prompt=consent';
		} else {
			$this->fbClient =  $this->getClient(!self::IS_GOOGLE);
			$fbHelper = $this->fbClient->getRedirectLoginHelper();
			$loginUrl = $fbHelper->getLoginUrl($redirect_uri, ['email', 'public_profile']);
			return $loginUrl;
		}
	}

	// after user grants login, use accesstoken to get users' info
	function logInWithSSO($isGoogle)
	{
		require_once QA_BASE_DIR . 'vendor/autoload.php';
		if ($isGoogle) {
			// retrieve google client
			$this->googleClient = $this->getClient(self::IS_GOOGLE);
			// authorize client with token after user grants the permission
			$token = $this->googleClient->fetchAccessTokenWithAuthCode($_SESSION['code']);
			if (!isset($token['access_token'])) {
				// to handle case when auth code is invalid, ask user to grant permission again
				header('Location: ' . filter_var($this->generateAuthUrl(self::IS_GOOGLE), FILTER_SANITIZE_URL));
			}
			$this->googleClient->setAccessToken($token['access_token']);
			// store user accesstoken for logout
			$this->storeToken($token, self::IS_GOOGLE);
			// Make the HTTP GET request to the API endpoint to get user info
			$url = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . urlencode($token['access_token']);
			$response = file_get_contents($url);
			$userInfo = json_decode($response, true);
			// register user in Support.Bible
			$this->registerUser($userInfo, 'google');
		} else {
			// get user accesstoken
			$auth = 'client_id=' . qa_opt("facebook_authentication_client_id") . '&redirect_uri=' . qa_opt('site_url') . self::REDIRECT_URL . '&client_secret=' . qa_opt("facebook_authentication_client_secret") . '&code=' . $_SESSION["code"];

			$token = $this->validateFBRequest('https://graph.facebook.com/v14.0/oauth/access_token?' . $auth);
			if (is_null($token)) {
				echo 'Error: Authentication failed! (Failed to get user access token.)';
				exit;
			}

			// store user accesstoken for logout
			$this->storeToken($token, !self::IS_GOOGLE);

			// get user data from Facebook using user accesstoken
			$data = $this->validateFBRequest('https://graph.facebook.com/v14.0/me?' . $auth . '&access_token=' . $access_token . '&fields=id,name,email');
			if (is_null($data)) {
				echo 'Error: Log in failed! (Failed to get user data.)';
				exit;
			}
			$this->registerUser($data, 'facebook');
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

	// store access token to cookie
	function storeToken($token, $isGoogle)
	{
		$access_token = $token['access_token'];
		$cipherToken = openssl_encrypt($access_token, 'aes-256-cbc', file_get_contents('token.key', true), OPENSSL_RAW_DATA, "");
		if ($isGoogle) {
			$_SESSION['google_access_token'] = $cipherToken;
			setcookie('google_access_token', $cipherToken, time() + (86400 * 30), true);
		} else {
			$_SESSION['fb_access_token'] = $cipherToken;
			setcookie('fb_access_token', $cipherToken, time() + (86400 * 30), true);
		}
	}

	// register user to Support.Bible site
	function registerUser($user_info, $provider)
	{
		$rootDir = $_SERVER['DOCUMENT_ROOT'];
		require_once $rootDir . '/qa-plugin/account-reclaim/qa-ar-functions.php';
		// Check if the user already has an account on the site
		require_once $rootDir . '/qa-include/db/users.php';
		$existingAccountIds = qa_db_user_find_by_email($user_info['email']);

		// No account exists; create a new one
		if (empty($existingAccountIds)) {
			// Check if the user is archived
			$matchingUsers = qa_ar_db_user_find_by_email($user_info['email']);

			// Make sure there is only one match
			if (count($matchingUsers) == 1) {
				// For qa_db_select_with_pending()
				require_once $rootDir . '/qa-include/db/selects.php';

				// For qa_complete_confirm()
				require_once $rootDir . '/qa-include/app/users-edit.php';

				// This is the userid of the archived user
				$userId = $matchingUsers[0];

				// Swap all the instances of the old username to the new one
				qa_ar_db_swap_name(qa_ar_db_get_anon($userId), $user_info['name']);

				// Set the fields of the account to the newly provided values
				// Note these updates must happen here because the credentials are needed to log in below
				qa_db_user_set($userId, array(
					'email' => $user_info['email'],       // Update the email address so the account is valid
					'handle' => $user_info['name'],   // Update the username to no longer be `anon######`
				));

				// The user is logging in with provider, so update their login source
				qa_db_query_sub('UPDATE ^userlogins SET source=$ WHERE userid=$', $provider, $userId);

				// This user has now confirmed their email
				qa_complete_confirm(strval($userId), $user_info['email'], $user_info['name']);

				// Report that a 'user reclaim' event has occurred (for event modules)
				qa_report_event(
					'u_reclaim',
					$userId,
					$user_info['name'],
					array(
						'email' => $user_info['email'],
					)
				);

				// Now log the user in
				qa_log_in_external_user($provider, $userId, array(
					'email' => @$user_info['email'],
					'handle' => @$user_info['name'],
					'name' => @$user_info['name'],
				));
			} else {
				// Otherwise, the user is completely new
				qa_log_in_external_user($provider, $user_info['id'], array(
					'email' => @$user_info['email'],
					'handle' => @$user_info['name'],
					'name' => @$user_info['name'],
				));
			}
		} else {
			// The user already has an account on the site; log them in with provider
			$users = array_values(qa_db_user_get_userid_handles($existingAccountIds));
			echo <<<HTML
					<h1>User</h1>
					HTML;
			qa_set_logged_in_user($existingAccountIds[0], $users[0], true, $provider);
		}
	}
}

/*
	Omit PHP closing tag to help avoid accidental output
*/
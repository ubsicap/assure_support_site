<?php
require_once 'facebook-config.php';

class sso_authentication_login
{
	protected $client = null;

	// called early on during every page request 
	// if a user is not currently logged in
	function check_login()
	{
		if(qa_clicked('login-google')) {
			echo '<script type="text/JavaScript"> 
			console.log("clickedclickedclickedclickedclickedclickedclickedclickedclickedclickedclicked");
		 </script>';
		}
		if (isset($_SESSION['code'])) {
			if (isset($_SESSION['scope']) && strpos($_SESSION['scope'], 'google'))
				// include package needed for using google client
				require_once QA_BASE_DIR . 'vendor/autoload.php';

			// retrieve google client
			$this->client = $this->getClient();

			// authorize client with token after user grants the permission
			$token = $this->client->fetchAccessTokenWithAuthCode($_SESSION['code']);
			$_SESSION['access_token'] = $token['access_token'];
			$this->client->setAccessToken($token['access_token']);

			// Make the HTTP GET request to the API endpoint to get user info
			$url = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . urlencode($token['access_token']);
			$response = file_get_contents($url);
			$userInfo = json_decode($response, true);

			// register user in Support.Bible
			$this->registerUser($userInfo, 'google');
		} else {
			//fb
		}
	}

	// called early on during every page request 
	// after a user is logged in
	function match_source($source)
	{
		if (isset($_SESSION['logout']) && strcmp($source, 'google') === 0) {
			// retrieve google client
			$this->client =  $this->getClient();
			// expire token so that user needs to grant permission next time for google
			$this->client->revokeToken();
			
			// log user out of Support.Bible
			if (qa_is_logged_in()) {
				echo "<script>console.log('logged in true');</script>";
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
		$googleUrl = $this->generateAuthUrl();
		// add unique state to url to avoid security attack
		$state = urlencode(time() . '-' . bin2hex(random_bytes(5)));
		$googleUrl .= '&state=' . $state;
		$_SESSION['state'] = $state;
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
		$fbUrl = get_fb_url();
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


	function loginWithFacebook()
	{

		try {
			$token = file_get_contents('https://graph.facebook.com/v14.0/oauth/access_token?client_id=' . qa_opt('facebook_authentication_client_id') . '&redirect_uri=' . qa_opt('site_url') . '&client_secret=' . qa_opt('facebook_authentication_client_secret') . '&code=' . $_GET['code']);
			$token = json_decode($token, true);
			$data = file_get_contents('https://graph.facebook.com/v14.0/me?client_id=' . qa_opt('facebook_authentication_client_id') . '&redirect_uri=' . qa_opt('site_url') . '&client_secret=' . qa_opt('facebook_authentication_client_secret') . '&code=' . $_GET['code'] . '&access_token=' . $token['access_token'] . '&fields=id,name,email');
			$data = json_decode($data, true);
			$this->registerUser($data, 'facebook');
		} catch (Exception $e) {
			echo $e->getMessage();
			exit();
		}
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
	function getClient()
	{
		if (is_null($this->client)) {
			require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
			$redirect_uri = 'https://staging.support.bible/qa-plugin/sso-authentication/login-callback.php';
			$this->client = new Google_Client();
			$this->client->setClientId(qa_opt('google_authentication_client_id'));
			$this->client->setClientSecret(qa_opt('google_authentication_client_secret'));
			$this->client->setRedirectUri($redirect_uri);
			$this->client->addScope("email");
			$this->client->addScope("profile");
			// offline access will give you both an access and refresh token so that
			// your app can refresh the access token without user interaction.
			$this->client->setAccessType('offline');
			// Using "consent" ensures that your application always receives a refresh token.
			// If you are not using offline access, you can omit this.
			$this->client->setApprovalPrompt('consent');
			$this->client->setIncludeGrantedScopes(true);   // incremental auth
		}

		return $this->client;
	}

	function generateAuthUrl()
	{
		$scope = 'email profile';
		return 'https://accounts.google.com/o/oauth2/v2/auth?'
			. 'response_type=code'
			. '&client_id=' . urlencode(qa_opt('google_authentication_client_id'))
			. '&redirect_uri=' . urlencode(qa_opt('site_url') . 'qa-plugin/sso-authentication/login-callback.php')
			. '&scope=' . urlencode($scope)
			. '&prompt=select_account';
	}

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
			qa_set_logged_in_user($existingAccountIds[0], $users[0], false, $provider);
		}
	}
}

/*
	Omit PHP closing tag to help avoid accidental output
*/
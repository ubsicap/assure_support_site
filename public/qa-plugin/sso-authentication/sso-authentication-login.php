<?php

// require_once dirname(QA_PLUGIN_DIR) . '/vendor/autoload.php';
// require_once dirname(QA_PLUGIN_DIR) . '/vendor/google/auth/src/OAuth2.php';
require_once QA_PLUGIN_DIR . 'account-reclaim/qa-ar-functions.php';


class sso_authentication_login
{
	var $directory;
	var $urltoroot;
	var $provider;

	function load_module($directory, $urltoroot, $type, $provider)
	{
		$this->directory = $directory;
		$this->urltoroot = $urltoroot;
		$opt = $provider . '_authentication_enabled';
		$this->provider = qa_opt($opt) ? $provider : '';
	}


	function check_login()
	{
		switch ($this->provider) {
			case "google":
				//when User clicked LOGOUT link
				if (isset($_GET['logout'])) {
					require_once QA_PLUGIN_DIR . 'sso-authentication/google-config.php';
					$client = get_google_client();
					$client->revokeToken();
				} else {

					$this->loginWithGoogle();
				}
				break;
			case "facebook":
				//when User clicked LOGOUT link
				if (isset($_GET['logout'])) {
					//added later
				} else {
					$this->loginWithFacebook();
				}
				break;
			default:
				break;
		}
	}


	function match_source($source)
	{
		return strcmp($this->provider, $source);
	}


	function login_html($tourl, $context)
	{
		switch ($this->provider) {
			case "google":
				require_once QA_PLUGIN_DIR . 'sso-authentication/google-config.php'; //for the $authUrl
				$googleUrl = get_google_url();
				$label = qa_lang('sso-auth/google_login');
				echo <<<HTML
					<a class="google-signin" href="$googleUrl">
							<span class="google-signin-icon"></span>
						<span class="signin-text"> $label </span>
					</a>
					HTML;
				break;

			case "facebook":
				require_once QA_PLUGIN_DIR . 'sso-authentication/facebook-config.php'; //for the $authUrl
				$fbUrl = get_fb_url();
				$label = qa_lang('sso-auth/facebook_login');
				echo <<<HTML
					<a class="facebook-signin" href="$fbUrl">
							<span class="facebook-signin-icon"></span>
						<span class="signin-text"> $label </span>
					</a>
					HTML;
				break;

			default:
				break;
		}
	}


	function logout_html($tourl)
	{
		echo '<script type="text/JavaScript"> 
		document.getElementsByClassName("fa fa-sign-out")[0].parentElement.href="' . $tourl . '";
		document.getElementsByClassName("fa fa-sign-out")[0].parentElement.innerHTML = "<span class=\"fa fa-sign-out\"></span> Log out ";
     </script>';
	}


	function loginWithGoogle()
	{
		if (isset($_GET['code'])) {
			// $qa_content = qa_content_prepare();
			try {
				$url = 'https://www.googleapis.com/oauth2/v4/token';
				// Get the access token 
				$data = $this->getAccessToken($url, qa_opt('google_authentication_client_id'), qa_opt('site_url'), qa_opt('google_authentication_client_secret'), $_GET['code']);

				// Access Token
				$access_token = $data['access_token'];

				// Get user information
				$user_info = $this->getUserProfileInfo($access_token);

				$this->registerUser($user_info, 'google');
				
			} catch (Exception $e) {
				echo $e->getMessage();
				exit();
			}
		}
	}

	function loginWithFacebook()
	{
		if (isset($_GET['code'])) {
			try {
				$url = "https://graph.facebook.com/v14.0/oauth/access_token?";
				$data = $this->getAccessToken($url, qa_opt('facebook_authentication_client_id'), qa_opt('site_url'), qa_opt('facebook_authentication_client_secret'), $_GET['code']);
				require_once QA_PLUGIN_DIR . 'sso-authentication/facebook-config.php'; //for get_fb_data();
				$fb = get_fb_data();
				// $helper = $fb->getRedirectLoginHelper();
				// $accessToken = $helper->getAccessToken();
				// Access Token
				$access_token = $data['access_token'];
				$response = $fb->get('/me?fields=id,name', $access_token);
				$user_info = $response->getGraphUser();
				$this->registerUser($user_info, 'facebook');
			} catch (Exception $e) {
				echo $e->getMessage();
				exit();
			}
		}
	}

	// $access_token is the access token you got earlier
	function getUserProfileInfo($access_token)
	{
		$url = 'https://www.googleapis.com/oauth2/v2/userinfo?fields=name,email,gender,id,picture,verified_email,link';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $access_token));
		$data = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($http_code != 200)
			throw new Exception('Error : Failed to get user information');

		return $data;
	}

	function getAccessToken($url, $client_id, $redirect_uri, $client_secret, $code)
	{
		$curlPost = 'client_id=' . $client_id . '&redirect_uri=' . $redirect_uri . '&client_secret=' . $client_secret . '&code=' . $code . '&grant_type=authorization_code';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
		$data = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($http_code != 200)
			throw new Exception('Error : Failed to receieve access token');

		return $data;
	}

	function registerUser($user_info, $provider) {
		// Check if the user already has an account on the site
		require_once QA_INCLUDE_DIR . 'db/users.php';
		$existingAccountIds = qa_db_user_find_by_email($user_info['email']);

		// No account exists; create a new one
		if (empty($existingAccountIds)) {
			// Check if the user is archived
			$matchingUsers = qa_ar_db_user_find_by_email($user_info['email']);

			// Make sure there is only one match
			if (count($matchingUsers) == 1) {
				// For qa_db_select_with_pending()
				require_once QA_INCLUDE_DIR . 'db/selects.php';

				// For qa_complete_confirm()
				require_once QA_INCLUDE_DIR . 'app/users-edit.php';

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
					'confirmed' => isset($user_info['verified_email']) ? : @$user_info['is_verified'],
					'name' => @$user_info['name'],
					'location' => @$user_info['location'],
					'website' => @$user_info['link'],
					'about' => isset($user_info['bio']) ? : '',
					'avatar' => strlen(@$user_info['picture']['data']['url']) ? qa_retrieve_url($user_info['picture']['data']['url']) : null,
				));
			} else {
				// Otherwise, the user is completely new
				qa_log_in_external_user($provider, $user_info['id'], array(
					'email' => @$user_info['email'],
					'handle' => @$user_info['name'],
					'confirmed' => @$user_info['verified_email'],
					'name' => @$user_info['name'],
					'location' => @$user_info['location'],
					'website' => @$user_info['link'],
					'about' => @$user_info['bio'],
					'avatar' =>  isset($user_info['picture']['data']['url']) ? qa_retrieve_url($user_info['picture']['data']['url']) : null,
				));
			}
		} else {
			// The user already has an account on the site; log them in with provider
			$users = array_values(qa_db_user_get_userid_handles($existingAccountIds));
			qa_set_logged_in_user($existingAccountIds[0], $users[0], false, $provider);
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
			// $ok = $this->send_sso_authentication_email();
			// if (!strpos($ok, 'successfully')) {
			// 	echo '<script type="text/JavaScript"> 
			// 		window.addEventListener("load", (event) => {
			// 			var grandparent = document.getElementsByClassName("qa-part-form-plugin-options")[0];
			// 			var target = grandparent.querySelector("form .qa-form-table-boxed .qa-form-tall-table tbody tr td");
			// 			target.classList.remove("qa-form-tall-ok");
			// 			target.className = "qa-form-tall-error";
			// 		});
			// 		</script>';
			// }
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
}

/*
	Omit PHP closing tag to help avoid accidental output
*/
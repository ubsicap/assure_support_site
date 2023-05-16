<?php


class sso_authentication_login
{
	// used to check if users log in with Google, true for Google, false for Facebook
	const IS_GOOGLE = true;
	const REDIRECT_URL = 'qa-plugin/sso-authentication/login-callback.php';
	private $googleClient = null; // to make sure client will not be created multiple times
	private $fbClient = null;

	public function __construct() {
		require_once 'helper-functions.php';
	}

	// called early on during every page request 
	// after a user is logged in
	function match_source($source)
	{
		if(isset($_SESSION['google_token'])) {
			storeToken($_SESSION['google_token'], self::IS_GOOGLE);
			
		}
		if(isset($_SESSION['fb_token'])) {
			storeToken($_SESSION['fb_token'], !self::IS_GOOGLE);
		}

		if (isset($_SESSION['logout'])) {
			if (strcmp($source, 'google') === 0) {

				$access_token = openssl_decrypt($_COOKIE['google_access_token'], 'aes-256-cbc', getenv('TOKEN_KEY'), OPENSSL_RAW_DATA, getenv('IV_KEY'));
				setcookie('google_access_token', '', time() - 3600);
				// retrieve google client
				$this->googleClient =  getClient(IS_GOOGLE);

				$this->googleClient->setAccessToken($access_token);
				// expire token so that user needs to grant permission next time for google
				$this->googleClient->revokeToken();
			} else if (strcmp($source, 'facebook') === 0 && isset($_COOKIE['fb_access_token'])) {
				// decipher access token got from cookie
				$access_token = openssl_decrypt($_COOKIE['fb_access_token'], 'aes-256-cbc', getenv('TOKEN_KEY'), OPENSSL_RAW_DATA, getenv('IV_KEY'));

				// remove cookie for access token
				setcookie('fb_access_token', '', time() - 3600);

				// check if access token is still valid in case user has already logged out 
				// or access token has expired
				$check_access_token = validateFBRequest("https://graph.facebook.com/me?access_token={$access_token}");

				// if access token of Facebook is not null, then expire it
				// or else, Facebook will throw error regarding invalid token
				if (!is_null($check_access_token)) {
					$this->fbClient =  getClient(!IS_GOOGLE);
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
		$googleUrl = generateAuthUrl(IS_GOOGLE);
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
		$fbUrl = generateAuthUrl(!IS_GOOGLE);
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
		aTag.href="https://support.bible/qa-plugin/sso-authentication/logout.php";		
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

}

// store access token to cookie, needs to ba called after redirect
// orelse, will not be able to write to cookie
function storeToken($token, $isGoogle)
{
    $access_token = $token['access_token'];
    $cipherToken = openssl_encrypt($access_token, 'aes-256-cbc', getenv('TOKEN_KEY'), OPENSSL_RAW_DATA, getenv('IV_KEY'));
    if ($isGoogle) {
        setcookie('google_access_token', $cipherToken, time() + (86400 * 30), true);				
		unset($_SESSION['google_token']);
    } else {
        setcookie('fb_access_token', $cipherToken, time() + (86400 * 30), true);
		unset($_SESSION['fb_token']);
    }
}
/*
	Omit PHP closing tag to help avoid accidental output
*/
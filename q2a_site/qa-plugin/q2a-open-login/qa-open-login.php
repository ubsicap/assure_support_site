<?php

/*
	Question2Answer (c) Gideon Greenspan
	Open Login Plugin (c) Alex Lixandru

	http://www.question2answer.org/


	File: qa-plugin/open-login/qa-open-login.php
	Version: 3.0.0
	Description: Login module class for handling OpenID/OAuth logins
	through HybridAuth library


	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	More about this license: http://www.question2answer.org/license.php
*/

class qa_open_login {

	var $directory;
	var $urltoroot;
	var $provider;

	function load_module($directory, $urltoroot, $type, $provider) {
		$this->directory = $directory;
		$this->urltoroot = $urltoroot;
		$this->provider = $provider;
	}


	function check_login() {
		require_once $this->directory . 'HybridAuth/autoload.php';

		try {

			// after login come back to the same page
			$loginCallback = qa_path('', array(), qa_opt('site_url'));

			require_once $this->directory . 'qa-open-utils.php';

			// prepare the configuration of HybridAuth
			$config = $this->getConfig($loginCallback);

			// try to login
			$hybridauth = new Hybridauth\Hybridauth($config);
			$storage = new Hybridauth\Storage\Session();
			$error = false;


			//
			// Event 1: User clicked SIGN-IN link
			//
			if (isset($_GET['login'])) {
				// Validate provider exists in the $config
				if ($_GET['login'] == strtolower($this->provider)) {
					// Store the provider for the callback event
					$storage->set('provider', $this->provider);
				} else {
					$error = $_GET['login'];
				}
			}

			//
			// Event 2: User clicked LOGOUT link
			//
			if (isset($_GET['logout'])) {
				if (in_array($_GET['logout'], $hybridauth->getProviders())) {
					// Disconnect the adapter
					$adapter = $hybridauth->getAdapter($_GET['logout']);
					$adapter->disconnect();
				} else {
					$error = $_GET['logout'];
				}
			}

			//
			// Event 3: Provider returns via CALLBACK
			//
			if ($provider = $storage->get('provider')) {

				if ($provider == $this->provider)
				{
					$hybridauth->authenticate($provider);
					$storage->set('provider', null);

					// Retrieve the provider record
					$adapter = $hybridauth->getAdapter($provider);
					$user = $adapter->getUserProfile();

					$duplicates = 0;
					if (!empty($user))
						$duplicates = qa_log_in_external_user($provider, $user->identifier, array(
							'email' => @$user->email,
							'handle' => @$user->displayName,
							'confirmed' => !empty($user->emailVerified),
							'name' => @$user->displayName,
							'location' => @$user->region,
							'website' => @$user->webSiteURL,
							'about' => @$user->description,
							'avatar' => strlen(@$user->photoURL) ? qa_retrieve_url($user->photoURL) : null,
						));

					// Now redirects:
					$topath = qa_get('to');
					if (!isset($topath)) {
						$topath = ''; // redirect to front page
					}

					if($duplicates > 0) {
						qa_redirect('logins', array('confirm' => '1', 'to' => $topath));
					} else {
						qa_redirect_raw(qa_opt('site_url') . $topath);
					}
				}
			}

		} catch (Exception $e) {
			error_log($e->getMessage());

			// not really interested in the error message - for now
			// however, in case we have errors 6 or 7, then we have to call logout to clean everything up
			if($e->getCode() == 6 || $e->getCode() == 7) {
				$adapter->logout();
			}

			$qry = 'provider=' . $this->provider . '&code=' . $e->getCode();
			if(strstr($topath, '?') === false) {
				$topath .= '?' . $qry;
			} else {
				$topath .= '&' . $qry;
			}

			// redirect
			qa_redirect_raw(qa_opt('site_url') . $topath);
		}

		return false;
	}


	function do_logout() {
		// after login come back to the same page
		$loginCallback = qa_path('', array(), qa_opt('site_url'));

		require_once $this->directory . 'HybridAuth/autoload.php';

		// prepare the configuration of HybridAuth
		$config = $this->getConfig($loginCallback);

		try {
			// try to logout
			$hybridauth = new Hybridauth\Hybridauth( $config );

			if($hybridauth->isConnectedWith( $this->provider )) {
				$adapter = $hybridauth->getAdapter( $this->provider );
				$adapter->disconnect();
			}

		} catch(Exception $e) {
			// not really interested in the error message - for now
			// however, in case we have errors 6 or 7, then we have to call logout to clean everything up
			if($e->getCode() == 6 || $e->getCode() == 7) {
				$adapter->logout();
			}
		}
	}


	function match_source($source) {
		// the session source will be in the format 'provider-xyx'
		$pos = strpos($source, '-');
		if($pos === false) {
			$pos = strlen($source);
		}

		// identify the provider out of the session source
		$provider = substr($source, 0, $pos);

		// verify if the identified provider matches the current one
		return stripos($this->provider, $provider) !== false;
	}


	function login_html($tourl, $context) {
		self::printCode($this->provider, $tourl, $context, 'login');
	}


	function logout_html($tourl) {
		self::printCode($this->provider, $tourl, 'menu', 'logout');
	}


	static function printCode($provider, $tourl, $context, $action = 'login', $print = true) {
		$css = $key = strtolower($provider);
		if ($key == 'live') {
			$css = 'windows'; // translate provider name to zocial css class
		} else if ($key == 'googleplus') {
			$provider = 'Google+';
		}
		$showInHeader = qa_opt("{$key}_app_shortcut") ? true : false;

		if($action == 'login' && !$showInHeader && $context == 'menu') {
			// do not show login button in the header for this
			return;
		}

		$zocial = qa_opt('open_login_zocial') == '1' ? 'zocial' : ''; // use zocial buttons
		if($action == 'logout') {
			$url = $tourl;
			$classes = "$context action-logout $zocial $css";
			$title = qa_lang_html('main/nav_logout');
			$text = qa_lang_html('main/nav_logout');

		} else if($action == 'login') {
			$topath = qa_get('to'); // lets user switch between login and register without losing destination page

			// clean GET parameters (not performed when to parameter is already passed)
			$get = $_GET;
			unset($get['provider']);
			unset($get['code']);

			$tourl = isset($topath) ? $topath : qa_path(qa_request(), $get, ''); // build our own tourl
			$params = array(
				'login' => $key,
			);

			$url = qa_path('login', $params, qa_path_to_root());
			if(strlen($tourl) > 0) {
				$url .= '&amp;to=' . htmlspecialchars($tourl); // play nice with validators
			}
			$classes = "$context action-login $zocial  $css";
			$title = qa_lang_html_sub('plugin_open/login_using', $provider);
			$text = ($key == 'google')?"Login using Google":$provider . ' ' . qa_lang_html('main/nav_login');

			if($context != 'menu') {
				$text = $title;
			}

		} else if($action == 'link') {
			$url = qa_path('logins', array('link' => $key), qa_path_to_root()); // build our own url
			$classes = "$context action-link $zocial $css";
			$title = qa_lang_html_sub('plugin_open/login_using', $provider);
			$text = qa_lang_html('main/nav_login');

			if($context != 'menu') {
				$text = $title;
			}

		} else if($action == 'view') {
			$url = 'javascript://';
			$classes = "$context action-link $zocial $css";
			$title = $provider;
			$text = $tourl;
		}

		if ($key == 'google' && in_array($action, array('login', 'view', 'link'))) {
			$html = <<<HTML
  <a class="google-signin" href="$url">
  	  <span class="google-signin-icon"></span>
      <span class="google-signin-text">$text</span>
  </a>
HTML;
		} else {
			$html = <<<HTML
  <a class="open-login-button context-$classes" title="$title" href="$url" rel="nofollow">$text</a>
HTML;
		}

		if($print) {
			echo $html;
		} else {
			return $html;
		}
	}


	function getConfig($url) {
		$key = strtolower($this->provider);

		require_once $this->directory . 'qa-open-utils.php';
		$scope = qa_open_login_get_provider_scope($this->provider);

		return array(
			'callback' => $url,
			'providers' => array (
				$this->provider => array (
					'enabled' => true,
					'keys' => array(
						'id' => qa_opt("{$key}_app_id"),
						'key' => qa_opt("{$key}_app_id"),
						'secret' => qa_opt("{$key}_app_secret")
					),
					'scope' => $scope,
				)
			),
			'debug_mode' => false,
			'debug_file' => ''
		);
	}

}

/*
	Omit PHP closing tag to help avoid accidental output
*/

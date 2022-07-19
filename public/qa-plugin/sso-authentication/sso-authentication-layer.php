<?php
require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';
require_once dirname(dirname(__FILE__)) . '/vendor/google/auth/src/OAuth2.php';
require_once QA_PLUGIN_DIR . 'account-reclaim/qa-ar-functions.php';

class qa_html_theme_layer extends qa_html_theme_base
{

    function head_script()
    {
        parent::head_script();
        $client = new Google_Client();

        if (qa_opt('google_authentication_enabled')) {
            $client->setClientId(qa_opt('google_authentication_client_id'));
            $client->setClientSecret(qa_opt('google_authentication_client_secret'));
            $client->setRedirectUri(qa_opt('site_url') . 'index.php');
            $client->addScope("email");
            $client->addScope("profile");

            if (isset($_GET['code'])) {
                try {
                    // Get the access token 
                    $data = $this->getAccessToken(qa_opt('google_authentication_client_id'), qa_opt('site_url') . 'index.php', qa_opt('google_authentication_client_secret'), $_GET['code']);

                    // Access Token
                    $access_token = $data['access_token'];

                    // Get user information
                    $user_info = $this->getUserProfileInfo($access_token);

                    // Check if the user is archived
                    $matchingUsers = qa_ar_db_user_find_by_email($user_info['email']);

                    // Make sure there is only one match
                    if (count($matchingUsers) == 1) {
                        // For qa_db_select_with_pending()
                        require_once QA_INCLUDE_DIR . 'db/selects.php';
                        // For qa_db_user_set_password(), qa_db_user_set()
                        require_once QA_INCLUDE_DIR . 'db/users.php';
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

                        // This user has now confirmed their email
                        qa_complete_confirm(strval($userId), $user_info['email'], $user_info['name']);

                        // Report that a 'user reclaim' event has occurred (for event modules)
                        qa_report_event(
                            'u_reclaim',
                            $userId,
                            $user_info['name'],
                            array(
                                'email' => $userInfo['email'],
                            )
                        );

                        // Now log the user in
                        qa_log_in_external_user('google', $userId, array(
                            'email' => @$user_info['email'],
                            'handle' => @$user_info['name'],
                            'confirmed' => @$user_info['verified_email'],
                            'name' => @$user_info['name'],
                            'location' => @$user_info['location']['name'],
                            'website' => @$user_info['link'],
                            'about' => @$user_info['bio'],
                            'avatar' => strlen(@$user_info['picture']['data']['url']) ? qa_retrieve_url($user_info['picture']['data']['url']) : null,
                        ));
                    } else {
                        // Otherwise, the user is completely new
                        qa_log_in_external_user('google', $user_info['id'], array(
                            'email' => @$user_info['email'],
                            'handle' => @$user_info['name'],
                            'confirmed' => @$user_info['verified_email'],
                            'name' => @$user_info['name'],
                            'location' => @$user_info['location']['name'],
                            'website' => @$user_info['link'],
                            'about' => @$user_info['bio'],
                            'avatar' => strlen(@$user_info['picture']['data']['url']) ? qa_retrieve_url($user_info['picture']['data']['url']) : null,
                        ));
                    }
                } catch (Exception $e) {
                    echo $e->getMessage();
                    exit();
                }
            } else {
                $authurl = $client->createAuthUrl();
                $this->output('<script type="text/javascript">
                window.onload=()=>{
                    document.getElementById("google-signin").href = "' . $authurl . '";
                };
            </script>');
                $this->output('
        <style type="text/css">
        
        #google-signin {
            background: #4285f4;
        }
        #facebook-signin {
            background: #3b5998;
        }
        #paratext-signin {
            background: #8aab68;
        }
        #google-signin:hover, #google-signin:focus {
            background: #3367d6;
        }
        #facebook-signin:hover, #facebook-signin:focus {  
            background: #293e6a;
        }
        #paratext-signin:hover, #paratext-signin:focus {
            background: #62794a;
        }
        #google-signin, #facebook-signin, #paratext-signin {
            width: 100%;
            height: 40px;
            border-radius: 3px;
            box-sizing: border-box;
            margin-bottom: 3px;
            display: inline-block;
            box-shadow: 1px 1px 0px 1px rgba(0,0,0,0.05);
            white-space: nowrap;
            transition: background 0.3s ease;
            padding: 0px !important;
            opacity: 1;
        }
        
        .google-signin-icon, .facebook-signin-icon, .paratext-signin-icon {
            box-sizing: content-box;
            position: absolute;
            width: 40px;
            height: 40px;
            border-radius: 3px 0px 0px 3px;
            background-size: 32px;
            background-position: 4px 4px;
            background-repeat: no-repeat;
        }
        .google-signin-icon {
            background-image: url("https://upload.wikimedia.org/wikipedia/commons/thumb/5/53/Google_%22G%22_Logo.svg/512px-Google_%22G%22_Logo.svg.png");
            background-color: #fff;
            border: 2px #4285f4;
        }
        .facebook-signin-icon {
            background-image: url("https://upload.wikimedia.org/wikipedia/en/thumb/0/04/Facebook_f_logo_%282021%29.svg/150px-Facebook_f_logo_%282021%29.svg.png");
            background-color: #fff;
            border: 2px #3b5998;
        }
        .paratext-signin-icon {
            background-image: url("https://registry.paratext.org/static/logo-pt9.png");
            background-color: #fff;
            border: 2px #8aab68;
        }
        
        .signin-text {
            display: inline-block;
            vertical-align: middle;
            padding: 0px;
            font-size: 14px;
            font-weight: bold;
            font-family: "Roboto", sans-serif;
            color: #fff;
            margin: 7px 8px 7px 13px;
        }

        </style>');
            }
        }
    }

    function body_header() // adds login bar, user navigation and search at top of page in place of custom header content
    {
        if (!empty($this->content['navigation']['main'])) {
            $this->output($this->donut_nav_bar($this->content['navigation']));
            unset($this->content['navigation']['main']);
        }
    }

    function donut_nav_bar($navigation)
    {
        ob_start();

        if (qa_opt('donut_enable_top_bar')) {
            donut_include_template('top-header.php');
        }

?>
        <header id="nav-header">
            <nav id="nav" class="navbar navbar-static-top" role="navigation" <?php echo (qa_opt('donut_enable_sticky_header') ? 'data-spy="affix" data-offset-top="120"' : '') ?>>
                <div class="container">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="glyphicon glyphicon-menu-hamburger"></span>
                        </button>
                    </div>
                    <div class="col-sm-3 col-xs-8 logo-wrapper">
                        <?php $this->logo(); ?>
                    </div>
                    <div class="donut-navigation col-sm-2 col-xs-3 pull-right">
                        <?php $this->donut_user_drop_down(); ?>
                    </div>
                    <div class="col-sm-7 navbar-collapse collapse main-nav navbar-left">
                        <ul class="nav navbar-nav inner-drop-nav">
                            <?php $this->donut_nav_bar_main_links($navigation['main']); ?>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
<?php
        return ob_get_clean();
    }


    function donut_user_drop_down()
    {
        if (qa_is_logged_in()) {
            require_once DONUT_THEME_BASE_DIR . '/templates/user-loggedin-drop-down.php';
        } else {
            if (qa_opt('sso_authentication_enabled'))
                require_once QA_PLUGIN_DIR . '/sso-authentication/customized-dropdown.php';
            else
                require_once DONUT_THEME_BASE_DIR . '/templates/user-login-drop-down.php';
        }
    }

    function donut_nav_bar_main_links($navigation)
    {
        if (count($navigation)) {
            foreach ($navigation as $key => $nav_item) {
                $this->donut_nav_bar_item($nav_item);
            }
        }
    }

    function donut_nav_bar_item($nav_item)
    {
        $class = (!!@$nav_item['class']) ? $nav_item['class'] . ' ' : '';
        $class .= (!!@$nav_item['selected']) ? 'active' : '';

        if (!empty($class)) {
            $class = 'class="' . $class . '"';
        }

        $icon = (!!@$nav_item['icon']) ? donut_get_fa_icon(@$nav_item['icon']) : '';

        $this->output('<li ' . $class . '><a href="' . $nav_item['url'] . '">' . $icon . $nav_item['label'] . '</a></li>');
    }

    function donut_get_fa_icon($icon)
    {
        if (!empty($icon)) {
            return '<span class="fa fa-' . $icon . '"></span> ';
        } else {
            return '';
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

    function getAccessToken($client_id, $redirect_uri, $client_secret, $code)
    {
        $url = 'https://www.googleapis.com/oauth2/v4/token';

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
}

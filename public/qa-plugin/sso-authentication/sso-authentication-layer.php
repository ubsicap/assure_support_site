<?php
require_once dirname(dirname(__FILE__)) .'/vendor/autoload.php';

class qa_html_theme_layer extends qa_html_theme_base
{

    function head_script()
    {   
        parent::head_script();
        $authurl = $this->get_google_url();
        if (qa_opt('google_authentication_enabled')) {
            $this->output('<script type="text/javascript">
                    window.onload=()=>{
					    document.getElementById("google-signin").href = "'.$authurl.'";
                    };
				</script>');
            $this->output('
            <style type="text/css">
            
            #google-signin {
                background: #DD4B39;
                opacity: .8;
            }
            #facebook-signin {
                background: #4C69BA;
                opacity: .8;
            }
            #paratext-signin {
                background: #8AAB68;
                opacity: .8;
            }
            .google-signin .facebook-signin .paratext-signin-icon {
                width: 100%;
                border: 1px solid #31708f;
                padding: 0;
                display: inline-block;
                border-radius: 1px;
                box-shadow: 1px 1px 0px 1px rgba(0,0,0,0.05);
                white-space: nowrap;
            }

            .google-signin-icon {
                background-image: url("https://upload.wikimedia.org/wikipedia/commons/thumb/5/53/Google_%22G%22_Logo.svg/512px-Google_%22G%22_Logo.svg.png");
                background-size: 18px;
                background-repeat: no-repeat;
                background-position: 11px;
                width: 40px;
                height: 40px;
                background-color: #fff;
                display: inline-block;
                vertical-align: middle;
            }
            .facebook-signin-icon {
                background-image: url("https://upload.wikimedia.org/wikipedia/en/thumb/0/04/Facebook_f_logo_%282021%29.svg/150px-Facebook_f_logo_%282021%29.svg.png");
                background-size: 18px;
                background-repeat: no-repeat;
                background-position: 11px;
                width: 40px;
                height: 40px;
                background-color: #fff;
                display: inline-block;
                vertical-align: middle;
            }
            .paratext-signin-icon {
                background-image: url("https://registry.paratext.org/static/logo-pt9.png");
                background-size: 18px;
                background-repeat: no-repeat;
                background-position: 11px;
                width: 40px;
                height: 40px;
                background-color: #fff;
                display: inline-block;
                vertical-align: middle;
            }

            .signin-text {
                display: inline-block;
                vertical-align: middle;
                padding: 0px;
                font-size: 14px;
                font-weight: bold;
                font-family: "Roboto", sans-serif;
                color: #fff;
                margin-left: 13px;
                margin-right: 8px;
            }

            #facebook-signin:hover,
            #facebook-signin:focus {
              background-color: #4C69BA !important;
              background-image: linear-gradient(#4C69BA, #13328a) !important;
            }
            #google-signin:hover,
            #google-signin:focus {
              background: #DD4B39 !important;
              background-image: linear-gradient(#DD4B39, #9b2e20) !important;
            }
            #paratext-signin:hover,
            #paratext-signin:focus {
              background: #8AAB68 !important;
              background-image: linear-gradient(#8AAB68, #607749) !important;
            }

            </style>');
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
                require_once QA_PLUGIN_DIR .'/sso-authentication/customized-dropdown.php';
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

    function get_google_url()
    {
        // init configuration

        $clientID = qa_opt('google_authentication_client_id');
        $clientSecret = qa_opt('google_authentication_client_secret');
        // create Client Request to access Google API
        $client = new Google_Client();
        $client->setClientId($clientID);
        $client->setClientSecret($clientSecret);
        $client->setRedirectUri("https://supportsitetest.tk/index.php");
        $client->addScope("email");
        $client->addScope("profile");
        return $client->createAuthUrl();
    }
}

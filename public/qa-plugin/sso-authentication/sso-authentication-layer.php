<?php
//require_once QA_PLUGIN_DIR .'/sso-authentication/sso-authentication-layer.php';
// require_once QA_BASE_DIR .'vendor/autoload.php';

class qa_html_theme_layer extends qa_html_theme_base
{

    function head_script()
    {   
        parent::head_script();
        // $authurl = $this->get_url();
        // if (qa_opt('sso_authentication_enabled')) {
        //     $this->output('<script type="text/javascript">
        //             window.onload=()=>{
		// 			    document.getElementById("google-signin").href = "'.$authurl.'";
        //             };
		// 		</script>');
        // }   
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

    function get_url()
    {
        // init configuration

           
        // create Client Request to access Google API
        $client = new Google_Client();
        $client->setClientId($clientID);
        $client->setClientSecret($clientSecret);
        $client->setRedirectUri($redirectUri);
        $client->addScope("email");
        $client->addScope("profile");
        return $client->createAuthUrl();
    }
}

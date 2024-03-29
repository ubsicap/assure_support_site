<?php
class qa_html_theme_layer extends qa_html_theme_base
{
    function head_css()
    {
        qa_html_theme_base::head_css();

        //if we're on the questions, unanswered, or users page hide the page-title
        //we do this since the title is self evidenct since there are tabs on these pages
        if($this->template == "questions" || $this->template == "unanswered" || $this->template == "users")
        {
            $this->output('
                <style>
                    .page-title {
                        display: none;
                    }
                </style>'
            );
        }
        if($this->template == "qa" || $this->template == "questions" || $this->template == "unanswered" || $this->template == "users")
        {
            $this->output('
                <style>
                    .qa-q-item-tag-list {
                        font-size: 12px;
                    }
                </style>'
            );
        }
        //css for the selected page on the tob bar,
        $this->output('
            <style>
                .navbar-nav > li.active > a {
                    opacity: 0.9;
                    color: #310b0b;
                }       
            </style>'
        );
        //the search bar should be enabled, remove the side search bar
        $this->output('
            <style>
                .side-search-bar, .top-search-bar {
                    display: none;
                }   
            </style>'
        );

        //remove login top right button when on log in screen
        if($this->template == "login")
        {
            $this->output('
                <style>
                    .navbar-nav > li > a.navbar-login-button {
                        display: none;
                    }
                </style>'
            );
        } else {
            //not on login screen, add "Log in" text to the button
            $this->output('
                <style>
                    .fa-sign-in:after {
                        content: "  ' . qa_lang_html('users/login_title') . '";
                        font-family: sans-serif;
                        font-size: 15px;
                    }
                    .fa-sign-in:before {
                        font-size: 15px;
                    }
                    a.navbar-login-button {
                        padding: 4px 10px 8px 10px !important /* not sure how to avoid using important here */
                    }
                    .nav.navbar-nav.navbar-right.login-nav {
                        padding: 20px 0;
                    }
                </style>'
            );
        }
    }
}

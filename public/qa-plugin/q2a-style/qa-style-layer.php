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
        //css for the selected page on the tob bar
        $this->output('
            <style>
                .navbar-nav > li.active > a {
                    opacity: 1;
                    color: #337ab7;
                    content: ' . $this->template . '
                }
            </style>'
        );

        //remove login top right button when on log in screen
        if($this->template == "login")
        {
            $this->output('
                <style>
                    .login-dropdown {
                        display: none;
                    }
                </style>'
            );
        } else {
            //not on login screen, add "Log in" text to the button
        }
    }
}

<?php

class qa_html_theme_layer extends qa_html_theme_base
{

    function head_css()
    {
        qa_html_theme_base::head_css();
        $this->output('
            <style type="text/css">
            
            .google-signin.fa, .google-signin {
                background: #4285f4;
            }
            .facebook-signin.fa, .facebook-signin {
                background: #3b5998;
            }
            .paratext-signin.fa, .paratext-signin {
                background: #8aab68;
            }
            .google-signin.fa, .facebook-signin.fa, .paratext-signin.fa {
                width: 100%;
                border-radius: 3px;
                box-sizing: border-box;
                overflow: hidden;
                margin-bottom: 3px;
                display: inline-block;
                box-shadow: 1px 1px 0px 1px rgba(0,0,0,0.05);
                white-space: nowrap;
                transition: background 0.3s ease;
                opacity: 1;
            }

            .google-signin, .facebook-signin, .paratext-signin {
                left: 30%
                width: 30%;
                height: 40px;
                border-radius: 3px;
                box-sizing: border-box;
                overflow: hidden;
                margin-bottom: 3px;
                display: inline-block;
                box-shadow: 1px 1px 0px 1px rgba(0,0,0,0.05);
                white-space: nowrap;
                transition: background 0.3s ease;
                opacity: 1;
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
            
            .google-signin.fa:hover, .google-signin.fa:focus, .google-signin:hover, .google-signin:focus {
                background: #3367d6;
            }
            .facebook-signin.fa:hover, .facebook-signin.fa:focus, .facebook-signin:hover, .facebook-signin:focus {  
                background: #293e6a;
            }
            .paratext-signin.fa:hover, .paratext-signin.fa:focus, .paratext-signin:hover, .paratext-signin:focus {
                background: #62794a;
            }
            </style>');
    }
}

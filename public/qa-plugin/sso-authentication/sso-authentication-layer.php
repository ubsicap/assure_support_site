<?php

class qa_html_theme_layer extends qa_html_theme_base
{

    function head_css()
    {
        qa_html_theme_base::head_css();
        $this->output('
            <style type="text/css">
            
            .google-signin {
                background: #4285f4;
            }
            .facebook-signin {
                background: #3b5998;
            }
            .paratext-signin {
                background: #8aab68;
            }
            li > .google-signin, li > .facebook-signin, li > .paratext-signin {
                width: 100%;
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
                padding: 0px !important;
            }

            div.qa-part-custom > .google-signin, div.qa-part-custom > .facebook-signin, div.qa-part-custom > .paratext-signin {
                margin-left: 35%;
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
            
            .google-signin-icon, .facebook-signin-icon, .paratext-signin-icon {
                box-sizing: content-box;
                position: absolute;
                width: 36px;
                height: 36px;
                border-radius: 3px 0px 0px 3px;
                margin: 2px 2px 2px 2px;
                background-size: 28px;
                background-position: 4px 4px;
                background-repeat: no-repeat;
            }
            .google-signin-icon {
                background-image: url("/assets/google_icon.png");
                background-color: #ffffff;
            }
            .facebook-signin-icon {
                background-image: url("https://upload.wikimedia.org/wikipedia/en/thumb/0/04/Facebook_f_logo_%282021%29.svg/150px-Facebook_f_logo_%282021%29.svg.png");
                background-color: #293e6a;
            }
            .paratext-signin-icon {
                background-image: url("https://registry.paratext.org/static/logo-pt9.png");
                background-color: #607749;
            }
            
            .signin-text {
                display: inline-block;
                vertical-align: middle;
                padding: 0px;
                font-size: 14px;
                font-weight: bold;
                font-family: "Roboto", sans-serif;
                color: #fff;
                margin: 11px 8px 11px 53px;
            }
            
            .google-signin:hover, .google-signin:focus {
                background: #3367d6;
            }
            .facebook-signin:hover, .facebook-signin:focus {  
                background: #293e6a;
            }
            .paratext-signin:hover, .paratext-signin:focus {
                background: #62794a;
            }
            </style>');
    }
}

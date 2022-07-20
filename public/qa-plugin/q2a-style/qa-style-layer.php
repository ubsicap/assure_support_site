<?php
class qa_html_theme_layer extends qa_html_theme_base
{
    function head_css()
    {
        qa_html_theme_base::head_css();

        //if we're on the questions page hide the page-title
        if($this->template == "questions")
        {
            $this->output('
                <style>
                    .page-title {
                        display: none;
                    }
                </style>'
            );
        }
    }
}

<?php

class qa_html_theme_layer extends qa_html_theme_base

{

    public function form_field($field, $style)
    {

        if ($this->template == 'register') {

            print_r($field);
            print_r($style);
        }

        qa_html_theme_base::form_field($field, $style);
    }
}

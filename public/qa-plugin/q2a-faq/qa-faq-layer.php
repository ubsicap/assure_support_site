<?php

class qa_html_theme_layer extends qa_html_theme_base
{

    // theme replacement functions
    function head_css()
    {
        qa_html_theme_base::head_css();
        $this->output('<style>', qa_opt('faq_css'), '</style>');
    }

    function body_prefix()
    {
        if (!qa_get_logged_in_userid() && !@$_COOKIE['qa_faq_noshow'] && qa_opt('faq_notify_show')) {
            setcookie('qa_faq_noshow', 'true', 0, '/', QA_COOKIE_DOMAIN);

            // notify new user of faq
            $notice = '<div class="notify-faq-container">';
            $text = str_replace('^faq', '<a href="' . qa_path_html(qa_opt('faq_page_url')) . '">' . qa_opt('faq_page_slug') . '</a>', qa_opt('faq_notify_text'));
            $notice .= '<div class="notify-faq">' . $text . '<div class="notify-faq-close" onclick="jQuery(this).parent().slideUp(\'fast\')">x</div></div>';
            $notice .= '</div>';
            $this->output($notice);
        }
        qa_html_theme_base::body_prefix();
    }
}

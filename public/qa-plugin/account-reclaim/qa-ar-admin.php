<?php

/*
    File: qa-plugin/account-reclaim/qa-ar-admin.php
    Description: Admin page for the Account Reclaim plugin
*/



class qa_ar_admin
{
    /**
     * Returns the default value for the provided option.
     * 
     * @param string $option The option being fetched.
     * @return string The default value for the option.
     */
    function option_default($option)
    {
        switch ($option) {
            case 'qa_ar_redirect_page':
                return 'account';
            case 'qa_ar_captcha_on_recover':
                return 1; // true
            default:
                return null;
        }
    }



    /**
     * Construct the HTML of the admin form for this plugin.
     * 
     * @param mixed $qa_content HTML content of the page.
     * @return mixed HTML content for the admin form.
     */
    function admin_form(&$qa_content)
    {

        //	Process form input

        $ok = null;
        if (qa_clicked('qa_ar_save_button')) {

            // Create an option for the redirect page after account reclaim
            qa_opt('qa_ar_redirect_page', qa_post_text('qa_ar_redirect_page'));

            // Toggle whether to use CAPTCHA on account recovery
            qa_opt('qa_ar_captcha_on_recover', (bool)qa_post_text('qa_ar_captcha_on_recover'));

            $ok = qa_lang('admin/options_saved');
        } else if (qa_clicked('qa_ar_reset_button')) {
            // If the user has clicked the reset button, reset all options
            foreach ($_POST as $i => $v) {
                $def = $this->option_default($i);
                if ($def !== null) qa_opt($i, $def);
            }
            $ok = qa_lang('admin/options_reset');
        }


        //	Create the form for display
        $fields = array();

        // Create a text area for setting the redirect page
        $fields[] = array(
            'label' => qa_lang('qa-ar/admin_redirect_page'),
            'tags' => 'NAME="qa_ar_redirect_page"',
            'value' => qa_opt('qa_ar_redirect_page'),
            'type' => 'textarea',
            'rows' => '1'
        );
        $fields[] = array(
            'label' => qa_lang('qa-ar/admin_captcha_on_recover'),
            'tags' => 'NAME="qa_ar_captcha_on_recover"',
            'value' => qa_opt('qa_ar_captcha_on_recover'),
            'type' => 'checkbox',
        );

        return array(
            'ok' => ($ok && !isset($error)) ? $ok : null,

            'fields' => $fields,

            'buttons' => array(
                array(
                    'label' => qa_lang_html('main/save_button'),
                    'tags' => 'NAME="qa_ar_save_button"',
                ),
                array(
                    'label' => qa_lang_html('admin/reset_options_button'),
                    'tags' => 'NAME="qa_ar_reset_button"',
                ),
            ),
        );
    }
}

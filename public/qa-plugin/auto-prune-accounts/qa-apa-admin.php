<?php

/*
    File: qa-plugin/auto-prune-accounts/qa-apa-event.php
    Description: Admin form for plugin
*/



require_once QA_PLUGIN_DIR . 'auto-prune-accounts/qa-apa-functions.php';



class qa_apa_admin
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
            case 'qa_apa_enable_autoprune':
                return true;
            case 'qa_apa_timeout_value':
                return '30';
                /*
            case 'qa_apa_timeout_units':
                return 'minutes';
            */
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
        if (qa_clicked('qa_apa_save_button')) {

            // Enable/disable plugin
            qa_opt('qa_apa_enable_autoprune', qa_post_text('qa_apa_enable_autoprune'));

            // Time between auto removal checks
            qa_opt('qa_apa_timeout_value', qa_post_text('qa_apa_timeout_value'));

            // Time unit for the timeout value
            //qa_opt('qa_apa_timeout_units', qa_post_text('qa_apa_timeout_units'));


            if (qa_opt('qa_apa_enable_autoprune')) {
                start_autoprune();
            } else {
                stop_autoprune();
            }

            $ok = qa_lang('admin/options_saved');
        } else if (qa_clicked('qa_apa_reset_button')) {
            // If the user has clicked the reset button, reset all options
            foreach ($_POST as $i => $v) {
                $def = $this->option_default($i);
                if ($def !== null) qa_opt($i, $def);
            }
            $ok = qa_lang('admin/options_reset');
        }


        //	Create the form for display
        $fields = array();

        // Enable or disable the autoprune system
        $fields[] = array(
            'label' => qa_lang('qa-apa/admin_enable_autoprune'),
            'tags' => 'NAME="qa_apa_enable_autoprune"',
            'value' => qa_opt('qa_apa_enable_autoprune'),
            'type' => 'checkbox',
        );
        // Create a text area for setting the timeout value
        $fields[] = array(
            'label' => qa_lang('qa-apa/admin_timeout_value'),
            'tags' => 'NAME="qa_apa_timeout_value"',
            'value' => qa_opt('qa_apa_timeout_value'),
            'type' => 'number',
        );
        /*
        //  Set the timeout units (minutes, hours, etc.)
        $fields[] = array(
            'label' => qa_lang('qa-apa/admin_timeout_units'),
            'tags' => 'NAME="qa_apa_timeout_units"',
            'value' => qa_opt('qa_apa_timeout_units'),
            'type' => 'textarea',
            'rows' => '1'
        );
        */


        return array(
            'ok' => ($ok && !isset($error)) ? $ok : null,

            'fields' => $fields,

            'buttons' => array(
                array(
                    'label' => qa_lang_html('main/save_button'),
                    'tags' => 'NAME="qa_apa_save_button"',
                ),
                array(
                    'label' => qa_lang_html('admin/reset_options_button'),
                    'tags' => 'NAME="qa_apa_reset_button"',
                ),
            ),
        );
    }
}

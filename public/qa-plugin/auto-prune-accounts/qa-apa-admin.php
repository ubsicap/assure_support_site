<?php

/*
    File: qa-plugin/auto-prune-accounts/qa-apa-event.php
    Description: Admin form for plugin
*/



require_once QA_PLUGIN_DIR . 'auto-prune-accounts/qa-apa-events.php';



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
            case 'qa_apa_timeout_minutes':
                return '30';
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
            qa_opt('qa_apa_timeout_minutes', qa_post_text('qa_apa_timeout_minutes'));

            if (qa_opt('qa_apa_enable_autoprune')) {
                qa_apa_events::start_autoprune();
            } else {
                qa_apa_events::stop_autoprune();
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
            'label' => qa_lang('qa-apa/admin_timeout_minutes'),
            'tags' => 'NAME="qa_apa_timeout_minutes"',
            'value' => qa_opt('qa_apa_timeout_minutes'),
            'type' => 'number',
        );


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

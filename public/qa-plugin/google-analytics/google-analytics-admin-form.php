<?php

class google_analytics_admin_form {

    function admin_form() {
        $saved=false;

        if (qa_clicked('google_analytics_save_button')) {
            qa_opt('google_analytics_GA4', qa_post_text('google_analytics_GA4_field'));
            qa_opt('google_analytics_show_for_admin', (bool)qa_post_text('google_analytics_show_for_admin_field'));
            $saved=true;
        }

        return array(
            'ok' => $saved ? 'Google Analytics settings saved.' : null,

            'fields' => array(
                array(
                    'label' => 'Enter Google Global Site Tag:',
                    'value' => qa_opt('google_analytics_GA4'),
                    'tags' => 'NAME="google_analytics_GA4_field"',
                    'type' => 'textarea',
                    'rows' => 10
                ),

                array(
                    'label' => 'Check to exclude Super Admin visits from tracking.',
                    'type' => 'checkbox',
                    'value' => qa_opt('google_analytics_show_for_admin'),
                    'tags' => 'NAME="google_analytics_show_for_admin_field"',
                ),
            ),

            'buttons' => array(
                array(
                    'label' => 'Save Changes',
                    'tags' => 'NAME="google_analytics_save_button"',
                ),
            ),

        );


    }
}
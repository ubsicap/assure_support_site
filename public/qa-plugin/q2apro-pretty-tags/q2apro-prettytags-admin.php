<?php

/*
	Plugin Name: Pretty Tags
	Plugin URI: http://www.q2apro.com/plugins/pretty-tags
	Plugin Description: Provides a pretty autocomplete for tags on the ask page 
	Plugin Version: 1.0
	Plugin Date: 2014-10-05
	Plugin Author: q2apro.com
	//Plugin Author URI: http://www.q2apro.com
	Plugin Minimum Question2Answer Version: 1.5
	//Plugin Update Check URI: http://www.q2apro.com/pluginupdate?id=59
	
	Licence: Copyright © q2apro.com - All rights reserved

*/

class q2apro_prettytags_admin
{

    // option's value is requested but the option has not yet been set
    function option_default($option)
    {
        switch ($option) {
            case 'q2apro_prettytags_enabled':
                return 1; // true
            default:
        }
    }

    function allow_template($template)
    {
        return ($template != 'admin');
    }

    function admin_form(&$qa_content)
    {

        // process the admin form if admin hit Save-Changes-button
        $ok = null;
        if (qa_clicked('q2apro_prettytags_save')) {
            qa_opt('q2apro_prettytags_enabled', (bool)qa_post_text('q2apro_prettytags_enabled')); // empty or 1
            $ok = qa_lang('admin/options_saved');
        }

        qa_set_display_rules($qa_content, array(
            'tag_max_len_display' => 'tag_on_field',
        ));

        return array(
            'ok' => ($ok && !isset($error)) ? $ok : null,
            'fields' => array(
                array(
                    'type' => 'checkbox',
                    'label' => qa_lang('q2apro_prettytags_lang/enable_plugin'),
                    'tags' => 'name="q2apro_prettytags_enabled"',
                    'value' => qa_opt('q2apro_prettytags_enabled'),
                ),
            ),
            'buttons' => array(
                array(
                    'label' => qa_lang('main/save_button'),
                    'tags' => 'name="q2apro_prettytags_save"',
                ),
            ),
        );
    }
}


/*
	Omit PHP closing tag to help avoid accidental output
*/
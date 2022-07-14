<?php
class qa_ar_admin
{

    function allow_template($template)
    {
        return ($template != 'admin');
    }

    function option_default($option)
    {

        switch ($option) {
            case 'qa_ar_redirect_page':
                return '';
            default:
                return null;
        }
    }

    function admin_form(&$qa_content)
    {

        //	Process form input

        $ok = null;
        if (qa_clicked('qa_ar_save_button')) {
            qa_opt('qa_ar_redirect_page', qa_post_text('qa_ar_redirect_page'));


            /*
            qa_opt('marker_plugin_a_qv', (bool)qa_post_text('marker_plugin_a_qv'));
            qa_opt('marker_plugin_a_qi', (bool)qa_post_text('marker_plugin_a_qi'));
            qa_opt('marker_plugin_a_a', (bool)qa_post_text('marker_plugin_a_a'));
            qa_opt('marker_plugin_a_c', (bool)qa_post_text('marker_plugin_a_c'));

            qa_opt('marker_plugin_w_users', (bool)qa_post_text('marker_plugin_w_users'));
            qa_opt('marker_plugin_w_qv', (bool)qa_post_text('marker_plugin_w_qv'));
            qa_opt('marker_plugin_w_qi', (bool)qa_post_text('marker_plugin_w_qi'));
            qa_opt('marker_plugin_w_a', (bool)qa_post_text('marker_plugin_w_a'));
            qa_opt('marker_plugin_w_c', (bool)qa_post_text('marker_plugin_w_c'));
*/
            $ok = qa_lang('admin/options_saved');
        } else if (qa_clicked('qa_ar_reset_button')) {
            foreach ($_POST as $i => $v) {
                $def = $this->option_default($i);
                if ($def !== null) qa_opt($i, $def);
            }
            $ok = qa_lang('admin/options_reset');
        }
        //	Create the form for display


        $fields = array();

        $fields[] = array(
            'label' => 'Location to redirect users after account reclaim',
            'tags' => 'NAME="qa_ar_redirect_page"',
            'value' => qa_opt('qa_ar_redirect_page'),
            'type' => 'textarea'
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

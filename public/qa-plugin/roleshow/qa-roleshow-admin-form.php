<?php


class qa_roleshow_admin_form
{
    public function option_default($option)
    {
        if ($option === 'roleshow_content_max_len')
            return 480;
    }


    public function admin_form(&$qa_content)
    {
        $saved = qa_clicked('roleshow_save_button');

        if ($saved) {
            qa_opt('roleshow_content_on', (int) qa_post_text('roleshow_content_on_field'));
            qa_opt('roleshow_content_max_len', (int) qa_post_text('roleshow_content_max_len_field'));
        }

        qa_set_display_rules($qa_content, array(
            'roleshow_content_max_len_display' => 'roleshow_content_on_field',
        ));

        return array(
            'ok' => $saved ? 'Mouseover settings saved' : null,

            'fields' => array(
                array(
                    'label' => 'Show content preview on roleshow in question lists',
                    'type' => 'checkbox',
                    'value' => qa_opt('roleshow_content_on'),
                    'tags' => 'name="roleshow_content_on_field" id="roleshow_content_on_field"',
                ),

                array(
                    'id' => 'roleshow_content_max_len_display',
                    'label' => 'Maximum length of preview:',
                    'suffix' => 'characters',
                    'type' => 'number',
                    'value' => (int) qa_opt('roleshow_content_max_len'),
                    'tags' => 'name="roleshow_content_max_len_field"',
                ),
            ),

            'buttons' => array(
                array(
                    'label' => 'Save Changes',
                    'tags' => 'name="roleshow_save_button"',
                ),
            ),
        );
    }
}

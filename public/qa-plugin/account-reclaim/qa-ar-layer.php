<?php

class qa_html_theme_layer extends qa_html_theme_base

{
    public function form_fields($form, $columns)
    {
        // Provide a confirmation layer on the registration page underneath the email form
        //if ($this->template == 'register' && $form['fields']['email']['label'] == qa_lang_html('users/email_label')) {
        if ($this->template == 'register') {
            // Fetch the email address and confirmation text
            // If the confirmation box isn't displayed, it will be an empty string
            $inemail = qa_post_text('email');
            $inconfirm = qa_post_text('custom_confirm');

            // Check if email belongs to an archived account
            if (qa_ar_db_is_archived_email($inemail)) {
                // If the confirmation box doesn't match, display the email error
                if ($inconfirm != qa_lang('qa-ar/do_not_reclaim')) {
                    $form['fields']['email']['error'] = qa_lang_sub('qa-ar/archived_warning', $inemail);
                }

                // Create confirmation box
                $form['fields']['custom_confirm'] = array(
                    'label' => qa_lang_sub('qa-ar/custom_confirm_box', qa_lang('qa-ar/do_not_reclaim')),
                    'tags' => 'name="custom_confirm" id="custom_confirm" dir="auto"',
                    // 'value' => qa_html(@$inconfirm),
                    'value' => null,
                    'error' => null,
                );

                // Reorder the fields so the confirm box is below the email box 
                qa_html_theme_base::form_reorder_fields($form, array('handle', 'password', 'email', 'custom_confirm'));
            }
        }
        qa_html_theme_base::form_fields($form, $columns);
    }
}

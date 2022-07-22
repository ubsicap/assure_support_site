<?php



// For qa_ar_db_is_archived_email()
require_once QA_PLUGIN_DIR . 'account-reclaim/qa-ar-functions.php';
// For qa_handle_email_filter()
require_once QA_INCLUDE_DIR . 'app/users-edit.php';
// For qa_captcha_validate_post()
require_once QA_INCLUDE_DIR . 'app/captcha.php';


class qa_html_theme_layer extends qa_html_theme_base
{
    public function form_fields($form, $columns)
    {
        // Provide a confirmation layer on the registration page underneath the email form
        if ($this->template == 'register') {
            // Fetch the text box values
            // If the confirmation box isn't displayed, it will be an empty string
            $inemail = qa_post_text('email');
            $inhandle = qa_post_text('handle');
            $inconfirm = qa_post_text('custom_confirm');

            // Initially check that no other errors are present
            $registrationErrors = qa_handle_email_filter($inhandle, $inemail);
            if (qa_opt('captcha_on_register')) {
                qa_captcha_validate_post($registrationErrors);
            }

            // If no errors are yet present, check if the email belongs to an archived account
            if (empty($registrationErrors) && qa_ar_db_is_archived_email($inemail)) {
                // If the confirmation box doesn't match, display the email error
                if ($inconfirm != qa_lang('qa-ar/do_not_reclaim')) {
                    $form['fields']['email']['error'] = qa_lang_sub('qa-ar/archived_warning', $inemail);
                }

                // Create confirmation box
                $form['fields']['custom_confirm'] = array(
                    'label' => qa_lang_sub('qa-ar/custom_confirm_box', qa_lang('qa-ar/do_not_reclaim')),
                    'tags' => 'name="custom_confirm" id="custom_confirm" dir="auto"',
                    // 'value' => qa_html(@$inconfirm), // Textbox contents are whatever the user entered
                    'value' => null, // Textbox contents are reset every time the form is loaded
                    'error' => null,
                );

                // Reorder the fields so the confirm box is below the email box 
                qa_html_theme_base::form_reorder_fields($form, array('handle', 'password', 'email', 'custom_confirm'));
            }
        }
        qa_html_theme_base::form_fields($form, $columns);
    }
}

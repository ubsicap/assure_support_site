<?php
/*
    File: qa-plugin/account-reclaim/qa-ar-functions.php
    Description: Contains custom functions and replacements for non-overridable
        functions used in the Account Reclaim process, such as functions that
        originally modified user-related tables and now modify custom tables.
*/



require_once QA_INCLUDE_DIR . 'db/users.php';



/**
 * Return the ids of all users in the Account Reclaim table which match $email (should be one or none)
 * 
 * Modified Function. Original:
 *      qa-include\db\users.php:qa_db_user_find_by_email($email)
 *
 * @param $email
 * @return array
 */
function qa_db_ac_user_find_by_email($email)
{
    return qa_db_read_all_values(qa_db_query_sub(
        'SELECT userid FROM ^accountreclaim WHERE email=$',
        $email
    ));
}



/**
 * Set $field of $userid to $value in the database users table. If the $fields parameter is an array, the $value
 * parameter is ignored and each element of the array is treated as a key-value pair of user fields and values.
 * 
 *  Modified Function. Original:
 *      qa-include\db\users.php:qa_db_user_set($userid, $fields, $value = null)
 * 
 * @param mixed $userid
 * @param string|array $fields
 * @param string|null $value
 */
function qa_ar_db_user_set($userid, $fields, $value = null)
{
    if (!is_array($fields)) {
        $fields = array(
            $fields => $value,
        );
    }

    $sql = 'UPDATE ^accountreclaim SET ';
    foreach ($fields as $field => $fieldValue) {
        $sql .= qa_db_escape_string($field) . ' = $, ';
    }
    $sql = substr($sql, 0, -2) . ' WHERE userid = $';

    $params = array_values($fields);
    $params[] = $userid;

    qa_db_query_sub_params($sql, $params);
}



/**
 * Assembles content for the Recovery page.
 *
 * @param string $request The page being requested
 * @param array $qa_content HTML array of the page
 * @return mixed HTML array of the page.
 */
function generate_recover_content($request, $qa_content)
{
    // Setting the title and errors of the page
    $qa_content['title'] = qa_lang_html('qa-ar/recover_page_title');

    // Body text to describe the purpose of this page and describe the reclaim process
    $qa_content['custom_description'] = qa_lang('qa-ar/recover_page_description');

    // If the user is already logged in, they cannot reclaim an account
    if (qa_is_logged_in()) {
        //qa_redirect(''); // Redirect user to homepage
        $qa_content['error'] .= qa_lang_html('qa-ar/already_logged_in');
        return $qa_content;
    }

    // This form is for entering your email and activating the reclaim process
    $qa_content['form'] = array(
        'tags' => 'method="post" action="' . qa_self_html() . '"',

        'style' => 'tall',

        'fields' => array(
            'email_handle' => array(
                'label' => qa_lang_html('qa-ar/email_label'),
                'tags' => 'name="emailhandle" id="emailhandle"',
                'value' => qa_html(@$inemailhandle),
                'error' => qa_html(@$errors['emailhandle']),
                'note' => qa_lang_html('qa-ar/send_recover_note'),
            ),
        ),

        'buttons' => array(
            'send' => array(
                'label' => qa_lang_html('qa-ar/send_recover_button'),
                // Important! We have to name the button in order to see when it's been clicked
                'tags' => 'name="qa-ar-send-recover" id="qa-ar-send-recover"',
            ),
        ),

        'hidden' => array(
            'dorecover' => '0',
            'code' => qa_get_form_security_code('recover'),
        ),
    );

    // Enable CAPTCHA on this page, if applicable
    if (qa_opt('captcha_on_reset_password')) {
        qa_set_up_captcha_field($qa_content, $qa_content['form']['fields'], @$errors);
    }

    $qa_content['focusid'] = 'emailhandle';

    return $qa_content;
}



/**
 * Assembles content for the Reclaim page.
 *
 * @param string $request The page being requested
 * @param array $qa_content HTML array of the page
 * @return mixed HTML array of the page.
 */
function generate_reclaim_content($request, $qa_content)
{
    // Check we're not using single-sign on integration and that we're not logged in
    if (QA_FINAL_EXTERNAL_USERS) {
        qa_fatal_error('User login is handled by external code');
    }

    if (qa_is_logged_in()) {
        //qa_redirect('');
    }

    // Fetch the email or handle from POST or GET
    $emailHandle = qa_post_text('emailhandle');
    if (!isset($emailHandle)) {
        $emailHandle = qa_get('e');
    }
    $emailHandle = trim($emailHandle); // if $emailHandle is null, trim returns an empty string

    // Fetch the code from POST or GET
    $code = qa_post_text('code');
    if (!isset($code)) {
        $code = qa_get('c');
    }
    $code = trim($code); // if $code is null, trim returns an empty string

    $forgotPath = strlen($emailHandle) > 0 ? qa_path('forgot', array('e' => $emailHandle)) : qa_path('forgot');

    $focusId = 'code';

    $errors = array();
    $fields = array(
        'email_handle' => array(
            'type' => 'static',
            'label' => qa_lang_html(qa_opt('allow_login_email_only') ? 'users/email_label' : 'users/email_handle_label'),
            'value' => qa_html($emailHandle),
        ),
        'code' => array(
            'label' => qa_lang_html('users/email_code_label'),
            'tags' => 'name="code" id="code"',
            'value' => isset($code) ? qa_html($code) : null,
            'note_force' => true,
            'note' => qa_lang_html('users/email_code_emailed') . ' - ' .
                '<a href="' . qa_html($forgotPath) . '">' . qa_lang_html('users/email_code_another') . '</a>',
        ),
    );
    $buttons = array(
        'next' => array(
            'tags' => 'name="donext"',
            'label' => qa_lang_html('misc/next_step'),
        ),
    );
    $hidden = array(
        'formcode' => qa_get_form_security_code('reset'),
    );

    if (strlen($emailHandle) > 0) {
        require_once QA_INCLUDE_DIR . 'app/users-edit.php';
        require_once QA_INCLUDE_DIR . 'db/users.php';

        $hidden['emailhandle'] = $emailHandle;

        $matchingUsers = qa_opt('allow_login_email_only') || strpos($emailHandle, '@') !== false // handles can't contain @ symbols
            ? qa_db_user_find_by_email($emailHandle)
            : qa_db_user_find_by_handle($emailHandle);

        // Make sure there is only one match
        if (count($matchingUsers) == 1) {
            require_once QA_INCLUDE_DIR . 'db/selects.php';

            // strlen() check is vital otherwise we can reset code for most users by entering the empty string
            if (strlen($code) > 0) {
                $userId = $matchingUsers[0];
                $userInfo = qa_db_select_with_pending(qa_db_user_account_selectspec($userId, true));

                if (strtolower(trim($userInfo['emailcode'])) == strtolower($code)) {
                    // User input a valid code so no need to ask for it but pass it to the next step
                    unset($fields['code']);
                    $hidden['code'] = $code;

                    $buttons = array(
                        'change' => array(
                            'tags' => 'name="dochangepassword"',
                            'label' => qa_lang_html('users/change_password'),
                        ),
                    );

                    $focusId = 'newpassword1';

                    if (qa_clicked('dochangepassword')) {
                        $newPassword = qa_post_text('newpassword1');
                        $repeatPassword = qa_post_text('newpassword2');

                        if (!qa_check_form_security_code('reset', qa_post_text('formcode'))) {
                            $errors['page'] = qa_lang_html('misc/form_security_again');
                        } else {
                            $passwordError = qa_password_validate($newPassword, $userInfo);
                            if (!empty($passwordError)) {
                                $errors['new_1'] = $passwordError['password'];
                            }

                            if ($newPassword != $repeatPassword) {
                                $errors['new_2'] = qa_lang('users/password_mismatch');
                            }

                            if (empty($errors)) {
                                // Update password, login user, fire events and redirect to home page
                                qa_finish_reset_user($userId, $newPassword);
                                qa_redirect('');
                            }
                        }
                    }

                    $fields['new_1'] = array(
                        'label' => qa_lang_html('users/new_password_1'),
                        'tags' => 'name="newpassword1" id="newpassword1"',
                        'type' => 'password',
                        'error' => qa_html(isset($errors['new_1']) ? $errors['new_1'] : null),
                    );

                    $fields['new_2'] = array(
                        'label' => qa_lang_html('users/new_password_2'),
                        'tags' => 'name="newpassword2"',
                        'type' => 'password',
                        'error' => qa_html(isset($errors['new_2']) ? $errors['new_2'] : null),
                    );
                } else {
                    // User input wrong code so show field with error
                    $fields['code']['error'] = qa_lang('users/email_code_wrong');
                }
            } elseif (qa_clicked('donext')) {
                // If user submitted the form with an empty code
                $fields['code']['error'] = qa_lang('users/email_code_wrong');
            }
        } else {
            // If match more than one (should be impossible), consider it a non-match
            $errors['page'] = qa_lang_html('users/user_not_found');
        }
    } else {
        // If there is no handle notify the user
        $errors['page'] = qa_lang_html('users/user_not_found');
    }


    // Prepare content for theme

    $qa_content = qa_content_prepare();

    $qa_content['title'] = qa_lang_html('users/reset_title');
    $qa_content['error'] = isset($errors['page']) ? $errors['page'] : null;

    if (!isset($errors['page'])) {
        // Using this form action instead of qa_self_html() to get rid of the 's' (success) GET parameter from forgot.php
        $qa_content['form'] = array(
            'tags' => 'method="post" action="' . qa_path_html('reset') . '"',

            'style' => 'tall',

            'ok' => qa_get('s') ? qa_lang_html('users/email_code_emailed') : null,

            'fields' => $fields,

            'buttons' => $buttons,

            'hidden' => $hidden,
        );
    }

    $qa_content['focusid'] = $focusId;

    return $qa_content;
}

<?php
/*
        File: qa-plugin/account-reclaim/qa-ar-page.php
        Description: Controller for the 'Reclaim Account' pages
*/

require_once QA_INCLUDE_DIR . 'app/captcha.php';
require_once QA_PLUGIN_DIR . 'account-reclaim/qa-ar-functions.php';

class qa_ar_page
{
    // URL of the pages, relative to the site root directory
    const RECOVER_PAGE_URL = 'recover-account';
    const RECLAIM_PAGE_URL = 'account-reclaim';


    /**
     * Determines if the URL requested matches this page. Used to check whether
     * this page should be delivered.
     * 
     * @param $request The URL request being checked
     * @return bool True if the request matches this page
     */
    function match_request($request)
    {
        return $request == self::RECOVER_PAGE_URL || $request == self::RECLAIM_PAGE_URL;
    }



    /**
     * Returns an array of suggested pages for your module.
     * 
     * These suggestions will be displayed within the Q2A admin interface.
     * Each element for a page in the array is itself an array, containing the following elements:
     *      - title contains a human-readable title used to describe the page, e.g. 'More Stats'.
     *      - request contains the Q2A $request string for the page, e.g. 'stats/more'.
     *          Your match_request() function should of course return true for this string.
     *      - nav contains a suggestion about where this page should be linked in the navigation menus.
     *          This is only a hint and can easily be changed by the site's administrator.
     *          Use 'M' for after the main menu, 'B' for before the main menu,
     *          'O' for opposite the main menu, 'F' for the footer, or null for no navigation element.
     * 
     * @return array An array of suggest pages for this module
     */
    function suggest_requests()
    {
        return array(
            array( // Initiating the process; sending recovery email
                'title' => qa_lang('qa-ar/recover_page_title'), // title of page
                'request' => self::RECOVER_PAGE_URL, // request name
                'nav' => null, // 'M'=main, 'F'=footer, 'B'=before main, 'O'=opposite main, null=none
            ),
            array( // Completing the process; resetting account info
                'title' => qa_lang('qa-ar/reclaim_page_title'), // title of page
                'request' => self::RECLAIM_PAGE_URL, // request name
                'nav' => null, // 'M'=main, 'F'=footer, 'B'=before main, 'O'=opposite main, null=none
            ),
        );
    }



    /**
     * Assembles a page with which to respond to the given $request.
     * 
     * This sets up `$qa_content`, which is an array of page elements.
     * 
     * @param string $request The page being requested
     * @return mixed The HTML array of the page.
     */
    function process_request($request)
    {
        // Initialize the qa_content form to include titles/headers/etc.
        $qa_content = qa_content_prepare();
        $qa_content['error'] = @$errors['page'];

        // Process the request appropriately, depending on the page
        switch ($request) {
            case self::RECOVER_PAGE_URL:
                // Content for initiating the reclaim process
                $qa_content = generate_recover_content($request, $qa_content);
                break;
            case self::RECLAIM_PAGE_URL:
                // Content for resetting account info
                $qa_content = generate_reclaim_content($request, $qa_content);
                break;
            default:
                // Invalid page requested
                $qa_content['error'] = qa_lang_html('main/page_not_found');
                break;
        }

        return $qa_content;
    }
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
    // Start the 'Recover Account' process, sending email if appropriate
    if (qa_clicked('qa-ar-send-recover')) {
        require_once QA_INCLUDE_DIR . 'app/users-edit.php';

        $inemailhandle = qa_post_text('emailhandle');

        $errors = array();

        if (!qa_check_form_security_code('recover', qa_post_text('code'))) {
            $errors['page'] = qa_lang_html('misc/form_security_again');
        } else {
            // Fetch the user from the Account Reclaim table
            $matchusers = qa_ar_db_user_find_by_email($inemailhandle);

            if (count($matchusers) != 1) { // if we get more than one match (should be impossible) also give an error
                $errors['emailhandle'] = qa_lang('users/user_not_found');
            }

            if (qa_opt('captcha_on_reset_password')) {
                qa_captcha_validate_post($errors);
            }

            if (empty($errors)) {
                $inuserid = $matchusers[0];

                // Call the overridden function to recover instead of reset
                // Thus, the second (optional) parameter is `true`
                qa_start_reset_user($inuserid, $reclaim = true);

                qa_redirect('account-reclaim', array('e' => $inemailhandle, 's' => '1')); // redirect to page where code is entered
            }
        }
    } else {
        $inemailhandle = qa_get('e');
    }

    /***************************************************************************
     *
     * Now that we've (possibly) obtained input from the user, we will build the
     * web page to be displayed.
     *
     **************************************************************************/

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
                'label' => qa_lang_html('qa-ar/recover_email_label'),
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

    $recoverPath = strlen($emailHandle) > 0 ? qa_path('recover-account', array('e' => $emailHandle)) : qa_path('recover-account');

    $focusId = 'code';

    $errors = array();
    $fields = array(
        'email_handle' => array(
            'type' => 'static',
            'label' => qa_lang_html('qa-ar/reclaim_email_label'),
            'value' => qa_html($emailHandle),
        ),
        'code' => array(
            'label' => qa_lang_html('qa-ar/reclaim_code_label'),
            'tags' => 'name="code" id="code"',
            'value' => isset($code) ? qa_html($code) : null,
            'note_force' => true,
            'note' => qa_lang_html('users/email_code_emailed') . ' - ' .
                '<a href="' . qa_html($recoverPath) . '">' . qa_lang_html('users/email_code_another') . '</a>',
        ),
    );
    $buttons = array(
        'next' => array(
            'tags' => 'name="donext"',
            'label' => qa_lang_html('misc/next_step'),
        ),
    );
    $hidden = array(
        'formcode' => qa_get_form_security_code('reclaim'),
    );

    // If an email address has been provided, perform the reclaim process
    if (strlen($emailHandle) > 0) {
        require_once QA_INCLUDE_DIR . 'app/users-edit.php';
        require_once QA_INCLUDE_DIR . 'db/users.php';

        $hidden['emailhandle'] = $emailHandle;

        // Find the user in the qa_accountreclaim table
        $matchingUsers = qa_ar_db_user_find_by_email($emailHandle);

        // Make sure there is only one match
        if (count($matchingUsers) == 1) {
            require_once QA_INCLUDE_DIR . 'db/selects.php';

            // strlen() check is vital otherwise we can reset code for most users by entering the empty string
            if (strlen($code) > 0) {
                $userId = $matchingUsers[0];
                // This is the correct row from qa_users
                $userInfo = qa_db_select_with_pending(qa_db_user_account_selectspec($userId, true));

                // This query gets us the correct row from qa_accountreclaim
                $reclaimInfo = qa_db_single_select(array(
                    'columns' => array('^accountreclaim.userid', '^accountreclaim.email', '^accountreclaim.reclaimcode'),
                    'source' => '^accountreclaim WHERE ^accountreclaim.userid=$',
                    'arguments' => array($userId),
                    'single' => true,
                ));

                // Check if the reclaim code generated is the same as the code entered
                if (strtolower(trim($reclaimInfo['reclaimcode'])) == strtolower($code)) {
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

                        if (!qa_check_form_security_code('reclaim', qa_post_text('formcode'))) {
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
                                // Update password, login user, fire events and redirect to account page
                                $newUsername = strtok($emailHandle, '@');
                                qa_finish_reset_user($userId, $newPassword, $emailHandle, $newUsername);
                                qa_redirect('account');
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

    $qa_content['title'] = qa_lang_html('qa-ar/reclaim_page_title');
    $qa_content['error'] = isset($errors['page']) ? $errors['page'] : null;

    if (!isset($errors['page'])) {
        // Using this form action instead of qa_self_html() to get rid of the 's' (success) GET parameter from forgot.php
        $qa_content['form'] = array(
            'tags' => 'method="post" action="' . qa_path_html('account-reclaim') . '"',

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

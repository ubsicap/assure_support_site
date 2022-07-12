<?php
/*
        File: qa-plugin/account-reclaim/qa-ar-email-page.php
        Description: Controller for the 'Start Account Reclaim' page
*/

require_once QA_INCLUDE_DIR . 'db/users.php';
require_once QA_INCLUDE_DIR . 'app/captcha.php';
require_once __DIR__ . '/qa-ar-functions.php';

class qa_account_recover_page
{
    // URL of the page, relative to the site root directory
    const PAGE_URL = 'recover-account';



    /**
     * Determines if the URL requested matches this page. Used to check whether
     * this page should be delivered.
     * 
     * @param $request The URL request being checked
     * @return bool True if the request matches this page
     */
    function match_request($request)
    {
        return $request == self::PAGE_URL;
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
            array(
                'title' => qa_lang('qa-ar/recover_page_title'), // title of page
                'request' => self::PAGE_URL, // request name
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

        // Setting the title and errors of the page
        $qa_content['title'] = qa_lang_html('qa-ar/recover_page_title');
        $qa_content['error'] = @$errors['page'];

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
}



// Start the 'Reclaim Account' process, sending email if appropriate
if (qa_clicked('qa-ar-send-recover')) {
    require_once QA_INCLUDE_DIR . 'app/users-edit.php';

    $inemailhandle = qa_post_text('emailhandle');

    $errors = array();

    if (!qa_check_form_security_code('recover', qa_post_text('code'))) {
        $errors['page'] = qa_lang_html('misc/form_security_again');
    } else {
        // Fetch the user from the Account Reclaim table
        $matchusers = qa_db_ac_user_find_by_email($inemailhandle);

        if (count($matchusers) != 1) { // if we get more than one match (should be impossible) also give an error
            $errors['emailhandle'] = qa_lang('users/user_not_found');
        }

        if (qa_opt('captcha_on_reset_password')) {
            qa_captcha_validate_post($errors);
        }

        if (empty($errors)) {
            // TODO: Make sure this field is the correct userid!
            $inuserid = $matchusers[0];

            // Call the overridden function to recover instead of reset
            // Thus, the second (optional) parameter is `true`
            qa_start_reset_user($inuserid, $reclaim = true);

            // TODO: We'll likely need to copy/modify this "reset" page as well
            qa_redirect('reset', array('e' => $inemailhandle, 's' => '1')); // redirect to page where code is entered
        }
    }
} else
    $inemailhandle = qa_get('e');

<?php
/*
        File: qa-plugin/account-reclaim/qa-ar-page.php
        Description: Controller for the 'Reclaim Account' pages
*/

require_once QA_INCLUDE_DIR . 'app/captcha.php';
require_once QA_PLUGIN_DIR . '/account-reclaim/qa-ar-functions.php';

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



// Start the 'Recover Account' process, sending email if appropriate
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
            $inuserid = $matchusers[0];

            // Call the overridden function to recover instead of reset
            // Thus, the second (optional) parameter is `true`
            qa_start_reset_user($inuserid, $reclaim = true);

            qa_redirect('account-reclaim', array('e' => $inemailhandle, 's' => '1')); // redirect to page where code is entered
        }
    }
}

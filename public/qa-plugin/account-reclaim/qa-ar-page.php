<?php
/*
        File: qa-plugin/account-reclaim/qa-ar-reclaim.php
        Description: Controller for the 'Account Reclaim' page
*/

require_once QA_INCLUDE_DIR . 'db/users.php';
require_once QA_INCLUDE_DIR . 'app/captcha.php';
require_once __DIR__ . '/qa-ar-functions.php';

class qa_account_reclaim
{
    // URL of the page, relative to the site root directory
    const PAGE_URL = 'account-reclaim';

    function match_request($request)
    {
        /*
        Should return true if your page module will respond to Q2A page $request.
        */
        return $request == self::PAGE_URL;
    }

    function suggest_requests()
    {
        /*
        Should return an array of suggested pages for your module.
        These suggestions will be displayed within the Q2A admin interface.
        Each element for a page in the array is itself an array, containing the following elements:
                - title contains a human-readable title used to describe the page, e.g. 'More Stats'.
                 - request contains the Q2A $request string for the page, e.g. 'stats/more'.
                        Your match_request() function should of course return true for this string.
                - nav contains a suggestion about where this page should be linked in the navigation menus.
                        This is only a hint and can easily be changed by the site's administrator.
                     Use 'M' for after the main menu, 'B' for before the main menu,
                        'O' for opposite the main menu, 'F' for the footer, or null for no navigation element.
        */
        return array(
            array(
                'title' => qa_lang('qa-ar/title'), // title of page
                'request' => self::PAGE_URL, // request name
                'nav' => null, // 'M'=main, 'F'=footer, 'B'=before main, 'O'=opposite main, null=none
            ),
        );
    }

    function process_request($request)
    {
        /*
        Allows your page module to respond for a particular $request.
        This function should perform any necessary processing based on user inputs,
        and return information about the page to be displayed in a nested Q2A content array,
        named $qa_content by convention.
        (If your function outputs some special content such as XML,
        return null to prevent Q2A displaying anything further.)
        It is beyond the scope of this documentation to describe $qa_content in detail
                - there are many examples in the qa-page-*.php files and the page plugins that come with Q2A.
        You can also see how $qa_content is picked apart at the end of qa-page.php and in main() in qa-theme-base.php.

        Some particular points to note:
            Always start by assigning $qa_content=qa_content_prepare(); to include navigation menus and the like.
            All elements within the nested $qa_content array should be made HTML safe - use qa_html() as appropriate.
            Use $qa_content['title'] and $qa_content['error'] to display a page title or error message respectively.
            To add some custom HTML to the page, add an element to $qa_content whose key begins with custom, e.g. $qa_content['custom'] or $qa_content['custom_2']. The value of the element should contain the actual HTML.
            To add a Q2A-style form to the page, add an element to $qa_content whose key begins with form, e.g. $qa_content['form'] or $qa_content['form_2']. The value of the element should contain a Q2A form array, whose definition is also beyond the scope of this documentation, although see the comments here.
            Other possible types of structured content in $qa_content are question lists (keys starting q_list), question views (q_view), answer lists (a_list) and rankings (ranking). Look through the qa-page-*.php files for examples.
            The elements in $qa_content are displayed in array order, with a few exceptions such as page titles and errors.
        */

        // Initialize the qa_content form to include titles/headers/etc.
        $qa_content = qa_content_prepare();

        // Setting the title and errors of the page
        $qa_content['title'] = qa_lang_html('qa-ar/title');
        $qa_content['error'] = @$errors['page'];

        // Body text to describe the purpose of this page and describe the reclaim process
        $qa_content['custom_description'] = qa_lang_html('qa-ar/page_description');

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
                    'note' => qa_lang_html('qa-ar/send_reclaim_note'),
                ),
            ),

            'buttons' => array(
                'send' => array(
                    'label' => qa_lang_html('qa-ar/send_reclaim_button'),
                    // Important! We have to name the button in order to see when it's been clicked
                    'tags' => 'name="qa-ar-send-reclaim" id="qa-ar-send-reclaim"', 
                ),
            ),

            'hidden' => array(
                'doreclaim' => '0',
                'code' => qa_get_form_security_code('reclaim'),
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
if (qa_clicked('qa-ar-send-reclaim')) {
    require_once QA_INCLUDE_DIR . 'app/users-edit.php';

    $inemailhandle = qa_post_text('emailhandle');

    $errors = array();

    if (!qa_check_form_security_code('reclaim', qa_post_text('code'))) {
        $errors['page'] = qa_lang_html('misc/form_security_again');
    }

    else {
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

            // Call the overridden function to reclaim instead of reset
            // Thus, the second (optional) parameter is `true`
            qa_start_reset_user($inuserid, $reclaim = true);

            // TODO: We'll likely need to copy/modify this "reset" page as well
            qa_redirect('reset', array('e' => $inemailhandle, 's' => '1')); // redirect to page where code is entered
        }
    }
} else
    $inemailhandle = qa_get('e');

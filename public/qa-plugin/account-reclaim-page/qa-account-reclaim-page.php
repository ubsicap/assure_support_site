<?php
/*
        File: qa-plugin/account-reclaim/qa-account-reclaim.php
        Description: Page plugin for Account Reclaim process
*/

require_once QA_INCLUDE_DIR . 'db/users.php';
require_once QA_INCLUDE_DIR . 'app/captcha.php';

class qa_account_reclaim
{
    function match_request($request)
    {
        /*
        Should return true if your page module will respond to Q2A page $request.
        */
        return $request == 'accountreclaim';
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
                'title' => 'Account Reclaim', // title of page
                'request' => 'accountreclaim', // request name
                'nav' => 'M', // 'M'=main, 'F'=footer, 'B'=before main, 'O'=opposite main, null=none
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

        $qa_content = qa_content_prepare();

        $qa_content['title'] = qa_lang_html('users/reset_title');
        $qa_content['error'] = @$errors['page'];

        $qa_content['form'] = array(
            'tags' => 'method="post" action="' . qa_self_html() . '"',

            'style' => 'tall',

            'fields' => array(
                'email_handle' => array(
                    'label' => qa_opt('allow_login_email_only') ? qa_lang_html('users/email_label') : qa_lang_html('users/email_handle_label'),
                    'tags' => 'name="emailhandle" id="emailhandle"',
                    'value' => qa_html(@$inemailhandle),
                    'error' => qa_html(@$errors['emailhandle']),
                    'note' => qa_lang_html('users/send_reset_note'),
                ),
            ),

            'buttons' => array(
                'send' => array(
                    'label' => qa_lang_html('users/send_reset_button'),
                ),
            ),

            'hidden' => array(
                'doforgot' => '1',
                'code' => qa_get_form_security_code('forgot'),
            ),
        );

        if (qa_opt('captcha_on_reset_password'))
            qa_set_up_captcha_field($qa_content, $qa_content['form']['fields'], @$errors);

        $qa_content['focusid'] = 'emailhandle';


        return $qa_content;
    }
}

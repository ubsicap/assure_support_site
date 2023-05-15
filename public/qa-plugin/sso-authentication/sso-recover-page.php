<?php
/*
        File: qa-plugin/account-reclaim/sso-page.php
        Description: Controller for the 'Reclaim Account' pages
*/

// URL of the pages, relative to the site root directory
const CREATE_PAGE_URL = 'create-account-sso';
const RECOVER_PAGE_URL = 'recover-account-sso';

class sso_recover_page
{
    /**
     * Determines if the URL requested matches this page. Used to check whether
     * this page should be delivered.
     * 
     * @param string $request The URL request being checked
     * @return bool True if the request matches this page
     */
    function match_request($request)
    {
        return $request == CREATE_PAGE_URL || $request == RECOVER_PAGE_URL;
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
                'title' => qa_lang('sso-auth/finish_creating_account'), // title of page
                'request' => CREATE_PAGE_URL, // request name
                'nav' => null, // 'M'=main, 'F'=footer, 'B'=before main, 'O'=opposite main, null=none
            ),
            array( // Completing the process; resetting account info
                'title' => qa_lang('sso-auth/recover_page_title'), // title of page
                'request' => RECOVER_PAGE_URL, // request name
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
            case CREATE_PAGE_URL:
                // Content for initiating the reclaim process
                $qa_content = generate_create_content($request, $qa_content);
                break;
            case RECOVER_PAGE_URL:
                // Content for resetting account info
                $qa_content = generate_recover_content_sso($request, $qa_content);
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
function generate_create_content($request, $qa_content)
{
    require_once QA_INCLUDE_DIR . 'app/users-edit.php';
    require_once QA_INCLUDE_DIR . 'app/users.php';

    // if user wants to submit the form
    if (qa_clicked('create_new_account')) {
        // get and validate user's input of username
        $newUsername = qa_post_text('newusername');
        validate_user_input($newUsername);

        // if no errors and receive user's authorization from sso
        if (empty($errors) && isset($_SESSION['id']) && isset($_SESSION['provider'])) {
            if (isset($_SESSION['user_email'])) {

                // Update password, login user, fire events and redirect to account page
                qa_log_in_external_user($_SESSION['provider'], $_SESSION['id'], array(
                    'email' => @$_SESSION['user_email'],
                    'handle' => @$newUsername,
                    'name' => @$newUsername,
                    'confirmed' => true,
                ));
            } else {
                require_once QA_INCLUDE_DIR . 'db/admin.php';
                // when users log in with FB and their FB are not connected to their emails  
                $userEmail = qa_post_text('newemail');

                qa_log_in_external_user($_SESSION['provider'], $_SESSION['id'], array(
                    'email' => @$userEmail,
                    'handle' => @$newUsername,
                    'name' => @$newUsername,
                    'confirmed' => false,
                ));
            }
            header('Location: ' . filter_var(qa_opt('site_url'), FILTER_SANITIZE_URL));
        }
    }

    // If the user is already logged in, they cannot reclaim an account
    if (qa_is_logged_in()) {
        $qa_content['error'] .= qa_lang_html('sso-auth/already_logged_in_new');
        return $qa_content;
    }

    // make sure users will be redirected to this page only after they use sso
    if (!isset($_SESSION['code'])) {
        $qa_content['error'] = qa_lang_html('sso-auth/invalid_authorization_code');
        return $qa_content;
    }

    $focusId = 'newusername';
    $buttons = array(
        'submit' => array(
            'tags' => 'name="create_new_account"',
            'label' => 'Create New Account',
        ),
    );

    // Prompt the user to enter a new username, autofilled with their current username
    $fields['new_0'] = array(
        'label' => qa_lang('sso-auth/create_enter_new_username') . qa_lang_html('users/handle_label'),
        'tags' => 'name="newusername" id="newusername"',
        'value' => isset($newUsername) ? $newUsername : (isset($_SESSION['username']) ?  $_SESSION['username']  : ''),
        'error' => qa_html(isset($errors['new_0']) ? $errors['new_0'] : null),
    );

    // for FB users who do not have email address connected
    if (!isset($_SESSION['user_email'])) {
        // Display text regarding entering email address
        $fields['email_prompt'] = array(
            'type' => 'static',
            'label' => '<hr>' . qa_lang('sso-auth/connect_email'),
        );

        // Field for entering email address
        $fields['new_email'] = array(
            'label' => qa_lang_html('users/email_label'),
            'tags' => 'name="newemail" id="newemail"',
            'error' => qa_html(isset($errors['new_email']) ? $errors['new_email'] : null),
        );
    }

    /***************************************************************************
     *
     * Now that we've (possibly) obtained input from the user, we will build the
     * web page to be displayed.
     *
     **************************************************************************/

    $qa_content = qa_content_prepare();

    $qa_content['title'] = qa_lang_html('sso-auth/finish_creating_account');
    $qa_content['error'] = isset($errors['page']) ? $errors['page'] : null;

    if (!isset($errors['page'])) {
        // Using this form action instead of qa_self_html() to get rid of the 's' (success) GET parameter from forgot.php
        $qa_content['form'] = array(
            'tags' => 'method="post" action="' . qa_opt('site_url') . CREATE_PAGE_URL . '"',

            'style' => 'tall',

            'fields' => $fields,

            'buttons' => $buttons,
        );
    }

    $qa_content['focusid'] = $focusId;

    return $qa_content;
}



/**
 * Assembles content for the Reclaim page.
 *
 * @param string $request The page being requested
 * @param array $qa_content HTML array of the page
 * @return mixed HTML array of the page.
 */
function generate_recover_content_sso($request, $qa_content)
{
    require_once QA_INCLUDE_DIR . 'app/users-edit.php';
    require_once QA_INCLUDE_DIR . 'app/users.php';

    if (qa_clicked('reclaim_account')) {
        $newUsername = qa_post_text('newusername');

        validate_user_input($newUsername);
        if (empty($errors) && isset($_SESSION['id']) && isset($_SESSION['user_email'])) {
            // Update password, login user, fire events and redirect to account page
            qa_finish_reset_user($_SESSION['id'], null, $_SESSION['user_email'], $newUsername);
            header('Location: ' . filter_var(qa_opt('site_url'), FILTER_SANITIZE_URL));
        }
    }

    if (qa_clicked('create_account')) {

        $newUsername = qa_post_text('newusername');
        validate_user_input($newUsername);
        if (empty($errors) && isset($_SESSION['id']) && isset($_SESSION['user_email']) && isset($_SESSION['provider'])) {
            require_once QA_INCLUDE_DIR . 'qa-db.php';
            $sql = 'DELETE FROM ^accountreclaim WHERE ^accountreclaim.email=$';
            qa_db_query_sub($sql, $_SESSION['user_email']);
            qa_log_in_external_user($_SESSION['provider'], $_SESSION['id'], array(
                'email' => @$_SESSION['user_email'],
                'handle' => @$newUsername,
                'name' => @$newUsername,
                'confirmed' => true
            ));

            header('Location: ' . filter_var(qa_opt('site_url'), FILTER_SANITIZE_URL));
        }
    }
    if (qa_is_logged_in()) {
        $qa_content['error'] .= qa_lang_html('sso-auth/already_logged_in_new');
        return $qa_content;
    }
    // make sure users will be redirected to this page only after they use sso
    if (!isset($_SESSION['code'])) {
        $qa_content['error'] = qa_lang_html('main/page_not_found');
        return $qa_content;
    }


    $focusId = 'newusername';
    $buttons = array(
        array(
            'tags' => 'name="reclaim_account"',
            'label' => 'Reclaim Your Account',
        ),
        array(
            'tags' => 'name="create_account"',
            'label' => 'Create New Account',
        ),
    );

    // help to get the handle of the user
    if (isset($_SESSION['id'])) $userInfo = qa_db_select_with_pending(qa_db_user_account_selectspec($_SESSION['id'], true));

    // Prompt the user to enter a new username, autofilled with their current username
    $fields['new_0'] = array(
        'label' => qa_lang('sso-auth/reclaim_enter_new_username') . qa_lang_html('users/handle_label'),
        'tags' => 'name="newusername" id="newusername"',
        'value' => isset($newUsername) ? $newUsername : (isset($userInfo['handle']) ? $userInfo['handle'] : ''),
        'error' => qa_html(isset($errors['new_0']) ? $errors['new_0'] : null),
    );

    /***************************************************************************
     *
     * Now that we've (possibly) obtained input from the user, we will build the
     * web page to be displayed.
     *
     **************************************************************************/

    $qa_content = qa_content_prepare();

    $qa_content['title'] = qa_lang_html('sso-auth/recover_page_title');
    $qa_content['error'] = isset($errors['page']) ? $errors['page'] : null;

    if (!isset($errors['page'])) {
        // Using this form action instead of qa_self_html() to get rid of the 's' (success) GET parameter from forgot.php
        $qa_content['form'] = array(
            'tags' => 'method="post" action="' . qa_opt('site_url') . RECOVER_PAGE_URL . '"',

            'style' => 'tall',

            'fields' => $fields,

            'buttons' => $buttons,
        );
    }

    $qa_content['focusid'] = $focusId;

    return $qa_content;
}

function validate_user_input($newUsername)
{
    // Un-muting this displays some MySQL error about escaped strings...
    $usernameError = @qa_handle_email_filter($newUsername);

    if (isset($usernameError['handle'])) {
        $errors['new_0'] = $usernameError['handle'];

        // If the error is that we are trying to use the same anonymous username that is already set,
        //      just ignore it. After all, you can change your username to itself!
        if ($usernameError['handle'] == qa_lang('users/handle_exists')) {
            $requestedUser = qa_db_single_select(array(
                'columns' => array('^users.userid'),
                'source' => '^users WHERE ^users.handle=$',
                'arguments' => array($newUsername),
                'single' => true,
            ));
            if (isset($requestedUser['userid']) && isset($_SESSION['id']) && $requestedUser['userid'] == $_SESSION['id']) {
                unset($errors['new_0']);
            }
        }
    }
}

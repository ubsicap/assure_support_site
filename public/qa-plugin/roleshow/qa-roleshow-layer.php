<?php

/*
*/

class qa_html_theme_layer extends qa_html_theme_base
{
    function post_meta_who($post, $class) // show usernames of privileged users in italics
    {
        require_once QA_INCLUDE_DIR . 'qa-app-users.php'; // for QA_USER_LEVEL_BASIC constant

        if (isset($post['raw']['opostid'])) { // if item refers to an answer or comment...
            $level = @$post['raw']['olevel']; // ...take the level of answer or comment author
        } else {
            $level = @$post['raw']['level']; // otherwise take level of the question author
        }

        //if ($level > QA_USER_LEVEL_BASIC) // if level is more than basic user...
        //    $post['who']['data'] = '<I>' . @$post['who']['data'] . '</I>'; // ...add italics

        switch ($level) {
            case QA_USER_LEVEL_SUPER:
        $post['who']['data'] = '<b>[Super Administrator]</b> ' . @$post['who']['data'];
                break;
            case QA_USER_LEVEL_ADMIN:
        $post['who']['data'] = '<b>[Administrator]</b> ' . @$post['who']['data'];
                break;
            case QA_USER_LEVEL_MODERATOR:
        $post['who']['data'] = '<b>[Moderator]</b> ' . @$post['who']['data'];
                break;
            case QA_USER_LEVEL_EDITOR:
        $post['who']['data'] = '<b>[Editor]</b> ' . @$post['who']['data'];
                break;
            case QA_USER_LEVEL_EXPERT:
        $post['who']['data'] = '<b>[Expert]</b> ' . @$post['who']['data'];
                break;
            case QA_USER_LEVEL_APPROVED:
                break;
            default:
                break;
        }

        qa_html_theme_base::post_meta_who($post, $class);
    }
}

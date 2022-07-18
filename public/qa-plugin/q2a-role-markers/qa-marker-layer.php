<?php
class qa_html_theme_layer extends qa_html_theme_base
{
    function head_custom()
    {
        qa_html_theme_base::head_custom();

        $this->output('
<style>
' . qa_opt('marker_plugin_css_2') . '				
</style>');
    }

    function post_avatar($post, $class, $prefix = null)
    {
        if (isset($post['avatar']) && (($class == 'qa-q-view' && qa_opt('marker_plugin_a_qv')) || ($class == 'qa-q-item' && qa_opt('marker_plugin_a_qi')) || ($class == 'qa-a-item' && qa_opt('marker_plugin_a_a')) || ($class == 'qa-c-item' && qa_opt('marker_plugin_a_c')))) {
            $uid = $post['raw']['userid'];
            $image = $this->get_role_marker($uid);
            $post['avatar'] = $image . @$post['avatar'];
        }
        qa_html_theme_base::post_avatar($post, $class, $prefix);
    }
    function post_meta($post, $class, $prefix = null, $separator = '<BR/>')
    {

        if (isset($post['who']) && (($class == 'qa-q-view' && qa_opt('marker_plugin_w_qv')) || ($class == 'qa-q-item' && qa_opt('marker_plugin_w_qi')) || ($class == 'qa-a-item' && qa_opt('marker_plugin_w_a')) || ($class == 'qa-c-item' && qa_opt('marker_plugin_w_c')))) {
            $handle = strip_tags($post['who']['data']);
            $uid = $this->getuserfromhandle($handle);
            $image = $this->get_role_marker($uid);
            $post['who']['data'] = $image . $post['who']['data'];
        }
        if (isset($post['who_2']) && (($class == 'qa-q-view' && qa_opt('marker_plugin_w_qv')) || ($class == 'qa-q-item' && qa_opt('marker_plugin_w_qi')) || ($class == 'qa-a-item' && qa_opt('marker_plugin_w_a')) || ($class == 'qa-c-item' && qa_opt('marker_plugin_w_c')))) {
            $handle = strip_tags($post['who_2']['data']);
            $uid = $this->getuserfromhandle($handle);
            $image = $this->get_role_marker($uid);
            $post['who_2']['data'] = $image . $post['who_2']['data'];
        }

        qa_html_theme_base::post_meta($post, $class, $prefix, $separator);
    }
    function ranking_label($item, $class)
    {
        if (qa_opt('marker_plugin_w_users') && $class == 'qa-top-users') {
            $handle = strip_tags($item['label']);
            $uid = $this->getuserfromhandle($handle);
            $image = $this->get_role_marker($uid);
            $item['label'] = $image . $item['label'];
        }
        qa_html_theme_base::ranking_label($item, $class);
    }

    // worker

    function get_role_marker($uid)
    {
        if (QA_FINAL_EXTERNAL_USERS) {
            $user = get_userdata($uid);
            if (isset($user->wp_capabilities['administrator']) || isset($user->caps['administrator']) || isset($user->allcaps['administrator'])) {
                $level = qa_lang('users/level_admin');
                $img = 'admin';
            } elseif (isset($user->wp_capabilities['moderator']) || isset($user->caps['moderator'])) {
                $level = qa_lang('users/level_moderator');
                $img = 'moderator';
            } elseif (isset($user->wp_capabilities['editor']) || isset($user->caps['editor'])) {
                $level = qa_lang('users/level_editor');
                $img = 'editor';
            } elseif (isset($user->wp_capabilities['contributor']) || isset($user->caps['contributor'])) {
                $level = qa_lang('users/level_expert');
                $img = 'expert';
            } else
                return;
        } else {
            $levelno = qa_db_read_one_value(
                qa_db_query_sub(
                    'SELECT level FROM ^users WHERE userid=#',
                    $uid
                ),
                true
            );
            $level = qa_user_level_string($levelno);
            if ($level == qa_lang('users/level_admin') || $level == qa_lang('users/level_super'))
                $img = 'admin';
            elseif ($level == qa_lang('users/level_moderator'))
                $img = 'moderator';
            elseif ($level == qa_lang('users/level_editor'))
                $img = 'editor';
            elseif ($level == qa_lang('users/level_expert'))
                $img = 'expert';
            else
                return;
        }

        $rolemarker = '';

        if (qa_opt('marker_plugin_role_names')) {
            $rolemarker .= '<span class="qa-who-marker-' . $img . '" title="' . qa_html($level) . '">&nbsp;<b>[' . $this->getrolename($uid) . ']</b>  </span>';
        }

        if (qa_opt('marker_plugin_icons_images')) {
            require_once QA_PLUGIN_DIR . 'q2a-role-markers/qa-marker-functions.php';
            $svgFile = qa_get_badge_svg(QA_HTML_THEME_LAYER_URLTOROOT);

            $rolemarker .= '<div class="qa-avatar-marker">'. $svgFile .'</div>';
        } else {
            $rolemarker .= '<span class="qa-who-marker qa-who-marker-' . $img . '" title="' . qa_html($level) . '">' . qa_opt('marker_plugin_who_text') . '</span>';
        }

        return $rolemarker;
    }
    function getuserfromhandle($handle)
    {
        require_once QA_INCLUDE_DIR . 'qa-app-users.php';

        if (QA_FINAL_EXTERNAL_USERS) {
            $publictouserid = qa_get_userids_from_public(array($handle));
            $userid = @$publictouserid[$handle];
        } else {
            $userid = qa_db_read_one_value(
                qa_db_query_sub(
                    'SELECT userid FROM ^users WHERE handle = $',
                    $handle
                ),
                true
            );
        }
        if (!isset($userid)) return;
        return $userid;
    }
    function getrolename($uid)
    {
        $rolename = '';
        $levelno = qa_db_read_one_value(
            qa_db_query_sub(
                'SELECT level FROM ^users WHERE userid=#',
                $uid
            ),
            true
        );
        $level = qa_user_level_string($levelno);
        if ($level == qa_lang('users/level_admin') || $level == qa_lang('users/level_super'))
            $rolename = 'Administrator';
        elseif ($level == qa_lang('users/level_moderator'))
            $rolename = 'Moderator';
        elseif ($level == qa_lang('users/level_editor'))
            $rolename = 'Editor';
        elseif ($level == qa_lang('users/level_expert'))
            $rolename = 'Expert';
        else
            return;

        //return $switch ? '<b>[' . $rolename . ']</b> ' : '';
        return $rolename;
    }
}

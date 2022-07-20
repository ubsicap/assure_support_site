<?php
require_once dirname(QA_PLUGIN_DIR) . '/vendor/autoload.php';

if (qa_opt('facebook_authentication_enabled')) {
    $fb = new Facebook\Facebook([
        'app_id' => qa_opt('facebook_authentication_client_id'),
        'app_secret' => qa_opt('facebook_authentication_client_secret'),
        'default_graph_version' => 'v2.10',
    ]);
    $helper = $fb->getRedirectLoginHelper();
    $permissions = ['email']; // Optional permissions
    $loginUrl = $helper->getLoginUrl(qa_opt('site_url') . 'index.php', $permissions);
}

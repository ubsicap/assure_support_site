<?php
require_once dirname(QA_PLUGIN_DIR) . '/vendor/autoload.php';

if(qa_opt('google_authentication_enabled')) {
    $client = new Google_Client(); 
    $client->setClientId(qa_opt('google_authentication_client_id'));
    $client->setClientSecret(qa_opt('google_authentication_client_secret'));
    $client->setRedirectUri(qa_opt('site_url') . 'index.php');
    $client->addScope("email");
    $client->addScope("profile");
    $authurl = $client->createAuthUrl();
}



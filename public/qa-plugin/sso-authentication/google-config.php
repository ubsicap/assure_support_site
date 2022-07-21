<?php
require_once dirname(QA_PLUGIN_DIR) . '/vendor/autoload.php';

function get_google_url()
{
    if (qa_opt('google_authentication_enabled')) {
        $client = new Google_Client();
        $client->setClientId(qa_opt('google_authentication_client_id'));
        $client->setClientSecret(qa_opt('google_authentication_client_secret'));
        $client->setRedirectUri(qa_opt('site_url') . 'index.php');
        $client->addScope("email");
        $client->addScope("profile");
        return $client->createAuthUrl();
    }
    return ""; //error, just return blank url
}

function get_google_client()
{
    if (qa_opt('google_authentication_enabled')) {
        $client = new Google_Client();
        $client->setClientId(qa_opt('google_authentication_client_id'));
        $client->setClientSecret(qa_opt('google_authentication_client_secret'));
        $client->setRedirectUri(qa_opt('site_url'));
        $client->addScope("email");
        $client->addScope("profile");
        return $client;
    }
    return null;
}
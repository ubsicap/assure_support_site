<?php

/*
    File: qa-plugin/account-reclaim/qa-ar-overrides.php
    Description: Contains Q2A function overrides for the Account Reclaim plugin
*/



require_once QA_PLUGIN_DIR . 'account-reclaim/qa-ar-functions.php';
require_once QA_INCLUDE_DIR . 'app/users.php'; //make sure we have flag value definitions


/**
 * This is an OVERRIDE. THE original function is:
 * qa-include/app/users.php:qa_get_user_avatar_html
 * 
 * Return HTML to display for the user's avatar, constrained to $size pixels, with optional $padding to that size
 *
 * @param int $flags The user's flags
 * @param string $email The user's email. Only needed to return the Gravatar HTML
 * @param string $blobId The blob ID. Only needed to return the locally stored avatar HTML
 * @param string $handle The handle of the user that the avatar will link to
 * @param int $width The width to constrain the image
 * @param int $height The height to constrain the image
 * @param int $size The size to constrain the final image
 * @param bool $padding HTML padding to add to the image
 * @return string|null The HTML to the user's avatar or null if no valid source for the avatar could be found
 */
function qa_get_user_avatar_html($flags, $email, $handle, $blobId, $width, $height, $size, $padding = false)
{
    require_once QA_INCLUDE_DIR . 'app/format.php';

    $avatarSource = qa_get_user_avatar_source($flags, $email, $blobId);

    switch ($avatarSource) {
        case 'gravatar':
            $html = qa_get_gravatar_html($email, $size);
            break;
        case 'local-user':
            $html = qa_get_avatar_blob_html($blobId, $width, $height, $size, $padding);
            break;
        case 'local-default':
            $html = qa_get_random_gravatar_html($flags, $email, $size); //this line is the only change
            if (strlen($handle) == 0) {
                return $html;
            }
            break;
        default: // NULL
            return null;
    }

    return sprintf('<a href="%s" class="qa-avatar-link">%s</a>', qa_path_html('user/' . $handle), $html);
}


/**
 * This is an OVERRIDE. THE original function is:
 * qa-include/app/users.php:qa_get_user_avatar_url
 * 
 * We override this, but it doesn't seem to be used anywhere
 * 
 * Return the avatar URL, either Gravatar or from a blob ID, constrained to $size pixels.
 *
 * @param int $flags The user's flags
 * @param string $email The user's email. Only needed to return the Gravatar link
 * @param string $blobId The blob ID. Only needed to return the locally stored avatar
 * @param int $size The size to constrain the final image
 * @param bool $absolute Whether the link returned should be absolute or relative
 * @return null|string The URL to the user's avatar or null if none could be found (not even as a default site avatar)
 */
function qa_get_user_avatar_url($flags, $email, $blobId, $size = null, $absolute = false)
{
    $avatarSource = qa_get_user_avatar_source($flags, $email, $blobId);

    switch ($avatarSource) {
        case 'gravatar':
            return qa_get_gravatar_url($email, $size);
        case 'local-user':
            return qa_get_avatar_blob_url($blobId, $size, $absolute);
        case 'local-default':
            return qa_get_random_gravatar_url($flags, $email, $size); //this line is the only change
        default: // NULL
            return null;
    }
}
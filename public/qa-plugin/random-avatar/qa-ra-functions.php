<?php
/*
    File: qa-plugin/account-reclaim/qa-ar-functions.php
    Description: Contains custom functions and replacements for non-overridable
        functions used in the Account Reclaim process, such as functions that
        originally modified user-related tables and now modify custom tables.
*/

require_once QA_INCLUDE_DIR . 'app/users.php';

//helper for qa_get_user_avatar_url, get the gravatar random image for a user
/**
 * Return the URL for the Gravatar corresponding to $email, constrained to $size
 * This is a helper for qa_get_user_avatar_url, get the gravatar random image for a user
 *
 * @since 1.8.0
 * @param int flag value of the user, if not confirmed give a plain image, otherwise it will be random
 * @param string $email The email of the Gravatar to return
 * @param int|null $size The size of the Gravatar to return. If omitted the default size will be used
 * @return string The URL to the Gravatar of the user
 */
function qa_get_random_gravatar_url($flags, $email, $size = null)
{
	$link = 'https://www.gravatar.com/avatar/%s';

	$params = array(md5(strtolower(trim($email))));

	$size = (int)$size;
	if ($size > 0) {
		$link .= '?s=%d';
		$params[] = $size;
	}

	$url = vsprintf($link, $params);

    if($flags & QA_USER_FLAGS_EMAIL_CONFIRMED) //email confirmed
        $url .= '&d=identicon'; //random geometric image
    else //email not confirmed
        $url .= '&d=mp'; //mystery person avatar
    return $url;
}

/**
 * Return the <img...> HTML to display the Gravatar for $email, constrained to $size
 * 
 * @param int $flags The user's flags
 * @param $email
 * @param $size
 * @return mixed|null|string
 */
function qa_get_random_gravatar_html($flags, $email, $size)
{
	$avatarLink = qa_html(qa_get_random_gravatar_url($flags, $email, $size)); //only edit is here

	$size = (int)$size;
	if ($size > 0) {
		return sprintf('<img src="%s" width="%d" height="%d" class="qa-avatar-image" alt="" />', $avatarLink, $size, $size);
	} else {
		return null;
	}
}
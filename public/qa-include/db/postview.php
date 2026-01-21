<?php
/*

*/

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}


/**
 * Set the view for $userid on $postid to $viewflag in the database
 * @param $postid
 * @param $userid
 * @param $viewflag
 */
function qa_db_post_view_set($postid, $userid, $viewflag)
{
	error_log("qa_db_post_view_set");
	
	qa_db_query_sub(
		'UPDATE ^posts SET viewflag=$ WHERE postid=$ AND userid=$', $viewflag,$postid, $userid
	);
}

/**
 * Get the view for $userid on $postid from the database
 * @param $postid
 * @param $userid
 * @return $viewflag
 */
function qa_db_post_view_get($postid, $userid)
{
	error_log("qa_db_post_view_get");

	return qa_db_query_sub(
		'SELECT viewflag FROM ^posts WHERE postid=$ AND userid=$', $postid, $userid
	);
}


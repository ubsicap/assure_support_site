<?php
/*
	Posts table, read_status column - Database query and update functions
*/

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}


/**
 * Set the read_status_flag for $userid on $postid to $read_status_flag in the database
 * @param $postid
 * @param $userid
 * @param $read_status_flag
 */
function qa_db_post_read_set($postid, $userid, $read_status_flag)
{	
	qa_db_query_sub(
		'UPDATE ^posts SET read_status=$ WHERE postid=$ AND userid=$', $read_status_flag,$postid, $userid
	);
}

/**
 * Get the read_status_flag for $userid on $postid from the database
 * @param $postid
 * @param $userid
 * @return $read_status_flag
 */
function qa_db_post_read_get($postid, $userid)
{
	return qa_db_query_sub(
		'SELECT read_status FROM ^posts WHERE postid=$ AND userid=$', $postid, $userid
	);
}


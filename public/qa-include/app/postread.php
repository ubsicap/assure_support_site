<?php
/*
	Posts table, read_status column - Application level query and update functions
*/

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}

/**
 * Get the read_status_flag for $userid and $postid from the database
 * @param $postid
 * @param $userid
 * @param $read_status_flag
 */
function qa_post_read_get($postid, $userid)
{
   require_once QA_INCLUDE_DIR . 'db/postread.php';

   return qa_db_post_read_get($postid, $userid);
}

/**
 * Set the read_status_flag for $userid and $postid in the database
 * @param $postid
 * @param $userid
 * @param $read_status_flag
 */
function qa_post_read_set($postid, $userid, $read_status_flag)
{
   require_once QA_INCLUDE_DIR . 'db/postread.php';
   
   qa_db_post_read_set($postid, $userid, $read_status_flag);
}

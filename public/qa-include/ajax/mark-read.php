<?php
/*
    AJAX handler for marking posts as read
*/

require_once QA_INCLUDE_DIR . 'app/users.php';
require_once QA_INCLUDE_DIR . 'app/posts.php';
require_once QA_INCLUDE_DIR . 'db/messages.php';

error_log("AJAX mark-unread.php");

error_log("mark-unread - POST data: " . print_r($POST, true));

// Get user ID
$userid = qa_get_logged_in_userid();

error_log("mark-unread - userid: " . $userid);

if (!isset($userid)) {
    error_log("QA_AJAX_RESPONSE\n0\nNot logged in");
    return;
}

// Get and validate post ID
$post_id = qa_post_text('post_id');

if (!isset($post_id) || empty($post_id)) {
    error_log("QA_AJAX_RESPONSE\n0\nInvalid post ID");
    return;
}

error_log("mark-unread - post_id: " . $post_id);

// Get and validate read_status
$read_status = qa_post_text('read_status');

error_log("mark-unread - read_status: " . $read_status);

// Perform the SQL UPDATE
$result = qa_db_query_sub(
	'UPDATE ^posts SET read_status=$ WHERE postid=$ AND userid=$', $read_status,$post_id, $userid
);


if ($result) {
    echo "QA_AJAX_RESPONSE\n1\n";
} else {
    echo "QA_AJAX_RESPONSE\n0\nUpdate failed";
}
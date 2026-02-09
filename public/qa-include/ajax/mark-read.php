<?php
/*
    AJAX handler for marking posts as read
*/

require_once QA_INCLUDE_DIR . 'app/users.php';
require_once QA_INCLUDE_DIR . 'app/posts.php';
require_once QA_INCLUDE_DIR . 'db/messages.php';


// Get user ID
$userid = qa_get_logged_in_userid();

error_log("mark-read - userid: " . $userid);

if (!isset($userid)) {
    error_log("QA_AJAX_RESPONSE\n0\nNot logged in");
    return;
}

// Get and validate post ID
$postid = qa_post_text('postid');

if (!isset($postid) || empty($postid)) {
    error_log("QA_AJAX_RESPONSE\n0\nInvalid post ID");
    return;
}
$int_postid = intval($postid);
if ($int_postid == 0) {
    error_log("mark-read  - non integer postid, invalid");
    return;
}

// Get and validate read_status
$read_status = qa_post_text('read_status');


// Perform the SQL UPDATE
$result = qa_db_query_sub(
	'UPDATE ^posts SET read_status=$ WHERE postid=$ AND userid=$', $read_status,$int_postid, $userid
);


if ($result) {
    echo "QA_AJAX_RESPONSE\n1\n";
} else {
    echo "QA_AJAX_RESPONSE\n0\nUpdate failed";
}
<?php

require_once QA_INCLUDE_DIR . 'app/users.php';
require_once QA_INCLUDE_DIR . 'app/posts.php';
require_once QA_INCLUDE_DIR . 'db/messages.php';

error_log("GET-MARK-READ");

// Get the GET parameter
$postid = qa_get('postid');

// Get the logged in user ID
$userid = qa_get_logged_in_userid();

// Check if user is logged in
if (!isset($userid)) {
    error_log("QA_AJAX_RESPONSE\n0\nUser not logged in");
    return;
}

// Validate postid
if (!isset($postid) || empty($postid)) {
    error_log("QA_AJAX_RESPONSE\n0\nInvalid post ID");
    return;
}
$int_postid = intval($postid);
if ($int_postid == 0) {
    error_log("GET_MARK-READ - non integer postid, invalid");
    return;
}

// Perform SELECT query to get read_status
$result = qa_db_query_sub(
    'SELECT read_status FROM ^posts WHERE postid = $ AND userid = $',
    $int_postid,
    $userid
);

// Check if record exists
if (qa_db_num_rows($result) > 0) {
    $row = qa_db_read_one_assoc($result, true);
    $read_status = $row['read_status'];

    if ($read_status == null) {
        $read_status = 0;
    }
    echo "QA_AJAX_RESPONSE\n1\n" . $read_status;
} else {
    echo "QA_AJAX_RESPONSE\n1\n0";
}
?>
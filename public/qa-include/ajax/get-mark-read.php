<?php

require_once QA_INCLUDE_DIR . 'app/users.php';
require_once QA_INCLUDE_DIR . 'app/posts.php';
require_once QA_INCLUDE_DIR . 'db/messages.php';

// Get the GET parameter
$postid = qa_get('postid');

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
    'SELECT read_status FROM ^posts WHERE postid = $',
    $int_postid
);

// Check if record exists
if (qa_db_num_rows($result) > 0) {
    $row = qa_db_read_one_assoc($result, true);
    $read_status = $row['read_status'];


    error_log("GET-MARK-READ: retrieving postid: " . $int_postid . ", read_status: " . $read_status);

    if ($read_status == null) {
        $read_status = 0;
    }
    echo "QA_AJAX_RESPONSE\n1\n" . $read_status;
} else {
    echo "QA_AJAX_RESPONSE\n1\n0";
}
?>
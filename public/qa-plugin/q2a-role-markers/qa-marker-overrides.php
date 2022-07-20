<?php

/*
    Description: Contains function overrides for the q2a role marker plugin
*/



require_once QA_PLUGIN_DIR . 'q2a-role-markers/qa-marker-functions.php';



/**
 * Override the delte user function so it removes any custom user titles
 * 
 * @param $userid
 * @return mixed
 */
function qa_delete_user($userid)
{
    if(qa_has_user_title($userid)) //delete any user title
        qa_remove_user_title($userid);
        
    return qa_delete_user_base($userid); //run the base function
}
<?php

//for database interactions
require_once QA_INCLUDE_DIR . 'db/users.php';

/**
 * Return the svg data of the badge icon, used for dynamic coloring
 *
 * @param $id the id the svg tag should be given
 * @return string
 */
function qa_get_badge_svg($id)
{
    $filePath = './qa-plugin/q2a-role-markers/qa-shield-gen.svg';
    $svgFile = fopen($filePath, "r");
    $text = fread($svgFile,filesize($filePath));
    fclose($svgFile);
    return str_replace('class="svg"', 'class="' . $id . '"', $text);
}

/**
 * Create the qa_usertitle table 
 * Format: userid, title 
 * @return none
 */
function qa_create_user_title_table()
{
    qa_db_query_sub(
        'CREATE TABLE IF NOT EXISTS ^usertitles (userid INT UNSIGNED PRIMARY KEY, title VARCHAR(50))'
    );
}

/**
 * Delete the qa_usertitle table  
 * @return none
 */
function qa_delete_user_title_table()
{
    qa_db_query_sub(
        'DROP TABLE IF EXISTS ^usertitles'
    );
}

/**
 * Set a user title, will either update or add the title based on if a title already exists for that user
 * @param userid
 * @param string title
 * @return none
 */
function qa_set_user_title($userid, $title)
{
    if(qa_has_user_title($userid)) //title already exists
        qa_update_user_title($userid, $title);
    else //no title, add instead
        qa_add_user_title($userid, $title);
}

/**
 * Add user title, user should not already have a title defined
 * @param userid
 * @param string title
 * @return none
 */
function qa_add_user_title($userid, $title)
{
    qa_db_query_sub(
        'INSERT INTO ^usertitles (userid, title) VALUES ($, $)', $userid, $title
    );
}

/**
 * Update user title, user should already have a title defined
 * @param userid
 * @param string title
 * @return none
 */
function qa_update_user_title($userid, $title)
{
    qa_db_query_sub(
        'UPDATE ^usertitles SET title=$ WHERE userid=$', $title, $userid
    );
}

/**
 * Delete user title
 * @param userid
 * @return none
 */
function qa_remove_user_title($userid)
{
    qa_db_query_sub(
        'DELETE FROM ^usertitles WHERE userid=$', $userid
    );
}

/**
 * Check if a user has a special title
 * @param userid
 * @return bool whether or not the user has a special title
 */
function qa_has_user_title($userid)
{
    $count = qa_db_read_one_value(qa_db_query_sub(
        'SELECT COUNT(*) FROM ^usertitles WHERE userid=$', $userid
    ));
    return $count != 0; //if the user has a title
}

/**
 * Get user title, assumes the user already has a title
 * @param userid
 * @return string title
 */
function qa_get_user_title($userid)
{
    return qa_db_read_one_value(qa_db_query_sub(
        'SELECT title FROM ^usertitles WHERE userid=$', $userid
    ));
}

/**
 * Simplify a user title to all lowercase no spaces
 * @param string title
 * @return string title simpilfied
 */
function qa_simplify_user_title($title)
{
    $simpleTitle = str_replace(' ', '_', strtolower($title));
    return preg_replace("/[^0-9_a-z\-]/", '', $simpleTitle); //lastly remove any non a-z, 0-9, underscore, or hyphen characters
}



/**
 * Return formatted response for custom title display on user page
 * 
 * 
 * @param int userid of the profile page we're on
 * @param array reponse
 */
function qa_get_user_content($userid)
{
    $tags = 'id="marker-form" action="'.qa_self_html().'#signature_text" method="POST"';

    $textDefault = '';
    if(qa_has_user_title($userid))
        $textDefault = qa_get_user_title($userid);


    $fields[] = array(
        'label' => qa_lang('qa-marker/user_title'),
        'type' => 'text',
        'tags' => 'NAME="marker_custom_title"',
        'value' => $textDefault,
    );
                        
    $buttons[] = array(
        'label' => qa_lang_html('qa-marker/set_custom_title'),
        'tags' => 'NAME="marker_update_title_button"',
    );

    return array(
        'style' => 'wide',
        'tags' => $tags,
        'title' => qa_lang('qa-marker/user_field'),
        'fields'=>$fields,
        'buttons'=>$buttons,
    );
}
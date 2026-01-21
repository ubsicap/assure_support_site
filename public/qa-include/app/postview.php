<?php
/*
	Question2Answer by Gideon Greenspan and contributors
	http://www.question2answer.org/

	Description: Higher-level functions to create and manipulate posts


	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	More about this license: http://www.question2answer.org/license.php
*/

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}

/**
 *	R
 */
function qa_post_view_get($postid, $userid)
{

   error_log("qa_post_view_get");

   require_once QA_INCLUDE_DIR . 'db/postview.php';

   return qa_db_post_view_get($postid, $userid);
}

/**
 *	R
 */
function qa_post_view_set($postid, $userid, $viewflag)
{
   error_log("qa_post_view_set");
      
   require_once QA_INCLUDE_DIR . 'db/postview.php';
   
   qa_db_post_view_set($postid, $userid, $viewflag);
}

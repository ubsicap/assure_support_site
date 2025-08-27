<?php

/*
	Plugin Name: Stop Spam
	Plugin URI: http://www.q2apro.com/plugins/stop-spam
	Plugin Description: An easy to use customizable captcha with a honeypot that keeps 99 % of the spam away
	Plugin Version: 1.0
	Plugin Date: 2014-02-20
	Plugin Author: q2apro.com
	Plugin Author URI: http://www.q2apro.com
	Plugin Minimum Question2Answer Version: 1.5
	Plugin Update Check URI: http://www.q2apro.com/pluginupdate?id=7
	
	Licence: Copyright © q2apro.com - All rights reserved

*/

/*
	This layer prevents user with same IP from voting and selecting.
	
	with q2a v1.6.3 there is only the option "Allow users to answer their own question:" 
	that prevents the answering (and only by username not IP), see admin/posting
*/

	class qa_html_theme_layer extends qa_html_theme_base 
	{

		// global var
		// var $questionIP = ''; 
		// $this->questionIP
		
		// override theme class
		function q_view($q_view) {
			if(qa_opt('q2apro_prevent_spamvotes')) {
				// get IPs
				$questionIP = $q_view['raw']['createip']; // global
				$visitorIP = $_SERVER['REMOTE_ADDR']; // visitor
				
				// SET qa_opt OPTION
				if($visitorIP == $questionIP) {
					if(false) {
						// prevent answering of question if poster and visitor have same IP address
						// remove answer button from form
						unset($q_view['form']['buttons']['answer']);				
					}
					if(true) {
						// prevent voting for himself by disabling the vote button
						$q_view['vote_state'] = 'disabled';
					}
				}
			}
			
			// call default method output
			qa_html_theme_base::q_view($q_view);
		}
		
		
		function a_list_item($a_item)
		{
			if(qa_opt('q2apro_prevent_spamvotes'))
			{
				// prevent voting and selecting if answerer and visitor have the same IP
				$answerIP = $a_item['raw']['createip'];
				$visitorIP = $_SERVER['REMOTE_ADDR']; // visitor
				
				if(isset($answerIP) && isset($visitorIP)) {
					// check if the guy who asked is the same that answered
					// make also sure it is only the involved visitor we restrict
					if($visitorIP == $answerIP) {
						// disable voting button for answer
						$a_item['vote_state'] = 'disabled';
						
						// remove select button from answer
						unset($a_item['select_tags']);
						
						// remove voting button from answer
						// unset($a_item['vote_up_tags']);
						
						// we can always remove the vote-down buttons
						// unset($a_item['vote_down_tags']);
					}
				}
			}
			
			// call default method output
			qa_html_theme_base::a_list_item($a_item);
		}
		
		// prevent anonymous users without IP to answer or comment, allow on ask page 
		public function form_buttons($form, $columns)
		{
			if(qa_opt('q2apro_block_guestswithoutip'))
			{
				$isaskpage = ($this->template == 'ask');
				$userid = qa_get_logged_in_userid();
				$userip = qa_remote_ip_address();
				if($isaskpage || isset($userip) || isset($userid))
				{
					// call default method output
					qa_html_theme_base::form_buttons($form, $columns);
				}
			}
			else
			{
				// call default method output
				qa_html_theme_base::form_buttons($form, $columns);
			}
		}
		
	} // end class
	

	
/*
	Omit PHP closing tag to help avoid accidental output
*/
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

	if (!defined('QA_VERSION')) 
	{ // don't allow this page to be requested directly from browser
		header('Location: ../');
		exit;
	}

	class q2apro_stopspam_filter_posts
	{

		var $directory;
		function load_module($directory, $urltoroot)
		{
			$this->directory=$directory;
		}

		// newly added, prevent certain emails to be registered
		public function filter_email(&$email, $olduser)
		{
			$email = strtolower($email);
			
			// also remove all whitespaces
			$emailbans = preg_replace('/\s+/', '', qa_opt('q2apro_stopspam_filter_emails') );
			if(!empty($emailbans))
			{
				// array from string
				$notAllowed = explode(',', $emailbans); 
				// check if email contains one of the spam strings
				foreach($notAllowed as $string)
				{
					if(strpos($email, $string) !== false)
					{
						// error message frontend
						return qa_lang('q2apro_stopspam_lang/emaildomain_unwanted');
					}
				}
			}
		}
		
		function filter_question(&$question, &$errors, $oldquestion)
		{
			
			$question['content'] = $this->removeLineBreaks($question['content'], $question['format']);
			
			// remove invisible characters from title, mostly happens on copy & paste from PDFs
			$question['title'] = preg_replace('/[^\P{C}\n]+/u', '', $question['title']);
			
			// check if question title already exists, prevent duplicate
			$quTitleExists = qa_db_read_one_value( qa_db_query_sub('SELECT title
										FROM `^posts` 
										WHERE title = #
										AND type = "Q"
										LIMIT 1', $question['title']), true
							);

			if( $quTitleExists && is_null($oldquestion) ) 
			{
				$errors['title'] = qa_lang('q2apro_stopspam_lang/is_duplicate');
			}
		
			// is admin mentioned, e.g. @admin, then send email
			$this->notifyIfMentioned($question['content']);
			
			// intercept certain spam words or domains
			$blockmsg = $this->checkForSpamWords($question['content']);
			if($blockmsg) 
			{
				$errors['content'] = $blockmsg;
				return;
			}
			
			// link array
			$links = array();

			// get number of http links from content 
			// $links = substr_count($question['content'], 'http');
			$links = q2apro_list_all_getUrls_spamplugin($question['content']);
			if(q2apro_stopspam_urlexceeded($links)) 
			{
				$errors['content'] = qa_lang('q2apro_stopspam_lang/too_many_links');
				return;
			}
			
			/* Queue Example */
			// if ( strpos($question['content'], 'idiot') !== false ) {
			// 	  $question['queued'] = true; 
			// }
		}
		
		function filter_answer(&$answer, &$errors, $question, $oldanswer)
		{
			$answer['content'] = $this->removeLineBreaks($answer['content'], $answer['format']);
			
			// is admin mentioned, e.g. @admin, then send email
			$this->notifyIfMentioned($answer['content']);
			
			// intercept certain spam words or domains
			$blockmsg = $this->checkForSpamWords($answer['content']);
			if($blockmsg) 
			{
				$errors['content'] = $blockmsg;
				return;
			}
			
			// check if number of links is exceeded
			$allowedlinks = qa_opt('q2apro_stopspam_allow_links_count');
			// get number of http links from content
			$linkcount = substr_count($answer['content'], 'http');
			if($linkcount>$allowedlinks) 
			{
				$errors['content'] = qa_lang('q2apro_stopspam_lang/too_many_links');
				return;
			}
			
			// normal user that posts links, moderate post by sending it to the queue
			if($linkcount>0 && qa_get_logged_in_level() < QA_USER_LEVEL_EXPERT && qa_opt('q2apro_stopspam_moderate_post_with_links'))
			{
				$answer['queued'] = true;
			}
		}

		function filter_comment(&$comment, &$errors, $question, $parent, $oldcomment)
		{
			$comment['content'] = $this->removeLineBreaks($comment['content'], $comment['format']);
			
			// is admin mentioned, e.g. @admin, then send email
			$this->notifyIfMentioned($comment['content']);
			
			// intercept certain spam words or domains
			$blockmsg = $this->checkForSpamWords($comment['content']);
			if($blockmsg) 
			{
				$errors['content'] = $blockmsg;
			}
			
			// check if number of links is exceeded
			$allowedlinks = qa_opt('q2apro_stopspam_allow_links_count');
			// get number of http links from content
			$linkcount = substr_count($comment['content'], 'http');
			if($linkcount>$allowedlinks) 
			{
				$errors['content'] = qa_lang('q2apro_stopspam_lang/too_many_links');
				return;
			}
			
			// normal user that posts links, moderate post by sending it to the queue
			if($linkcount>0 && qa_get_logged_in_level() < QA_USER_LEVEL_EXPERT && qa_opt('q2apro_stopspam_moderate_post_with_links'))
			{
				$comment['queued'] = true;
			}
		}
		
		/* intercept certain spam words or domains, returns not-allowed-String if spam found, else false */
		function checkForSpamWords($posttext) 
		{
			$checktext = strtolower($posttext);
			// receive spam words from table qa_options, row filter_posts_words, which can be set in plugin options
			// also remove all whitespaces
			$spamwords = preg_replace('/\s+/', '', qa_opt('q2apro_stopspam_filter_words') );
			if(!empty($spamwords)) 
			{
				// array from string
				$notAllowedTerms = explode(',', $spamwords); 
				// check if checktext contains one of the spam strings
				foreach($notAllowedTerms as $string) 
				{
					if(strpos($checktext, $string) !== false) 
					{
						// $answer['queued'] = true;
						// error message
						return qa_lang('q2apro_stopspam_lang/contains_unwanted').' '.$string;
					}
				}
			}
			
			// check for chinese characters and block them
			if(qa_opt('q2apro_stopspam_filter_languages')) 
			{
				$languages = qa_opt('q2apro_stopspam_filter_languages');
				// remove whitespaces and convert string to array
				$blocklanguages = explode(',', str_replace(' ', '', $languages));
				// $blocklanguages = array('Han','Hangul','Hebrew','Arabic','Cyrillic','Greek','Khmer');
				foreach($blocklanguages as $lang) 
				{
					if(preg_match("/\p{".$lang."}+/u", $posttext)) 
					{
						if($lang=='Han') 
						{
							// just for frontend that users understand
							$lang = 'Chinese';
						}
						return qa_lang('q2apro_stopspam_lang/contains_unwanted_lang').' '.$lang;
					}
				}
			}
			
			// no spam word found
			return false;
		}
		
		/* remove empty p and br tags causing unnecessary line breaks */
		function removeLineBreaks(&$content, &$format) 
		{
		
			if(qa_opt('q2apro_stopspam_remove_linebreaks')) 
			{
				// only TEXT - the text is converted to nl2br later
				if(empty($format)) 
				{
					// merge \n line-breaks into one
					$content = preg_replace('/( (\\n\s*))+/im', "\n", $content);
					// \s : match also new lines
					// * : /cat*/g matches all the cats in "cat catamaran cater"
					// i : ignore case
					// m : go over multiple lines (match the beginning or end of each line, delimited by \n or \r, not only the very beginning or end of the whole input string)
					
					// remove line breaks from end of string, i.e. text ending with \n
					$content = preg_replace('/(\n)+$/', '', $content);
				}
				// HTML with tags
				else 
				{
					// merge empty p tags into one
					$content = preg_replace('/((<p\s*\/?>\s*)&nbsp;(<\/p\s*\/?>\s*))+/im', "<p>&nbsp;</p>\n", $content);
				
					// remove spaces from end of string (&nbsp;)
					$content = preg_replace('/(&nbsp;)+$/', '', $content);
					
					// remove sceditor's: <p>\n<br>\n</p> from end of string
					// \s* matches any number of whitespace characters (" ", \t, \n, etc)
					// (?:...)+ matches one or more (without capturing the group)
					// $ forces match to only be made at the end of the string
					$content = preg_replace("/(?:<p>\s*(<br>\s*)+\s*<\/p>\s*)+$/", "", $content);
				
					// remove line breaks from end of string - $ is end of line, +$ is end of line including \n
					// html with <p>&nbsp;</p>
					$content = preg_replace('/(<p>&nbsp;<\/p>)+$/', '', $content);
					$content = preg_replace('/(<br>)+$/', '', $content);

					// remove line breaks from beginning of string
					$content = preg_replace('/^(<p>&nbsp;<\/p>)+/', '', $content);
				}
				
				// filter and mask email addresses (also done in jquery)
				// $pattern = '/[^@\s]*@[^@\s]*\.[^@\s]*/';
				if(qa_opt('q2apro_stopspam_mask_emails')) 
				{
					$pattern = '/[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}/';
					$replacement = qa_lang('q2apro_stopspam_lang/emails_notallowed');
					$content = preg_replace($pattern, $replacement, $content);
				}
				
				/* remove invisible characters, mostly happens on copy & paste from PDFs */
				$content = preg_replace('/[^\P{C}\n]+/u', '', $content);
			}
		
			// done
			return $content;
		}
		
		function notifyIfMentioned($fullstring) 
		{
			$searchstring = qa_opt('q2apro_stopspam_notify_mentioning');
			if (!empty($searchstring) && strpos($fullstring,$searchstring) !== false) 
			{
				require_once QA_INCLUDE_DIR.'qa-app-emails.php';
				
				// qa_send_notification($userid, $email, $handle, $subject, $body, $subs)
				qa_send_notification(null, qa_opt('feedback_email'), null, 'You were mentioned in a post', $fullstring, null);
			}
		}
		
	}

	function q2apro_list_all_getUrls_spamplugin($string) 
	{
		// $regex = '/https?\:\/\/[^\" ]+/i';
		$regex = '#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#';
		preg_match_all($regex, $string, $matches);
		return ($matches[0]);
	}

	function q2apro_stopspam_urlexceeded($links)
	{ 
		$spamurlcount = 0;
		
		if(count($links)>0) 
		{
			// remove duplicate links
			$links = array_unique($links);
			
			// ignore internal links
			$ignorelinks = array(qa_opt('site_url'));
			
			for($i=0;$i<count($links);$i++) 
			{
				// check against list of ignore words
				foreach($ignorelinks as $ignorelink) 
				{
					if(isset($links[$i]))
					{
						if(strpos($links[$i],$ignorelink) !== false) 
						{
						}
						else 
						{
							$spamurlcount++;
						}
					}
				}
			}
		}
		// check if number of links is exceeded
		return ($spamurlcount>qa_opt('q2apro_stopspam_allow_links_count'));
	}


/*
	Omit PHP closing tag to help avoid accidental output
*/
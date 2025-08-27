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


	return array(
		// default
		'enable_plugin' => 'Enable Stop-Spam-Captcha Plugin',
		'contact' => 'For questions please visit ^1q2apro.com^2',
		
		'note' => 'Note:',
		'disable_captcha' => 'If you want to disable the captcha, please go to ^1Admin > Spam^2 and choose another captcha module.',
		
		// plugin
		'label_q' => 'Captcha Question:',
		'label_a' => 'Answer option 1:',
		'label_a2' => 'Answer option 2:',
		'label_error' => 'Error message',
		'captcha_question' => 'What is the color of the sky?',
		'captcha_answer' => 'blue',
		'captcha_answer2' => 'red',
		'captcha_errormsg' => 'Your answer is incorrect.',
		'emails_notallowed' => '*Emails are not allowed*',
		'is_duplicate' => 'A question with this title already exists. Please add more details.',
		'emaildomain_unwanted' => 'Trashmails are not allowed.',
		'contains_unwanted' => 'Your post contains unwanted words:',
		'contains_unwanted_lang' => 'Your post contains a disallowed language:',
		'too_many_links' => 'Your post contains too many links to external sites.',
		'notify_mentioning' => 'Notify admin by email if this mentioning is found:',
		'allow_links_count' => 'Number of allowed links per post:',
		'mask_emails' => 'Remove email addresses from posts. Example: spam@forum.com becomes *Emails are not allowed*',
		'remove_linebreaks' => 'Remove too many linebreaks and merge them into one. Also remove empty linebreaks in the post end.',
		'prevent_spamvotes' => 'Prevent user with same IP from voting for his own posts and selecting his answers as best.',
		'open_page' => 'Open page:',
		'block_guestswithoutip' => 'Do not allow guests without IP address to post in forum.',
		'moderate_post_with_links' => 'Moderate posts (answers, comments) with links. Will send them to the ^1moderate queue^2.',
	);
	

/*
	Omit PHP closing tag to help avoid accidental output
*/
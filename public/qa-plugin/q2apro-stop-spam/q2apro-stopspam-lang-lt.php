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
		'captcha_question' => 'Kiek yra DU kart PENKI?',
		'captcha_answer' => '10',
		'captcha_answer2' => 'dešimt',
		'captcha_errormsg' => 'Atsakymas ne teisingas.',
		'emails_notallowed' => '*E-mail neleidžiama*', // E-mails are not allowed
		'is_duplicate' => 'Klausimas šiuo pavadinimu jau yra. Papildyk pavadinimą naujom detalėm.',
		'emaildomain_unwanted' => 'Spam el. pašto neleidžiama.',
		'contains_unwanted' => 'Tavo laiškuose yra nepageidaujamų išsireiškimų:',
		'contains_unwanted_lang' => 'Tavo laiškuose yra uždraustą kalbą:',
		'too_many_links' => 'Your post contains too many links.',
		'notify_mentioning' => 'Notify the admin by email if this mentioning is found:',
		'allow_links_count' => 'Number of allowed links per post:',
		'mask_emails' => 'Remove email addresses from posts. Example: spam@forum.com becomes *Emails are not allowed*',
		'remove_linebreaks' => 'Removes too many linebreaks and merges it into one. Also removes empty linebreaks in the end of the post.',
	);
	

/*
	Omit PHP closing tag to help avoid accidental output
*/
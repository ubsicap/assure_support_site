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
		'enable_plugin' => 'Stop-Spam-Captcha Plugin aktivieren',
		'contact' => 'Bei Fragen bitte ^1q2apro.com^2 besuchen.',
		
		'note' => 'Hinweis:',
		'disable_captcha' => 'Wenn Sie das Captcha deaktivieren möchten, gehen Sie bitte zu ^1Admin > Spam^2 und wählen Sie ein anderes Captcha-Modul aus.',
		
		// plugin
		'label_q' => 'Captcha-Frage:',
		'label_a' => 'Antwort-Option 1:',
		'label_a2' => 'Antwort-Option 2:',
		'label_error' => 'Fehlermeldung',
		'captcha_question' => 'Welche Farbe hat der Himmel?',
		'captcha_answer' => 'blau',
		'captcha_answer2' => 'hellblau',
		'captcha_errormsg' => 'Deine Antwort ist leider nicht korrekt.',
		'emails_notallowed' => '*E-mails sind nicht erlaubt*',
		'is_duplicate' => 'Eine Frage mit diesem Titel existiert bereits. Bitte füge mehr Details hinzu.',
		'emaildomain_unwanted' => 'Trashmails sind nicht erlaubt.',
		'contains_unwanted' => 'Dein Post enthält geblockte Wörter:',
		'contains_unwanted_lang' => 'Dein Post enthält eine nicht-erlaubte Sprache:',
		'too_many_links' => 'Ihr Post beinhaltet zu viele Links zu externen Seiten.',
		'notify_mentioning' => 'Benachrichtige den Admin per E-Mail, wenn diese Erwähnung gefunden wird:',
		'allow_links_count' => 'Number of allowed links per post:',
		'mask_emails' => 'Remove email addresses from posts. Example: spam@forum.com becomes *Emails are not allowed*',
		'remove_linebreaks' => 'Removes too many linebreaks and merges it into one. Also removes empty linebreaks in the end of the post.',
	);
	

/*
	Omit PHP closing tag to help avoid accidental output
*/
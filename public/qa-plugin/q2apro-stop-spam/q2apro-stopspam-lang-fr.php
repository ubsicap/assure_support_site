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
		'enable_plugin' => 'Activer le plugin Stop-Spam-Captcha',
		'contact' => 'Si vous avez des questions, visitez ^1q2apro.com^2',
		
		'note' => 'Note:',
		'disable_captcha' => 'If you want to disable the captcha, please go to ^1Admin > Spam^2 and choose another captcha module.',
		
		// plugin
		'label_q' => 'Captcha question:',
		'label_a' => 'Option de réponse 1:',
		'label_a2' => 'Option de réponse 2:',
		'label_error' => 'Message d\'erreur',
		'captcha_question' => 'Quelle est la couleur du ciel?',
		'captcha_answer' => 'bleu',
		'captcha_answer2' => 'rouge',
		'captcha_errormsg' => 'Votre réponse est incorrecte.',
		'emails_notallowed' => '*Les e-mails ne sont pas autorisés*',
		'is_duplicate' => 'Une question avec ce titre existe déjà. Se il vous plaît ajouter plus de détails.',
		'emaildomain_unwanted' => 'Trashmails sond indésirables.',
		'contains_unwanted' => 'Votre message contient des mots indésirables:',
		'contains_unwanted_lang' => 'Votre message contient une langue refusé:',
		'too_many_links' => 'Your post contains too many links.',
		'notify_mentioning' => 'Avertissez l\'administrateur par mail si ce mention se trouve:',
		'allow_links_count' => 'Number of allowed links per post:',
		'mask_emails' => 'Remove email addresses from posts. Example: spam@forum.com becomes *Emails are not allowed*',
		'remove_linebreaks' => 'Removes too many linebreaks and merges it into one. Also removes empty linebreaks in the end of the post.',
	);
	

/*
	Omit PHP closing tag to help avoid accidental output
*/
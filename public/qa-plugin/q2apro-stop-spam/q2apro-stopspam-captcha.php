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
	{ 	// don't allow this page to be requested directly from browser
		header('Location: ../');
		exit;
	}

	class q2apro_stopspam_captcha 
	{
	
		// set captcha module to Stop Spam Captcha on first plugin run
		function init_queries($tableslc) 
		{
			if(qa_opt('q2apro_stopspam_captcha_init')!='1') 
			{
				qa_opt('captcha_module', 'Stop Spam Captcha');
				qa_opt('q2apro_stopspam_captcha_init', '1');
			}
		}

		var $directory;
		
		function load_module($directory, $urltoroot)
		{
			$this->directory=$directory;
		}

		// option's value is requested but the option has not yet been set
		function option_default($option) 
		{
			switch($option) 
			{
				case 'q2apro_stopspam_captcha_question':
					return qa_lang('q2apro_stopspam_lang/captcha_question');
				case 'q2apro_stopspam_captcha_answer':
					return qa_lang('q2apro_stopspam_lang/captcha_answer');
				case 'q2apro_stopspam_captcha_answer2':
					return qa_lang('q2apro_stopspam_lang/captcha_answer2');
				case 'q2apro_stopspam_captcha_errormsg':
					return qa_lang('q2apro_stopspam_lang/captcha_errormsg');
				case 'q2apro_stopspam_filter_emails':
					return 'trbvn.com, trashmail, 10minutemail.com';
				case 'q2apro_stopspam_filter_words':
					return '';
				case 'q2apro_stopspam_filter_languages':
					return '';
				case 'q2apro_stopspam_notify_mentioning':
					return '@admin';
				case 'q2apro_stopspam_allow_links_count':
					return '3';
				case 'q2apro_stopspam_moderate_post_with_links':
					return '';
				case 'q2apro_stopspam_remove_linebreaks':
					return '1'; // true
				case 'q2apro_stopspam_mask_emails':
					return '1'; // true
				case 'q2apro_prevent_spamvotes':
					return '1'; // true
				case 'q2apro_block_guestswithoutip':
					return '1'; // true
				default:
					return null;
			}
		}
		
		function admin_form(&$qa_content) 
		{

			// process the admin form if admin hit Save-Changes-button
			$ok = null;
			if (qa_clicked('q2apro_stopspam_save')) 
			{
				qa_opt('q2apro_stopspam_captcha_question', (string)qa_post_text('q2apro_stopspam_captcha_question'));
				qa_opt('q2apro_stopspam_captcha_answer', (string)qa_post_text('q2apro_stopspam_captcha_answer'));
				qa_opt('q2apro_stopspam_captcha_answer2', (string)qa_post_text('q2apro_stopspam_captcha_answer2'));
				qa_opt('q2apro_stopspam_captcha_errormsg', (string)qa_post_text('q2apro_stopspam_captcha_errormsg'));
				// filter words
                qa_opt('q2apro_stopspam_filter_emails', (string)qa_post_text('q2apro_stopspam_filter_emails'));
                qa_opt('q2apro_stopspam_filter_words', (string)qa_post_text('q2apro_stopspam_filter_words'));
                qa_opt('q2apro_stopspam_filter_languages', (string)qa_post_text('q2apro_stopspam_filter_languages'));
                qa_opt('q2apro_stopspam_notify_mentioning', (string)qa_post_text('q2apro_stopspam_notify_mentioning'));
                qa_opt('q2apro_stopspam_allow_links_count', (int)qa_post_text('q2apro_stopspam_allow_links_count'));
                qa_opt('q2apro_stopspam_moderate_post_with_links', (int)qa_post_text('q2apro_stopspam_moderate_post_with_links'));
                qa_opt('q2apro_stopspam_remove_linebreaks', (int)qa_post_text('q2apro_stopspam_remove_linebreaks'));
                qa_opt('q2apro_stopspam_mask_emails', (int)qa_post_text('q2apro_stopspam_mask_emails'));
                qa_opt('q2apro_prevent_spamvotes', (int)qa_post_text('q2apro_prevent_spamvotes'));
                qa_opt('q2apro_block_guestswithoutip', (int)qa_post_text('q2apro_block_guestswithoutip'));
				$ok = qa_lang('admin/options_saved');
			}
			
			// form fields to display frontend for admin
			$fields = array();
			
			$fields[] = array(
				'type' => 'static',
				'note' => qa_lang('q2apro_stopspam_lang/open_page').' <a style="color:#00F;" href="'.qa_path('listlinks').'">List Links in all Posts</a>',
			);
			
			$fields[] = array(
				'type' => 'input',
				'label' => qa_lang('q2apro_stopspam_lang/label_q'),
				'tags' => 'name="q2apro_stopspam_captcha_question"',
				'value' => qa_opt('q2apro_stopspam_captcha_question'),
			);
			
			$fields[] = array(
				'type' => 'input',
				'label' => qa_lang('q2apro_stopspam_lang/label_a'),
				'tags' => 'name="q2apro_stopspam_captcha_answer"',
				'value' => qa_opt('q2apro_stopspam_captcha_answer'),
			);
			
			$fields[] = array(
				'type' => 'input',
				'label' => qa_lang('q2apro_stopspam_lang/label_a2'),
				'tags' => 'name="q2apro_stopspam_captcha_answer2"',
				'value' => qa_opt('q2apro_stopspam_captcha_answer2'),
			);
			
			$fields[] = array(
				'type' => 'input',
				'label' => qa_lang('q2apro_stopspam_lang/label_error'),
				'tags' => 'name="q2apro_stopspam_captcha_errormsg"',
				'value' => qa_opt('q2apro_stopspam_captcha_errormsg'),
			);
			
			// 'options/captcha_module'
			$fields[] = array(
				'type' => 'static',
				'note' => '<span><b>'.qa_lang('q2apro_stopspam_lang/note').'</b> '.strtr( qa_lang('q2apro_stopspam_lang/disable_captcha'), array( 
							'^1' => '<a target="_blank" href="./spam">',
							'^2' => '</a>'
						  )).'</span>',
			);

			// anti spam emails
			$fields[] = array(
                'type' => 'textarea',
				'rows' => 10,
				'label' => 'List domains (or part of their names) that prevent user registration:',
				'tags' => 'name="q2apro_stopspam_filter_emails"',
				'value' => qa_opt('q2apro_stopspam_filter_emails'),
			);
			
			// anti spam words
			$fields[] = array(
                'type' => 'textarea',
				'rows' => 10,
				'label' => 'Spam words that prevent the posting to be submitted (separate with comma):',
				'tags' => 'name="q2apro_stopspam_filter_words"',
				'value' => qa_opt('q2apro_stopspam_filter_words'),
			);
			
			// spam languages
			$fields[] = array(
				'type' => 'input',
				'label' => 'Disallow certain languages here, separate them by comma. Available are: Han,Hangul,Hebrew,Arabic,Cyrillic,Greek,Khmer. Note: "Han" stands for Chinese.',
				'tags' => 'name="q2apro_stopspam_filter_languages"',
				'value' => qa_opt('q2apro_stopspam_filter_languages'),
			);
			
			// notify on mentioning
			$fields[] = array(
				'type' => 'input',
				'label' => qa_lang('q2apro_stopspam_lang/notify_mentioning'),
				'tags' => 'name="q2apro_stopspam_notify_mentioning"',
				'value' => qa_opt('q2apro_stopspam_notify_mentioning'),
			);
			
			// number of allowed links in post
			$fields[] = array(
				'type' => 'number',
				'label' => qa_lang('q2apro_stopspam_lang/allow_links_count'),
				'tags' => 'name="q2apro_stopspam_allow_links_count"',
				'value' => qa_opt('q2apro_stopspam_allow_links_count'),
			);
			
			// moderate answer-comment posts with links 
			$fields[] = array(
				'type' => 'checkbox',
				'label' => strtr( qa_lang('q2apro_stopspam_lang/moderate_post_with_links'), array( 
							'^1' => '<a href="'.qa_path('admin/moderate').'">',
							'^2' => '</a>'
						  )),
				'tags' => 'name="q2apro_stopspam_moderate_post_with_links"',
				'value' => qa_opt('q2apro_stopspam_moderate_post_with_links'),
			);
			
			// mask email addresses
			$fields[] = array(
				'type' => 'checkbox',
				'label' => qa_lang('q2apro_stopspam_lang/mask_emails'),
				'tags' => 'name="q2apro_stopspam_mask_emails"',
				'value' => qa_opt('q2apro_stopspam_mask_emails'),
			);
			
			// remove too many line breaks and merge them into one
			$fields[] = array(
				'type' => 'checkbox',
				'label' => qa_lang('q2apro_stopspam_lang/remove_linebreaks'),
				'tags' => 'name="q2apro_stopspam_remove_linebreaks"',
				'value' => qa_opt('q2apro_stopspam_remove_linebreaks'),
			);
			
			// prevent spam votes
			$fields[] = array(
				'type' => 'checkbox',
				'label' => qa_lang('q2apro_stopspam_lang/prevent_spamvotes'),
				'tags' => 'name="q2apro_prevent_spamvotes"',
				'value' => qa_opt('q2apro_prevent_spamvotes'),
			);
			
			// prevent spam votes
			$fields[] = array(
				'type' => 'checkbox',
				'label' => qa_lang('q2apro_stopspam_lang/block_guestswithoutip'),
				'tags' => 'name="q2apro_block_guestswithoutip"',
				'value' => qa_opt('q2apro_block_guestswithoutip'),
			);
			
			// q2apro link
			$fields[] = array(
				'type' => 'static',
				'note' => '<span style="font-size:75%;color:#789;">'.strtr( qa_lang('q2apro_stopspam_lang/contact'), array( 
							'^1' => '<a target="_blank" href="http://www.q2apro.com/plugins/stop-spam">',
							'^2' => '</a>'
						  )).'</span>',
			);

			return array(           
				'ok' => ($ok && !isset($error)) ? $ok : null,
				'fields' => $fields,
				'buttons' => array(
					array(
						'label' => qa_lang('main/save_button'),
						'tags' => 'name="q2apro_stopspam_save"',
					),
				),
			);
		}
		
		// indicates whether the module is ready to be used
		function allow_captcha()
		{
			return true;
		}

		// returns the HTML form to be displayed for the captcha challenge
		function form_html(&$qa_content, $error)
		{
		  	$html = '<div class="q2apro_captcha">';
			$html .= '<span class="captcha_q">'.qa_opt('q2apro_stopspam_captcha_question').'</span>';
		  	$html .= '<input type="text" name="captcha_challenge_field" />';
			$html .= '<input type="checkbox" name="response_field" id="response_field" />';
			$html .= '</div>';
			// insert css to hide the honeypot
			$html .= '<style type="text/css"> .captcha_q{margin-right:10px;color:#00C;} #response_field{display:none;} .q2apro_captcha { margin-bottom:20px; }</style>';

			return $html;
		}

		// check if user has given the correct answer
		function validate_post(&$error)
		{
			// honeypot: bot filled input field (which is hidden by CSS)
			if(!empty($_POST['response_field']))
			{
				$error = 's-p-a-m-b-o-t';
			}
			else if(!empty($_POST['captcha_challenge_field']))
			{
				// correct answer given
				if( $_POST['captcha_challenge_field']==qa_opt('q2apro_stopspam_captcha_answer')
					|| $_POST['captcha_challenge_field']==qa_opt('q2apro_stopspam_captcha_answer2') ) 
				{
					return true;
				}
				else 
				{
					// wrong answer
					$error = qa_opt('q2apro_stopspam_captcha_errormsg');
				}
			}
			return false;
		}
	
	}
	

/*
	Omit PHP closing tag to help avoid accidental output
*/
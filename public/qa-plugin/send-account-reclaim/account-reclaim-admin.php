<?php

/*
	Plugin Name: Account Reclaim
	Plugin Description: send emails to remind users to reclaim their account
	Plugin Version: 1.0
	Plugin Date: 2022-07-13	
	Licence: Copyright © q2apro.com - All rights reserved

*/

class account_reclaim_admin
{

	// option's value is requested but the option has not yet been set
	function option_default($option)
	{
		switch ($option) {
			case 'account_reclaim_enabled':
				return true;
			default:
		}
	}

	function admin_form(&$qa_content)
	{

		// process the admin form if admin hit Save-Changes-button
		$ok = null;
		if (qa_clicked('account_reclaim_start')) {
			qa_opt('account_reclaim_enabled', (bool)qa_post_text('account_reclaim_enabled')); // empty or 1
			qa_opt('account_reclaim_email_name', qa_post_text('account_reclaim_email_name'));
			qa_opt('account_reclaim_email_address', qa_post_text('account_reclaim_email_address'));
			qa_opt('account_reclaim_email_subject_line', qa_post_text('account_reclaim_email_subject_line'));
			qa_opt('account_reclaim_email_body_text', qa_post_text('account_reclaim_email_body_text'));
			$this->send_account_reclaim_email();

			$ok = 'sent successfully';
		}

		return array(
			'ok' => ($ok && !isset($error)) ? $ok : null,
			'fields' => array(
				array(
					'type' => 'checkbox',
					'label' => qa_lang('account_reclaim_lang/enable_plugin'),
					'tags' => 'name="account_reclaim_enabled"',
					'value' => qa_opt('account_reclaim_enabled'),
				),
				array(
					// 'id' => 'category_logo_url_display_' .$category_backpath['backpath']. '',
					'label' => 'From name:',
					'type' => 'text',
					'value' => qa_opt('account_reclaim_email_name'),
					'tags' => 'name="account_reclaim_email_name"',
				),
				array(
					// 'id' => 'category_logo_url_display_' .$category_backpath['backpath']. '',
					'label' => 'From email address:',
					'type' => 'text',
					'value' => qa_opt('account_reclaim_email_address'),
					'tags' => 'name="account_reclaim_email_address"',
				),
				array(
					// 'id' => 'category_logo_url_display_' .$category_backpath['backpath']. '',
					'label' => 'Subject line:',
					'type' => 'text',
					'value' => qa_opt('account_reclaim_email_subject_line'),
					'tags' => 'name="account_reclaim_email_subject_line"',
				),
				array(
					// 'id' => 'category_logo_url_display_' .$category_backpath['backpath']. '',
					'label' => 'Body text:',
					'type' => 'text',
					'value' => qa_opt('account_reclaim_email_body_text'),
					'tags' => 'name="account_reclaim_email_body_text"',
				)
			),
			'buttons' => array(
				array(
					'label' => 'Start Mailing',
					'tags' => 'name="account_reclaim_start"',
				)
			),
		);
	}

	function send_account_reclaim_email()
	{
		require_once QA_INCLUDE_DIR . 'db/users.php';
		require_once QA_INCLUDE_DIR . 'app/users.php';
		require_once QA_INCLUDE_DIR . 'app/emails.php';
		require_once QA_INCLUDE_DIR . 'qa-db.php';

		$users = qa_db_query_raw( 
			'SELECT email FROM qa_accountreclaim'
		);
		// echo '<script type="text/JavaScript"> 
		// console.log("'.$users.'");
		// </script>';
		foreach ($users as $user) {
					echo '<script type="text/JavaScript"> 
		console.log("'.$user['email'].'");
		</script>';
			$send_status = qa_send_email(array(
				'fromemail' => qa_opt('account_reclaim_email_address'),
				'fromname' => qa_opt('account_reclaim_email_name'),
				'toemail' => $user['email'],
				'toname' => 'Ling',
				'subject' => qa_opt('account_relaim_email_subject_line'),
				'body' => trim(qa_opt('account_reclaim_email_body_text')),
				'html' => false,
			));
			if (!$send_status) {
				echo '<script type="text/JavaScript"> 
     alert("not okay");
     </script>';
			}
		}
	}
}


/*
	Omit PHP closing tag to help avoid accidental output
*/
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
			
			$ok = $this->send_account_reclaim_email();
			if (!strcmp($ok, 'None being sent.')) {
				echo '<script type="text/JavaScript"> 
					window.addEventListener("load", (event) => {
						var grandparent = document.getElementsByClassName("qa-part-form-plugin-options")[0];
						var target = grandparent.querySelector("form .qa-form-table-boxed .qa-form-tall-table tbody tr td");
						target.classList.remove("qa-form-tall-ok");
						target.className = "qa-form-tall-error";
					});
     				</script>';
			}
				
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
					'label' => 'From name:',
					'type' => 'text',
					'value' => qa_opt('account_reclaim_email_name'),
					'tags' => 'name="account_reclaim_email_name"',
				),
				array(
					'label' => 'From email address:',
					'type' => 'text',
					'value' => qa_opt('account_reclaim_email_address'),
					'tags' => 'name="account_reclaim_email_address"',
				),
				array(
					'label' => 'Subject line:',
					'type' => 'text',
					'value' => qa_opt('account_reclaim_email_subject_line'),
					'tags' => 'name="account_reclaim_email_subject_line"',
				),
				array(
					'label' => 'Body text:',
					'type' => 'textarea',
					'rows' => 6,
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
		$unsent = 0;
		foreach ($users as $user) {
			$send_status = qa_send_email(array(
				'fromemail' => qa_opt('account_reclaim_email_address'),
				'fromname' => qa_opt('account_reclaim_email_name'),
				'toemail' => $user['email'],
				'toname' => '',
				'subject' => qa_opt('account_relaim_email_subject_line'),
				'body' => trim(qa_opt('account_reclaim_email_body_text')),
				'html' => false,
			));
			if (!$send_status) {
				$unsent += 1;
			}
		}
		$amount= qa_db_read_one_value(qa_db_query_sub(
			'SELECT COUNT(*) FROM qa_accountreclaim'
		));
		$sent = $amount - $unsent;
		$log = $sent > 0 ? 'Sent '.$sent.'/'.$amount.' successfully.': 'None being sent.';
		return $log;
	}
}


/*
	Omit PHP closing tag to help avoid accidental output
*/
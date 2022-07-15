<?php

class sso_authentication_admin
{

	// option's value is requested but the option has not yet been set
	function option_default($option)
	{
		switch ($option) {
			case 'sso_authentication_enabled':
				return true;
			default:
		}
	}

	function admin_form(&$qa_content)
	{

		// process the admin form if admin hit Save-Changes-button
		$ok = null;
		if (qa_clicked('sso_authentication_start')) {
			qa_opt('sso_authentication_enabled', (bool)qa_post_text('sso_authentication_enabled')); // empty or 1
			qa_opt('sso_authentication_client_id', qa_post_text('sso_authentication_client_id'));
			qa_opt('sso_authentication_client_secret', qa_post_text('sso_authentication_client_secret'));
			$ok = 'Options saved';
			// $ok = $this->send_sso_authentication_email();
			// if (!strpos($ok, 'successfully')) {
			// 	echo '<script type="text/JavaScript"> 
			// 		window.addEventListener("load", (event) => {
			// 			var grandparent = document.getElementsByClassName("qa-part-form-plugin-options")[0];
			// 			var target = grandparent.querySelector("form .qa-form-table-boxed .qa-form-tall-table tbody tr td");
			// 			target.classList.remove("qa-form-tall-ok");
			// 			target.className = "qa-form-tall-error";
			// 		});
     		// 		</script>';
			// }
		}

		return array(
			'ok' => ($ok && !isset($error)) ? $ok : null,
			'fields' => array(
				array(
					'type' => 'checkbox',
					'label' => 'Enable plugin',
					'tags' => 'name="sso_authentication_enabled"',
					'value' => qa_opt('sso_authentication_enabled'),
				),
				array(
					'label' => 'Client id:',
					'type' => 'text',
					'value' => qa_opt('sso_authentication_client_id'),
					'tags' => 'name="sso_authentication_client_id"',
				),
				array(
					'label' => 'Client secret:',
					'type' => 'text',
					'value' => qa_opt('sso_authentication_client_secret'),
					'tags' => 'name="sso_authentication_client_secret"',
				)
			),
			'buttons' => array(
				array(
					'label' => 'Save options',
					'tags' => 'name="sso_authentication_start"',
				)
			),
		);
	}

	// function send_sso_authentication_email()
	// {
	// 	$fromemail = qa_opt('sso_authentication_client_secret');
	// 	$fromname = qa_opt('sso_authentication_client_id');
	// 	$subject = qa_opt('sso_authentication_email_subject_line');
	// 	$body = trim(qa_opt('sso_authentication_email_body_text'));
	// 	//make sure input is valid
	// 	if(!($fromname && $fromemail && $subject && $body)) return 'Please make sure all fields are filled.';
	// 	require_once QA_INCLUDE_DIR . 'db/users.php';
	// 	require_once QA_INCLUDE_DIR . 'app/users.php';
	// 	require_once QA_INCLUDE_DIR . 'app/emails.php';
	// 	require_once QA_INCLUDE_DIR . 'qa-db.php';
	// 	require_once QA_INCLUDE_DIR . 'util/string.php';

	// 	$users = qa_db_query_raw(
	// 		'SELECT email FROM qa_accountreclaim'
	// 	);
	// 	$unsent = 0;
	// 	foreach ($users as $user) {
	// 		$send_status = qa_send_email(array(
	// 			'fromemail' => $fromemail,
	// 			'fromname' => $fromname,
	// 			'toemail' => $user['email'],
	// 			'toname' => '',
	// 			'subject' => $subject,
	// 			'body' => $body,
	// 			'html' => qa_opt('html_enabled'),
	// 		));

	// 		if (!$send_status && !qa_email_validate($email)) {
	// 			$unsent += 1;
	// 		}
	// 	}
	// 	$amount = qa_db_read_one_value(qa_db_query_sub(
	// 		'SELECT COUNT(*) FROM qa_accountreclaim'
	// 	));
	// 	$sent = $amount - $unsent;
	// 	$log = $sent > 0 ? 'Sent ' . $sent . '/' . $amount . ' successfully.' : 'Sent uncessfully.';
	// 	return $log;
	// }
}


/*
	Omit PHP closing tag to help avoid accidental output
*/
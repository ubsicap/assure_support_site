<?php

class post_validator_admin
{

	// option's value is requested but the option has not yet been set
	function option_default($option)
	{
		switch ($option) {
			case 'post_validator_enabled':
				return 1; // true
			default:
		}
	}

	function admin_form(&$qa_content)
	{

		// process the admin form if admin hit Save-Changes-button
		$ok = null;
		if (qa_clicked('post_validator_enable')) {
			qa_opt('post_validator_enabled', (bool)qa_post_text('post_validator_enabled')); // empty or 1
			qa_opt('check_email', (bool) qa_post_text('check_email')); //enable html or not
			$ok = qa_lang('post_validator_lang/options_saved');
		}

		return array(
			'ok' => ($ok && !isset($error)) ? $ok : null,
			'fields' => array(
				array(
					'type' => 'checkbox',
					'label' => qa_lang('post_validator_lang/enable_plugin'),
					'tags' => 'name="post_validator_enabled"',
					'value' => qa_opt('post_validator_enabled'),
				),
				array(
					'type' => 'checkbox',
					'label' => qa_lang('post_validator_lang/check_email'),
					'tags' => 'name="check email"',
					'value' => qa_opt('check_email'),
				),
			),
			'buttons' => array(
				array(
					'label' => qa_lang('post_validator_lang/enable_plugin'),
					'tags' => 'name="post_validator_enable"',
				)
			),
		);
	}
}


/*
	Omit PHP closing tag to help avoid accidental output
*/
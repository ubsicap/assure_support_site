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

			//enable different validator options
			qa_opt('post_val_check_name', (bool) qa_post_text('check_name'));
			qa_opt('post_val_enable_warning', (bool) qa_post_text('enable_warning'));
			qa_opt('post_val_check_email', (bool) qa_post_text('check_email'));
			qa_opt('post_val_check_phone', (bool) qa_post_text('check_phone'));
			qa_opt('post_val_check_registration', (bool) qa_post_text('check_registration'));
			qa_opt('post_val_check_ip', (bool) qa_post_text('check_ip'));
			qa_opt('post_val_check_mac', (bool) qa_post_text('check_mac'));
			qa_opt('post_val_check_image', (bool) qa_post_text('check_image'));
			
			$ok = qa_lang('post_validator_lang/options_saved');
		}

		return array(
			'ok' => ($ok && !isset($error)) ? $ok : null,
			'fields' => array(
				array( //enable plugin
					'type' => 'checkbox',
					'label' => qa_lang('post_validator_lang/enable_plugin'),
					'tags' => 'name="post_validator_enabled"',
					'value' => qa_opt('post_validator_enabled'),
				),
				array( //name
					'type' => 'checkbox',
					'label' => qa_lang('post_validator_lang/check_name'),
					'tags' => 'name="check name"',
					'value' => qa_opt('post_val_check_name'),
				),
				array( //name
					'type' => 'checkbox',
					'label' => qa_lang('post_validator_lang/enable_warning'),
					'tags' => 'name="enable warning"',
					'value' => qa_opt('post_val_enable_warning'),
				),
				array( //email
					'type' => 'checkbox',
					'label' => qa_lang('post_validator_lang/check_email'),
					'tags' => 'name="check email"',
					'value' => qa_opt('post_val_check_email'),
				),
				array( //phone
					'type' => 'checkbox',
					'label' => qa_lang('post_validator_lang/check_phone'),
					'tags' => 'name="check phone"',
					'value' => qa_opt('post_val_check_phone'),
				),
				array( //registration
					'type' => 'checkbox',
					'label' => qa_lang('post_validator_lang/check_registration'),
					'tags' => 'name="check registration"',
					'value' => qa_opt('post_val_check_registration'),
				),
				array( //ip
					'type' => 'checkbox',
					'label' => qa_lang('post_validator_lang/check_ip'),
					'tags' => 'name="check ip"',
					'value' => qa_opt('post_val_check_ip'),
				),
				array( //mac
					'type' => 'checkbox',
					'label' => qa_lang('post_validator_lang/check_mac'),
					'tags' => 'name="check mac"',
					'value' => qa_opt('post_val_check_mac'),
				),
				array( //image
					'type' => 'checkbox',
					'label' => qa_lang('post_validator_lang/check_image'),
					'tags' => 'name="check image"',
					'value' => qa_opt('post_val_check_image'),
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
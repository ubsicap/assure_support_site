<?php

class random_verse
{

	// option's value is requested but the option has not yet been set
	function option_default($option)
	{
		switch ($option) {
			case 'random_verse_enabled':
				return 1; // true
			default:
		}
	}

	function admin_form(&$qa_content)
	{

		// process the admin form if admin hit Save-Changes-button
		if (qa_clicked('save_option')) {
			qa_opt('random_verse_enabled', (bool)qa_post_text('random_verse_enabled')); // empty or 1
			qa_opt('random_verse_body_text', qa_post_text('random_verse_body_text'));
		}

		return array(
			'fields' => array(
				array(
					'type' => 'checkbox',
					'label' => qa_lang('random_verse_lang/enable_plugin'),
					'tags' => 'name="random_verse_enabled"',
					'value' => qa_opt('random_verse_enabled'),
				),
				array(
					'label' => qa_lang('random_verse_lang/verse_text'),
					'type' => 'textarea',
					'rows' => 15,
					'value' => qa_opt('random_verse_body_text'),
					'tags' => 'name="random_verse_body_text"',
				)
			),
			'buttons' => array(
				array(
					'label' => qa_lang('random_verse_lang/save_option'),
					'tags' => 'name="save_option"',
				)
			),
		);
	}
}


/*
	Omit PHP closing tag to help avoid accidental output
*/
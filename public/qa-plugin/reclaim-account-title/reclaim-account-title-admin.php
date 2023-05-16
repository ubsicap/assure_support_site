<?php

class reclaim_account_title_admin {

	public function admin_form(&$qa_content)
	{
		$saved = qa_clicked('save_button');

		if ($saved) 
			qa_opt('reclaim_account_title_on', (int) qa_post_text('reclaim_account_title_on_field'));

		return array(
			'ok' => $saved ? 'Mouseover settings saved' : null,

			'fields' => array(
				array(
					'label' => 'Show reclaim account subtitle in registration form',
					'type' => 'checkbox',
					'value' => qa_opt('reclaim_account_title_on'),
					'tags' => 'name="reclaim_account_title_on_field" id="reclaim_account_title_on_field"',
				),
			),

			'buttons' => array(
				array(
					'label' => 'Save Changes',
					'tags' => 'name="save_button"',
				),
			),
		);
	}
       
  
}
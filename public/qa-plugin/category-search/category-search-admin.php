<?php
class category_search_admin {

	function allow_template($template)
	{
		return ($template!='admin');
	}

	function option_default($option) {
		switch($option) {
			case 'category_search_enabled':
				return 1; // true
			default:
				return null;				
		}
	}   
		
	function admin_form(&$qa_content){                       

		$ok = null;
		if (qa_clicked('category_search_save')) {
			qa_opt('category_search_enabled', (bool)qa_post_text('category_search_enabled')); // empty or 1
			$ok = qa_lang('admin/options_saved');
		}
		
		$fields = array();
		
		$fields[] = array(
			'type' => 'checkbox',
			'label' => 'Enable Plugin',
			'tags' => 'name="category_search_enabled"',
			'value' => qa_opt('category_search_enabled'),
		);
		
		
		return array(           
			'ok' => ($ok && !isset($error)) ? $ok : null,
			'fields' => $fields,
			'buttons' => array(
				array(
					'label' => qa_lang_html('main/save_button'),
					'tags' => 'name="category_search_save"',
				),
			),
		);
	}

}

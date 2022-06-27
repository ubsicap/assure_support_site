<?php

class qa_category_logo {
    
    public function option_default($option)
	{
		if ($option === 'category_logo_url')
			return '';
	}

    public function admin_form(&$qa_content)
	{
        $saved = qa_clicked('category_logo_save_button');

		if ($saved) {
			qa_opt('category_logo_on', (int) qa_post_text('category_logo_on_field'));
			qa_opt('category_logo_url', qa_post_text('category_logo_url_field'));
		}

		// qa_set_display_rules($qa_content, array(
		// 	'category_logo_url_display' => 'category_logo_url_field',
		// ));
        
        $urls = $this->get_categories();
        //make array consists of prompt and specific labels with corresponding categories
        $fields = array(array(
            'label' => 'Add specific logo to corresponding categories',
            'type' => 'checkbox',
            'value' => qa_opt('category_logo_on'),
            'tags' => 'name="category_logo_on_field" id="category_logo_on_field"',
        ));
        foreach($urls as $url) {
            array_push($fields, $url);
        }
		return array(
			'ok' => $saved ? 'Category logo settings saved' : null,
            'fields' => $fields,
			'buttons' => array(
				array(
					'label' => 'Save Changes',
					'tags' => 'name="category_logo_save_button"',
				),
			),
		);
    }

    public function get_categories() {
        $categories = array();
        $results = qa_db_query_raw( //get the titles of categories from db
			'SELECT title From qa_categories'
		);
        $i = 0;
        //make array consists of specific labels with corresponding categories
        foreach($results as $result) {
            $categories[$i] = array( 
                'id' => 'category_logo_url_display' .$i. '',
                'label' => 'Url for logo for ' .$result['title']. ':',
                'type' => 'text',
                'value' => qa_opt('category_logo_url'.$i. ''),
                'tags' => 'name="category_logo_url_field".$i. ""',
            );
            $i++;
        }
        return $categories;
    }
}
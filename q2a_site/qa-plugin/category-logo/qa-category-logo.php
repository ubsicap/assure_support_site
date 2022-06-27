<?php

class qa_category_logo {

    public function admin_form(&$qa_content)
	{
        //$this->debug_to_console($qa_content);
        $urls = $this->get_categories();
        $categories_amount = count($urls);
        $saved = qa_clicked('category_logo_save_button');
		if ($saved) {
			qa_opt('category_logo_on', (int) qa_post_text('category_logo_on_field'));
            for($i=0;$i<$categories_amount;$i++) {
            	qa_opt('category_logo_url' .$i.'', qa_post_text('category_logo_url_field'.$i.''));
            }
		}

        $this -> qa_set_display_rules_helper($categories_amount);

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

    private function get_categories() {
        $categories = array();
        $i = 0;
        $results = qa_db_query_raw( //get the titles of categories from db
            'SELECT title From qa_categories'
        );
        //make array consists of specific labels with corresponding categories
        foreach($results as $result) {
            $tag_name = "category_logo_url_field" .$i. "";
            $categories[$i] = array( 
                'id' => 'category_logo_url_display' .$i. '',
                'label' => 'Url for logo for ' .$result['title']. ':',
                'type' => 'text',
                'value' => qa_opt('category_logo_url'.$i. ''),
                'tags' => 'name=' .$tag_name. "",
            );
            $i++;
        }
        return $categories;
    }

    private function qa_set_display_rules_helper($categories_amount ) {
        $array = array();
        for($i=0;$i<$categories_amount;$i++) {
            $array += array('category_logo_url_display' .$i. '' => 'category_logo_url_field'.$i.'');
        }
        qa_set_display_rules($qa_content, $array);
    }
}
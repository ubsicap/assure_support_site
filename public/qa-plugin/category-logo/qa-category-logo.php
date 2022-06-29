<?php

class qa_category_logo {

    public function admin_form(&$qa_content)
	{
        $categories_backpaths = $this->get_categories_backpath();
        $saved = qa_clicked('category_logo_save_button');

		if ($saved) {
			qa_opt('category_logo_on', (int) qa_post_text('category_logo_on_field'));
            foreach($categories_backpaths as $category_backpath) {
            	qa_opt('category_logo_url_' .$category_backpath['backpath'].'', qa_post_text('category_logo_url_field_'.$category_backpath['backpath'].''));
            }
		}

        $this -> qa_set_display_rules_helper($categories_backpaths);
        //make array consists of prompt and specific labels with corresponding categories
        $fields = array(array(
            'label' => 'Add specific logo to corresponding categories',
            'type' => 'checkbox',
            'value' => qa_opt('category_logo_on'),
            'tags' => 'name="category_logo_on_field" id="category_logo_on_field"',
        ));
        $results = $this->get_categories($categories_backpaths);
        foreach($results as $result) {
            array_push($fields, $result);
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

    //get the backpaths of categories from db
    private function get_categories_backpath() {
        return qa_db_query_raw( 
            'SELECT backpath From qa_categories'
        );
    }

    //returns the well-written array of categories needed for admin form
    private function get_categories($categories_backpaths) {
        $categories = array();
        $i = 0;
        //make array consists of specific labels with corresponding categories
        foreach($categories_backpaths as $category_backpath) {
            $tag_name = "category_logo_url_field_" .$category_backpath['backpath']. "";
            $categories[$i] = array( 
                'id' => 'category_logo_url_display_' .$category_backpath['backpath']. '',
                'label' => 'URL of logo for category ' .str_replace("-", " ", $category_backpath['backpath']). ' - image address or local file (relative to Q2A root) :',
                'type' => 'text',
                'value' => qa_opt('category_logo_url_'.$category_backpath['backpath']. ''),
                'tags' => 'name="'.$tag_name.'"',
            );
            $i++;
        }
        return $categories;
    }

    private function qa_set_display_rules_helper($categories_backpath) {
        $array = array();
        foreach($categories_backpath as $category_backpath) {
            $array['category_logo_url_display_' .$category_backpath['backpath']. ''] = "category_logo_on_field";
        }
        qa_set_display_rules($qa_content, $array);
    }
}
<?php

class qa_html_theme_layer extends qa_html_theme_base
{

	function search()
	{
		if (qa_opt('category_search_enabled')) {
			$search = $this->content['search'];
			$this->output(
				'<form class="search-form" role="form" method="get" action="' . qa_path('category-search-page') . '">',
				@$search['form_extra']
			);
        
			$this->search_field($search);
			$this->output(
				'</form>'
			);
		} else
			parent::search();
	}


	function search_button($search)
	{
		if (qa_opt('category_search_enabled')) {
			$this->output('<span class="input-group-btn">');
			$this->search_dropdown_category();
			$this->output('<button type="submit" value="" class="btn qa-search-button" ><span class="fa fa-search"></span></button>');
			$this->output('</span>');
		} else
			parent::search_button($search);
	}

	function search_dropdown_category()
	{
		$categories = $this->get_all_categories_titles();

		$selectedCategories = $this->content['search']['category'];

		$dropdownHTML = '<select name="search-category" class="qa-search-dropdown">
		<option value="" disabled selected>Select Category</option>'; //serve as label

		// Loop through the categories to add each as an <option>
		foreach ($categories as $categoryTitle) {
			// Check if this category should be selected
			$selectedAttribute = (strcmp($categoryTitle, $selectedCategories) == 0) ? ' selected' : '';
			$dropdownHTML .= '<option value="' . htmlspecialchars($categoryTitle, ENT_QUOTES, 'UTF-8') . '"' . $selectedAttribute . '>' . htmlspecialchars($categoryTitle, ENT_QUOTES, 'UTF-8') . '</option>';
		}

		$dropdownHTML .= '</select>';

		$this->output($dropdownHTML);
	}

	function head_css()
	{
		qa_html_theme_base::head_css();
		if (qa_opt('category_search_enabled')) {
			$this->output(
				' 
				<style type="text/css">
				.qa-search-dropdown {
					padding: 10px 45px 10px 12px;
    				font-size: 13px;
					height: auto;
					box-sizing: border-box;
					vertical-align: middle;
					border: 0.1px solid #ccc;
					background-color: #fff;
					cursor: pointer;
				}
				
				.qa-search-dropdown:focus {
					border-color: #66afe9;
					outline: 0;
					
				}
				</style>'
			);
		}
	}

	function get_all_categories_titles()
	{
		$titles = array();
		$result = qa_db_query_sub('SELECT title FROM ^categories ORDER BY position ASC');
		$titles = qa_db_read_all_values($result);

		return $titles;
	}
}

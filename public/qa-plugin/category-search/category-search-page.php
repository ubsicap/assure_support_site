<?php

class category_search_page
{

	private $directory;
	private $urltoroot;


	public function load_module($directory, $urltoroot)
	{
		$this->directory = $directory;
		$this->urltoroot = $urltoroot;
	}


	public function match_request($request)
	{
		return $request == 'category-search-page';
	}


	public function process_request($request)
	{

		require_once QA_INCLUDE_DIR . 'app/format.php';
		require_once QA_INCLUDE_DIR . 'app/options.php';
		require_once QA_INCLUDE_DIR . 'app/search.php';

		qa_set_template("search");

		// Perform the search if appropriate
		if (strlen(qa_get('q'))) {
			// Pull in input parameters
			$inquery = trim(qa_get('q'));
			$searchCategory = qa_get('search-category');  // Retrieve the search category parameter
			$userid = qa_get_logged_in_userid();
			$start = qa_get_start();

			$display = qa_opt_if_loaded('page_size_search');
			$count = 4 * (isset($display) ? $display : QA_DB_RETRIEVE_QS_AS) + 1;

			if (!empty($searchCategory)) {
				// Get the category ID from the search category
				$categoryId = $this->get_category_id($searchCategory);
			}

			// Perform the search using appropriate module
			$results = $this->qa_get_search_results($inquery, $start, $count, $userid, false, false, $categoryId ?: null);

			$pagesize = qa_opt('page_size_search');
			$gotcount = count($results);
			$results = array_slice($results, 0, $pagesize);

			// Retrieve extra information on users
			$fullquestions = array();

			foreach ($results as $result) {
				if (isset($result['question']))
					$fullquestions[] = $result['question'];
			}

			$usershtml = qa_userids_handles_html($fullquestions);

			// Report the search event
			// Prepare the event data array
			$eventData = array(
				'query' => $inquery,
				'start' => $start
			);

			// Add 'category' to the array if $searchCategory is not empty
			if (!empty($searchCategory)) {
				$eventData['search-category'] = $searchCategory;
			}

			// Report the search event
			qa_report_event('search', $userid, qa_get_logged_in_handle(), qa_cookie_get(), $eventData);
		}

		// Prepare content for theme
		$qa_content = qa_content_prepare(true);

		if (strlen(qa_get('q'))) {
			$qa_content['search']['value'] = qa_html($inquery);

			$searchDisplay = $inquery;
			if (!empty($searchCategory)) {
				$searchCategory = qa_html($searchCategory); 
				$qa_content['search']['category'] = $searchCategory;
				$searchDisplay = '"' . $inquery . '"' . ' in ' . $searchCategory;
			}
			if (count($results))
				$qa_content['title'] = qa_lang_html_sub('main/results_for_x', qa_html($searchDisplay));
			else
				$qa_content['title'] = qa_lang_html_sub('main/no_results_for_x', qa_html($searchDisplay));


			$qa_content['q_list']['form'] = array(
				'tags' => 'method="post" action="' . qa_self_html() . '"',
				'hidden' => array(
					'code' => qa_get_form_security_code('vote'),
				),
			);

			$qa_content['q_list']['qs'] = array();

			$qdefaults = qa_post_html_defaults('Q');

			foreach ($results as $result) {
				if (!isset($result['question'])) { // if we have any non-question results, display with less statistics
					$qdefaults['voteview'] = false;
					$qdefaults['answersview'] = false;
					$qdefaults['viewsview'] = false;
					break;
				}
			}

			foreach ($results as $result) {
				if (isset($result['question'])) {
					$fields = qa_post_html_fields(
						$result['question'],
						$userid,
						qa_cookie_get(),
						$usershtml,
						null,
						qa_post_html_options($result['question'], $qdefaults)
					);
				} elseif (isset($result['url'])) {
					$fields = array(
						'what' => qa_html($result['url']),
						'meta_order' => qa_lang_html('main/meta_order'),
					);
				} else {
					continue; // nothing to show here
				}

				if (isset($qdefaults['blockwordspreg']))
					$result['title'] = qa_block_words_replace($result['title'], $qdefaults['blockwordspreg']);

				$fields['title'] = qa_html($result['title']);
				$fields['url'] = qa_html($result['url']);

				$qa_content['q_list']['qs'][] = $fields;
			}

			// Prepare the parameters for the page links
			$pageLinkParams = array('q' => $inquery);

			// Conditionally add the 'category' parameter if it is not empty
			if (!empty($searchCategory)) {
				$pageLinkParams['search-category'] = $searchCategory;
			}

			// Generate the page links
			$qa_content['page_links'] = qa_html_page_links(
				qa_request(),
				$start,
				$pagesize,
				$start + $gotcount,
				qa_opt('pages_prev_next'),
				$pageLinkParams,
				$gotcount >= $count
			);

			if (qa_opt('feed_for_search')) {
				$qa_content['feed'] = array(
					'url' => qa_path_html(qa_feed_request('search/' . $searchDisplay)),
					'label' => qa_lang_html_sub('main/results_for_x', qa_html($searchDisplay)),
				);
			}

			if (empty($qa_content['page_links']))
				$qa_content['suggest_next'] = qa_html_suggest_qs_tags(qa_using_tags());
		} else
			$qa_content['error'] = qa_lang_html('main/search_explanation');

		return $qa_content;
	}

	function qa_get_search_results($query, $start, $count, $userid, $absoluteurls, $fullcontent, $categoryId = null)
	{
		// Identify which search module should be used
		$searchmodules = qa_load_modules_with('search', 'process_search');

		if (!count($searchmodules))
			qa_fatal_error('No search engine is available');

		$module = reset($searchmodules); // use first one by default

		if (count($searchmodules) > 1) {
			$tryname = qa_opt('search_module'); // use chosen one if it's available

			if (isset($searchmodules[$tryname]))
				$module = $searchmodules[$tryname];
		}

		// Get the results
		$results = $module->process_search($query, $start, $count, $userid, $absoluteurls, $fullcontent, $categoryId ?: null);

		// Work out what additional information (if any) we need to retrieve for the results
		$keypostidgetfull = array();
		$keypostidgettype = array();
		$keypostidgetquestion = array();
		$keypageidgetpage = array();

		foreach ($results as $result) {
			if (isset($result['question_postid']) && !isset($result['question']))
				$keypostidgetfull[$result['question_postid']] = true;

			if (isset($result['match_postid'])) {
				if (!((isset($result['question_postid'])) || (isset($result['question']))))
					$keypostidgetquestion[$result['match_postid']] = true; // we can also get $result['match_type'] from this

				elseif (!isset($result['match_type']))
					$keypostidgettype[$result['match_postid']] = true;
			}

			if (isset($result['page_pageid']) && !isset($result['page']))
				$keypageidgetpage[$result['page_pageid']] = true;
		}

		// Perform the appropriate database queries
		list($postidfull, $postidtype, $postidquestion, $pageidpage) = qa_db_select_with_pending(
			count($keypostidgetfull) ? qa_db_posts_selectspec($userid, array_keys($keypostidgetfull), $fullcontent) : null,
			count($keypostidgettype) ? qa_db_posts_basetype_selectspec(array_keys($keypostidgettype)) : null,
			count($keypostidgetquestion) ? qa_db_posts_to_qs_selectspec($userid, array_keys($keypostidgetquestion), $fullcontent) : null,
			count($keypageidgetpage) ? qa_db_pages_selectspec(null, array_keys($keypageidgetpage)) : null
		);

		// Supplement the results as appropriate
		foreach ($results as $key => $result) {
			if (isset($result['question_postid']) && !isset($result['question']))
				if (@$postidfull[$result['question_postid']]['basetype'] == 'Q')
					$result['question'] = @$postidfull[$result['question_postid']];

			if (isset($result['match_postid'])) {
				if (!(isset($result['question_postid']) || isset($result['question']))) {
					$result['question'] = @$postidquestion[$result['match_postid']];

					if (!isset($result['match_type']))
						$result['match_type'] = @$result['question']['obasetype'];
				} elseif (!isset($result['match_type']))
					$result['match_type'] = @$postidtype[$result['match_postid']];
			}

			if (isset($result['question']) && !isset($result['question_postid']))
				$result['question_postid'] = $result['question']['postid'];

			if (isset($result['page_pageid']) && !isset($result['page']))
				$result['page'] = @$pageidpage[$result['page_pageid']];

			if (!isset($result['title'])) {
				if (isset($result['question']))
					$result['title'] = $result['question']['title'];
				elseif (isset($result['page']))
					$result['title'] = $result['page']['heading'];
			}

			if (!isset($result['url'])) {
				if (isset($result['question']))
					$result['url'] = qa_q_path(
						$result['question']['postid'],
						$result['question']['title'],
						$absoluteurls,
						@$result['match_type'],
						@$result['match_postid']
					);
				elseif (isset($result['page']))
					$result['url'] = qa_path($result['page']['tags'], null, qa_opt('site_url'));
			}

			$results[$key] = $result;
		}

		return $results;
	}

	public function get_category_id($title)
	{
		return qa_db_read_one_value(qa_db_query_sub('SELECT categoryid FROM ^categories where title = #', $title));
	}
}

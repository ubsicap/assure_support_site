<?php

class qa_html_theme_layer extends qa_html_theme_base
{
	//override output_split methods in qa_html_theme_base
	//used to add logo to categories shown in question list
	public function output_split($parts, $class, $outertag = 'span', $innertag = 'span', $extraclass = null)
	{
		if (empty($parts) && strtolower($outertag) != 'td')
			return;
		//check if it is for categories in the list
		$flag = (strlen(@$parts['data']) !== 0) && ($this->compare_str($class, "-where"));
		if ($flag && qa_opt('category_logo_on')) {
			@$parts['prefix'] = ''; //i.e. where meta should be "LOGO category" instead of "in LOGO category"
			$logo = $this->get_logo($parts['data']);
			$this->output(
				'<' . $outertag . ' class="' . $class . (isset($extraclass) ? (' ' . $extraclass) : '') . '">',
				(strlen(@$parts['prefix']) ? ('<' . $innertag . ' class="' . $class . '-pad">' . $parts['prefix'] . '</' . $innertag . '>') : '') .
					($flag ? ('<' . $innertag . ' class="' . $class . '-data">' . $logo . $parts['data'] . '</' . $innertag . '>') : '') .
					(strlen(@$parts['suffix']) ? ('<' . $innertag . ' class="' . $class . '-pad">' . $parts['suffix'] . '</' . $innertag . '>') : ''),
				'</' . $outertag . '>'
			);
		} else
			parent::output_split($parts, $class, $outertag = 'span', $innertag = 'span', $extraclass = null); // call back through to the default function
	}

	//override nav_link methods in qa_html_theme_base
	//used to add logo to categories shown in the side panel
	public function nav_link($navlink, $class)
	{
		//check to see if it is a category in sidepanel
		if ((!strcmp($class, "nav-cat") || !strcmp($class, "browse-cat")) && strlen(@$navlink['note'])) {
			$logo = $this->get_logo($navlink['label']);
			if (isset($navlink['url'])) {
				$this->output(
					$logo .
						'<a href="' . $navlink['url'] . '" class="qa-' . $class . '-link' .
						(@$navlink['selected'] ? (' qa-' . $class . '-selected') : '') .
						(@$navlink['favorited'] ? (' qa-' . $class . '-favorited') : '') .
						'"' . (strlen(@$navlink['popup']) ? (' title="' . $navlink['popup'] . '"') : '') .
						(isset($navlink['target']) ? (' target="' . $navlink['target'] . '"') : '') . '>' . $navlink['label'] .
						'</a>'
				);
			} else {
				$this->output(
					$logo .
						'<span class="qa-' . $class . '-nolink' . (@$navlink['selected'] ? (' qa-' . $class . '-selected') : '') .
						(@$navlink['favorited'] ? (' qa-' . $class . '-favorited') : '') . '"' .
						(strlen(@$navlink['popup']) ? (' title="' . $navlink['popup'] . '"') : '') .
						'>' . $navlink['label'] . '</span>'
				);
			}
			if (strlen(@$navlink['note']))
				$this->output('<span class="qa-' . $class . '-note">' . $navlink['note'] . '</span>');
		} else
			parent::nav_link($navlink, $class);
	}

	// used to enable sorting for categories based on amount of questions
	public function nav_list($navigation, $class, $level = null)
	{
		if (qa_opt('category_sort_on')) {
			// Sort the remaining categories based on the amount of questions 
			usort($navigation, function ($a, $b) {
				// not sort general category 
				if ($a["categoryid"] == null || $b["categoryid"] == null || $a["label"] == "General" || $b["label"] == "General")
					return 0;
				// Access the 'note' property of $navlinkA and $navlinkB for comparison
				$noteA = $this->convertToNumeric($a["note"]);
				$noteB = $this->convertToNumeric($b["note"]);
				return $noteB <=> $noteA;
			});

			$this->output('<ul class="qa-' . $class . '-list' . (isset($level) ? (' qa-' . $class . '-list-' . $level) : '') . '">');
			$index = 0;
			foreach ($navigation as $key => $navlink) {
				$this->set_context('nav_key', $key);
				$this->set_context('nav_index', $index++);
				$this->nav_item($key, $navlink, $class, $level);
			}

			$this->clear_context('nav_key');
			$this->clear_context('nav_index');

			$this->output('</ul>');
		} else
			parent::nav_list($navigation, $class, $level = null);
	}

	//return logo in an image tag
	private function get_logo($category_title)
	{
		$title = strpos($category_title, "</a>") === false ? $this->tolower_replace($category_title) : $this->get_text_from_anchor($category_title);
		$src = qa_opt('category_logo_url_' . $title . '');
		//check if the logo url has been set or not
		if (strlen($src)) {
			$alt = 'icon';
			$style = 'width:22px;height:22px;display:inline-block;margin-right:2px;';
			return '<img class="qa-category-logo" src="' . $src . '" alt="' . $alt . '" style="' . $style . '">';
		} else
			return '';
	}

	//parse image tag to get the title of the category
	private function get_text_from_anchor($data)
	{
		$start_category_index = strpos($data, ">");
		$to_return = $this->tolower_replace(substr($data, $start_category_index + 1, -4));
		return $to_return;
	}

	//format the category title
	private function tolower_replace($str)
	{
		return strtolower(str_replace(" ", "-", $str));
	}

	//check if a str ends with a substr
	private function compare_str($str, $substr)
	{
		return substr_compare($str, $substr, -strlen($substr)) === 0;
	}

	// used to convert values like "2.0k" to 2000 for accurate comparison
	private function convertToNumeric($value) {
		$multiplier = 1;
		$lastChar = strtolower(substr($value, -1));
	
		if ($lastChar === 'k') {
			$multiplier = 1000;
			$value = substr($value, 0, -1);
		}
	
		return intval($value) * $multiplier;
	}
}

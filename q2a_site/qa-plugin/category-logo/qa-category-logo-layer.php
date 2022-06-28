<?php

class qa_html_theme_layer extends qa_html_theme_base
{
    public function output_split($parts, $class, $outertag = 'span', $innertag = 'span', $extraclass = null)
	{
		if (empty($parts) && strtolower($outertag) != 'td')
			return;
       
		$flag = (strlen(@$parts['data']) !== 0) && ($this->compare_str($class, "-where"));
		if($flag && qa_opt('category_logo_on')) {
			$src = qa_opt('category_logo_url_' .$this->get_text_from_anchor($parts['data']).'');
			$alt = 'icon';
			$style = 'width:22px;height:22px;display:inline-block';
			$logo = '<img src="'.$src.'" alt="'.$alt.'" style="'.$style.'">';
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

	private function get_text_from_anchor($data) {
		$closing_tag_index = strrpos($data, "</a>");
		$start_category_index = strpos($data, ">");
		return strtolower(str_replace(" ", "-", substr($data, $start_category_index+1, -4)));
	}

	private function compare_str($str, $substr) {
		return substr_compare($str, $substr, -strlen($substr)) === 0;
	}
}
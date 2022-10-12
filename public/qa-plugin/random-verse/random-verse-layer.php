<?php

class qa_html_theme_layer extends qa_html_theme_base
{
	public function sidebar()
	{
		$sidebar = @$this->content['sidebar'];
		if (qa_opt('random_verse_enabled') && !empty(qa_opt('random_verse_body_text'))) {
				$this->output('<div class="qa-sidebar">');
				$this->output_raw($sidebar);
				$this->output($this->get_random_verse(qa_opt('random_verse_body_text')));
				$this->output('</div>', '');
		} else
			parent::sidebar();		
	}

	private function get_random_verse($verses) {
		$verse_array = preg_split("/\d+\./", $verses, -1, PREG_SPLIT_NO_EMPTY);
		$random_verse = $verse_array[array_rand($verse_array)];
		$chapter_pattern = "/(?:\d\s*)?[A-Z]?[a-z]+\s*\d+(?:[:-]\d+)?(?:\s*-\s*\d+)?(?::\d+|(?:\s*[A-Z]?[a-z]+\s*\d+:\d+))?/";
		$verse = preg_split($chapter_pattern, $random_verse, -1, PREG_SPLIT_NO_EMPTY)[0];
		preg_match($chapter_pattern, $random_verse, $chapter);
		$toReturn = "<div style='margin-bottom: 10px;'><i>$verse</i> </div> <b>$chapter[0]</b>";
	// 	echo '<script type="text/JavaScript"> 
    //  console.log("verse: '.$verse.'");
	//  console.log("chapter: '.$chapter[0].'");
    //  </script>';
		return $toReturn;
	}
}


// end qa_html_theme_layer

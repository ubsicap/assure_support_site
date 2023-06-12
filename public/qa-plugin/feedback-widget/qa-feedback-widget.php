<?php

	class qa_feedback_widget {

		function allow_template($template)
		{
			return true;
		}

		function allow_region($region)
		{
			return true;
		}

		function output_widget($region, $place, $themeobject, $template, $request, $qa_content)
		{
			$themeobject->output("<div style='margin:0px; padding:15px;'>");
				
				$themeobject->output("<div class='give-feedback'>
				<a href='/index.php?qa=feedback' class='give-feedback-link'> <span class='icon-wrapper'><img style='display:inline-block; width:30px; height:30px; margin-right:10px;' src='data:image/png;base64,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' /></span>Give Feedback</a>
				</div>");
				
			$themeobject->output("</div>"); // END qa-feedback-widget
		}
	};


/*
	Omit PHP closing tag to help avoid accidental output
*/

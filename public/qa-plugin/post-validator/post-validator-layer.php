<?php

class qa_html_theme_layer extends qa_html_theme_base
{
	function head_script()
	{
		qa_html_theme_base::head_script();
		// check if plugin is enabled, only load js-css-files if tags are needed: ask and edit question page
		if (qa_opt('post_validator_enabled') && ($this->template == 'ask'|| isset($this->content['form_q_edit'])))
			$this->output(get_val_script('ask'));
		elseif (qa_opt('post_validator_enabled') && ($this->template == 'question' && !isset($this->content['form_q_edit'])))
			$this->output(get_val_script('question'));
	}

	//helper function for head_script
	//get the main scripts needed to display the warnings, $for should either by ask or question, returns the script as a string
	function get_val_script($for)
	{
		$scriptText = 
		'<script type="text/javascript">
			var enabled_name = ' . (qa_opt('post_val_name')=='1') . ';
			var enabled_email = ' . (qa_opt('post_val_email')=='1') . ';
			var enabled_phone = ' . (qa_opt('post_val_phone')=='1') . ';
			var enabled_registration = ' . (qa_opt('post_val_registration')=='1') . ';
			var enabled_ip = ' . (qa_opt('post_val_ip')=='1') . ';
			var enabled_mac = ' . (qa_opt('post_val_mac')=='1') . ';
			var enabled_images = ' . (qa_opt('post_val_images')=='1') . ';
		</script>'; //all the option variables
		$scriptText .= '<script type="text/javascript" src="' . QA_HTML_THEME_LAYER_URLTOROOT . 'utility.js"></script>'; //main functions

		//attach the write script for either a question or a answer/comment
		if($for == "ask") //for questions
			$scriptText .= '<script type="text/javascript" src="' . QA_HTML_THEME_LAYER_URLTOROOT . 'script-ask.js"></script>';
		elseif($for == "question") //for answers and comments
			$scriptText .= '<script type="text/javascript" src="' . QA_HTML_THEME_LAYER_URLTOROOT . 'script-question.js"></script>';
		return $scriptText;
	}


	function head_css()
    {
        qa_html_theme_base::head_css();
		if (qa_opt('post_validator_enabled') && ($this->template == 'ask' || $this->template == 'question')) {
        $this->output(' 
				<style type="text/css">
					.post-validator-error 
					{
						color: #a94442;
						margin-top: 10px;
						margin-bottom: 10px;
						font-size: 16px;
					}
					.qa-form-tall-table > tbody > tr > td.post-validator-error,
					.qa-form-wide-table > tbody > tr > td.post-validator-error 
					{
						border: 1px solid #ebccd1;
						background-color: #f2dede;
						color: #a94442;
						margin-top: 10px;
						margin-bottom: 10px;
						border-radius: 4px;
					}
					.qa-form-wide-table > tbody > tr > td.post-validator-error 
					{
						display: table-cell !important;
					}
				</style>'
			);
		}
    }

	// end qa_html_theme_layer
}

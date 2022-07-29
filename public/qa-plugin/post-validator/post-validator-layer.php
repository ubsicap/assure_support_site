<?php


//helper functions for head_script
//get the main scripts needed to display the warnings, $for should either by ask or question, returns the script as a string
function string_bool_option($opt_name)
{
	return qa_opt($opt_name) ? 'true' : 'false';
}

function get_val_script($for)
{
	$scriptText = 
	'<script type="text/javascript">
		var enabled_name = ' . string_bool_option('post_val_check_name') . ';
		var enabled_email = ' . string_bool_option('post_val_check_email') . ';
		var enabled_phone = ' . string_bool_option('post_val_check_phone') . ';
		var enabled_registration = ' . string_bool_option('post_val_check_registration') . ';
		var enabled_ip = ' . string_bool_option('post_val_check_ip') . ';
		var enabled_mac = ' . string_bool_option('post_val_check_mac') . ';
		var enabled_image = ' . string_bool_option('post_val_check_image') . ';
	</script>'; //all the option variables
	$scriptText .= '<script type="text/javascript" src="' . QA_HTML_THEME_LAYER_URLTOROOT . 'utility.js"></script>'; //main functions

	//attach the write script for either a question or a answer/comment
	if($for == "ask") //for questions
		$scriptText .= '<script type="text/javascript" src="' . QA_HTML_THEME_LAYER_URLTOROOT . 'script-ask.js"></script>';
	elseif($for == "question") //for answers and comments
		$scriptText .= '<script type="text/javascript" src="' . QA_HTML_THEME_LAYER_URLTOROOT . 'script-question.js"></script>';
	elseif($for == "question-edit")
		$scriptText .= '<script type="text/javascript" src="' . QA_HTML_THEME_LAYER_URLTOROOT . 'script-question-edit.js"></script>';
	return $scriptText;
}

class qa_html_theme_layer extends qa_html_theme_base
{
	function head_script() //attach scripts for displaying warnings
	{
		qa_html_theme_base::head_script();
		// check if plugin is enabled, only load js-css-files if tags are needed: ask and edit question page
		if (qa_opt('post_validator_enabled') && ($this->template == 'ask'|| isset($this->content['form_q_edit'])))
			$this->output(get_val_script('ask'));
		elseif (qa_opt('post_validator_enabled') && ($this->template == 'question'))
		{
			$isEditPage = false;
			/*
			if(isset($_GET['state']))
			{
				$isEditPage = true;
				$this->output("<script> console.log(" . $_GET['state'] . ");</script>");
			}
			else
			*/ 
			$this->output("<script> console.log(" . $formtype . ");</script>");
			//case for normal question view
			
			//case for edit answer/comment
			if(!$isEditPage)
				$this->output(get_val_script('question'));
			else //edit answer/comment
				$this->output(get_val_script('question-edit'));
		}
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

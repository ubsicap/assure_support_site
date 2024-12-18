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
		$scriptText .= '<script type="text/javascript" src="' . QA_HTML_THEME_LAYER_URLTOROOT . 'script-question.js?2.0.0"></script>';
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
			if(strlen(qa_get_state()) >= 4 && substr(qa_get_state(), 0, 4) == "edit")
				$isEditPage = true;
			
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

	public function form_label($field, $style, $columns, $prefixed, $suffixed, $colspan)
	{
		if (qa_opt('post_validator_enabled') && qa_opt('post_val_enable_warning') && ($this->template == 'ask' || $this->template == 'question' || isset($this->content['form_q_edit'])) && ((strpos(@$field['label'], "Email me")!== false) || strpos(@$field['label'], "Save silently"))!== false) {
			$extratags = '';
			if ($columns > 1 && (@$field['type'] == 'select-radio' || @$field['rows'] > 1))
				$extratags .= ' style="vertical-align:top;"';
	
			if (isset($colspan))
				$extratags .= ' colspan="' . $colspan . '"';
	
			$this->output('<td class="qa-form-' . $style . '-label"' . $extratags . '>');
			
			$this->output("<label style='padding-bottom: 5px;'>Support.Bible is a public website. Please be careful about what you post, and please refer to our <a href='https://support.bible/best-practices' rel='noopener noreferrer'>Best Practices page</a>.</label>");

			if ($prefixed) {
				$this->output('<label>');
				$this->form_field($field, $style);
			}
	
			$this->output(@$field['label']);
	
			if ($prefixed)
				$this->output('</label>');
	
			if ($suffixed) {
				$this->output('&nbsp;');
				$this->form_field($field, $style);
			}
	
			$this->output('</td>');
		}
		else parent::form_label($field, $style, $columns, $prefixed, $suffixed, $colspan);		
	}

	// end qa_html_theme_layer
}

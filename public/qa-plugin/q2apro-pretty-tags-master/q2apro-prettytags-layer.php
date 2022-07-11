<?php

/*
	Plugin Name: Pretty Tags
	Plugin URI: http://www.q2apro.com/plugins/pretty-tags
	Plugin Description: Provides a pretty autocomplete for tags on the ask page
	Plugin Version: 1.0
	Plugin Date: 2014-10-05
	Plugin Author: q2apro.com
	Plugin Author URI: http://www.q2apro.com
	Plugin Minimum Question2Answer Version: 1.5
	Plugin Update Check URI: http://www.q2apro.com/pluginupdate?id=59
	
	Licence: Copyright © q2apro.com - All rights reserved

*/

class qa_html_theme_layer extends qa_html_theme_base
{
	public function form_data($field, $style, $columns, $showfield, $colspan)
	{
		if (qa_opt('q2apro_prettytags_enabled') && $style == 'tall' && $showfield && array_key_exists('tags', $field) && strpos($field['tags'], 'id="tags"') !== false) {
			if ($showfield || (!empty($field['error'])) || (!empty($field['note']))) {
				$this->output(
					'<td class="qa-form-' . $style . '-data"' . (isset($colspan) ? (' colspan="' . $colspan . '"') : '') . '>'
				);

				if ($showfield)
					$this->form_field($field, $style);

				$this->output(
					'<div class="tag-length"> 
							<p2>Length of current tag: <span>0</span>/25</p2>
							<br>
							<p2>Number of Tags: <span>0</span>/5</p2>
						</div>'
				);

				if (!empty($field['error'])) {
					if (@$field['note_force'])
						$this->form_note($field, $style, $columns);
					$this->form_error($field, $style, $columns);
				} elseif (!empty($field['note']))
					$this->form_note($field, $style, $columns);

				$this->output('</td>');
			}
		} else
			parent::form_data($field, $style, $columns, $showfield, $colspan);
	}

	public function form_field($field, $style)
	{
		if (qa_opt('q2apro_prettytags_enabled') && array_key_exists('tags', $field) && strpos($field['tags'], 'id="tags"') !== false) {
			$this->form_prefix($field, $style);

			$this->output_raw(@$field['html_prefix']);

			switch (@$field['type']) {
				case 'checkbox':
					$this->form_checkbox($field, $style);
					break;

				case 'static':
					$this->form_static($field, $style);
					break;

				case 'password':
					$this->form_password($field, $style);
					break;

				case 'number':
					$this->form_number($field, $style);
					break;

				case 'file':
					$this->form_file($field, $style);
					break;

				case 'select':
					$this->form_select($field, $style);
					break;

				case 'select-radio':
					$this->form_select_radio($field, $style);
					break;

				case 'image':
					$this->form_image($field, $style);
					break;

				case 'custom':
					$this->output_raw(@$field['html']);
					break;

				default:
					if (@$field['type'] == 'textarea' || @$field['rows'] > 1)
						$this->form_text_multi_row($field, $style);
					else {
						if (strpos($field['tags'], 'id="tags"') !== false) {
							$this->output('<div class="tagbox">');
							$this->output('<ul>');
							$this->output('<script type="text/javascript" src="' . QA_HTML_THEME_LAYER_URLTOROOT . 'script.js"></script>');
							$this->output('<li class="new">');
							$this->form_text_single_row($field, $style);
							$this->output('</li>');
							$this->output('</ul>');
							$this->output('</div>');
						} else
							$this->form_text_single_row($field, $style);
					}
					break;
			}

			$this->output_raw(@$field['html_suffix']);

			$this->form_suffix($field, $style);
		} else
			parent::form_field($field, $style);
	}

	public function form_text_single_row($field, $style)
	{
		if (strpos($field['tags'], 'id="tags"') !== false)
			$this->output('<input ' . @$field['tags'] . ' type="text" value="' . @$field['value'] . '" class="qa-form-' . $style . '-text"/>');
		else
			parent::form_text_single_row($field, $style);
	}

	function head_script()
	{
		qa_html_theme_base::head_script();
		// check if plugin is enabled, only load js-css-files if tags are needed: ask and edit question page
		if (qa_opt('q2apro_prettytags_enabled') && ($this->template == 'ask' || isset($this->content['form_q_edit']))) {
			// mobile identifier
			$ismobile = qa_is_mobile_probably() ? 'true' : 'false';
			$this->output('<script type="text/javascript">
					var ismobile = ' . $ismobile . ';
				</script>');

			// load script
			//$this->output('<script type="text/javascript" src="'.QA_HTML_THEME_LAYER_URLTOROOT.'script.js"></script>');

			$this->output(' 
	<style type="text/css">
	/* pretty-tags plugin - tagbox styles */
	.autocomplete {
	position: absolute;
	padding-top: 5px;
	z-index: 1000;
	}
	.autocomplete * {
	padding: 0;
	margin: 0;
	}
	.autocomplete ul:before {
	position: absolute;
	top: -5px;
	left: 10px;
	content: "";
	border-left: 5px solid transparent;
	border-right: 5px solid transparent;
	border-bottom: 5px solid #222;
	}
	.autocomplete ul {
	position: relative;
	padding: 4px;
	background: #f8f8f8;
	color: #0d0d0d;
	min-width: 150px;
	-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
	border-radius: 3px;
	-webkit-box-shadow: rgba(0,0,0, .1) 0px 3px 5px;
	-moz-box-shadow: rgba(0,0,0, .1) 0px 3px 5px;
	box-shadow: rgba(0,0,0, .1) 0px 3px 5px;
	}
	.autocomplete li {
	list-style: none;
	text-align: left;
	padding: 4px 50px 4px 10px;
	cursor: pointer;
	margin-bottom: 1px;
	background: rgb(0 0 0 / 8%);
	}
	.autocomplete li:last-child {
	border-bottom: none;
	margin-bottom: 0;
	}
	.autocomplete li.selected,
	.autocomplete li.over {
	background: #555555;
	color: #fff;  
	}
	.tagbox {
	position: relative;
	overflow: hidden;
	background: #FFF;
	border: 1px solid #AAA;
	-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
	border-radius: 3px;
	-webkit-box-shadow: #fff 0px 1px 0px;
	-moz-box-shadow: #fff 0px 1px 0px;
	box-shadow: #fff 0px 1px 0px;
	}
	.tagbox * {
	margin: 0;
	padding: 0;
	}
	.tagbox.focus{  
	outline: 0;
	}
	.tagbox ul {
	overflow: hidden;
	padding: 10px 10px 6px 10px;
	-webkit-border-radius: 2px;
	-moz-border-radius: 2px;
	border-radius: 2px;
	-webkit-box-shadow: rgba(0,0,0,.07) 0px 2px 4px inset;
	-moz-box-shadow: rgba(0,0,0,.07) 0px 2px 4px inset;
	box-shadow: rgba(0,0,0,.07) 0px 2px 4px inset;
	width: 809.5px;
	}
	.tagbox li {
	position: relative;
	list-style: none;
	float: left;  
	cursor: pointer;
	margin: 0 4px 4px 0;
	}
	.tagbox li.new input {
	/*font-family: Arial, sans-serif;*/
	border: none;
	display: block;
	height: 20px;
	line-height: 20px;
	font-size: 12px;
	background: none;
	}
	.tagbox li.new input:focus {
	outline: none;
	}
	.tagbox li .tag {
	position: relative;
	display: block;
	background: #f8f8f8;
	color: #333;
	border-radius: 20px;
	height: 20px;
	line-height: 20px;
	font-size-adjust: 100%;
	font-size: 12px;
	padding: 1px 30px 2px 12px;
	vertical-align: middle;
	}
	.tagbox li .delete {
	position: absolute;
	/*font-family: Arial, sans-serif;*/
	right: 7px;
	top: 6px;
	color: #fff;
	z-index: 100;
	cursor: pointer;
	color: #222;
	background: #fff url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAMAAACeL25MAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAAZQTFRF////QkJCfEGH6wAAABRJREFUeNpiYGRgACEgBhMgBBBgAABkAAl7vFkJAAAAAElFTkSuQmCC) no-repeat center center;
	width: 12px;
	text-indent: -6000px;
	font-size: 11px;
	line-height: 12px;
	height: 12px;
	text-align: center;
	-webkit-border-radius: 20px;
	-moz-border-radius: 20px;
	border-radius: 20px;
	}
	.tagbox .selected .tag {
	background: #555555;
	}
</style>');
		}
	}


	// end qa_html_theme_layer
}

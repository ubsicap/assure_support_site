<?php

/*
	Plugin Name: Account Reclaim
	Plugin Description: add ^accountreclaim users to mailing in admin
	Plugin Version: 1.0
	Plugin Date: 2022-07-13	
	Licence: Copyright © q2apro.com - All rights reserved

*/

class qa_html_theme_layer extends qa_html_theme_base
{
	// private $special = false;
	// public function form_label($field, $style, $columns, $prefixed, $suffixed, $colspan)
	// {
	// 	if (qa_opt('account_reclaim_enabled') && array_key_exists('tags', $field) && strpos($field['tags'], 'id="option_mailing_enabled"') !== false) {
	// 		$extratags = '';

	// 		if ($columns > 1 && (@$field['type'] == 'select-radio' || @$field['rows'] > 1))
	// 			$extratags .= ' style="vertical-align:top;"';

	// 		if (isset($colspan))
	// 			$extratags .= ' colspan="' . $colspan . '"';

	// 		$this->output('<td class="qa-form-' . $style . '-label"' . $extratags . '>');

	// 		if ($prefixed) {
	// 			$this->output('<label>');
	// 			$this->form_field($field, $style);
	// 		}
	// 		$this->output(@$field['label']);
	// 		if ($prefixed)
	// 			$this->output('</label>');
	// 		$this->output('<label>');
	// 		$this->special = true;
	// 		$this->form_field($field, $style);
	// 		$this->special = false;
	// 		$this->output('Enable mass mailing to users who need account claim');
	// 		$this->output('</label>');
	// 		$this->output('<script type="text/javascript" src="' . QA_HTML_THEME_LAYER_URLTOROOT . 'script.js"></script>');
	// 		if ($suffixed) {
	// 			$this->output('&nbsp;');
	// 			$this->form_field($field, $style);
	// 		}
	// 		$this->output('</td>');
	// 	} else
	// 		parent::form_label($field, $style, $columns, $prefixed, $suffixed, $colspan);
	// }

	// public function form_checkbox($field, $style)
	// {
	// 	if ($this->special)
	// 		$this->output('<input name="account_reclaim_mailing_enabled" id="account_reclaim_mailing_enabled" type="checkbox" value="1" class="qa-form-' . $style . '-checkbox"/>');
	// 	else
	// 		parent::form_checkbox($field, $style);
	// }


	// public function form_button_data($button, $key, $style)
	// {
	// 	if (qa_opt('account_reclaim_enabled') && isset($_POST['option_mailing_enabled'])) {
	// 		$baseclass = 'qa-form-' . $style . '-button qa-form-' . $style . '-button-' . $key;
	// 		$this->output( '<button name="mailtoclaimaccount" id="mailtoclaimaccount" title="' . @$button['popup'] . '" type="submit"' .
	//                 ( isset( $style ) ? ( ' class="' . $baseclass . '"' ) : '' ) . '>' . @$button['label'] . '</button>' );
	// 	} else {
	// 		parent::form_button_data($button, $key, $style);
	// 	}

	// }

	// end qa_html_theme_layer
}

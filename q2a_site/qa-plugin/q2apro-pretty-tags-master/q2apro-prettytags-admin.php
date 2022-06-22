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

	class q2apro_prettytags_admin {

		// option's value is requested but the option has not yet been set
		function option_default($option) {
			switch($option) {
				case 'q2apro_prettytags_enabled':
					return 1; // true
				case 'q2apro_prettytags_anotitle':
					return 1; // true
				case 'q2apro_prettytags_preservenames':
					return 1; // true
				default:
					return null;				
			}
		}
			
		function allow_template($template) {
			return ($template!='admin');
		}       
			
		function admin_form(&$qa_content){                       

			// process the admin form if admin hit Save-Changes-button
			$ok = null;
			if (qa_clicked('q2apro_prettytags_save')) {
				qa_opt('q2apro_prettytags_enabled', (bool)qa_post_text('q2apro_prettytags_enabled')); // empty or 1
				qa_opt('q2apro_prettytags_idalgo', (int)qa_post_text('q2apro_prettytags_idalgo')); // 1, 2 or 3
				qa_opt('q2apro_prettytags_preservenames', (bool)qa_post_text('q2apro_prettytags_preservenames')); // empty or 1
				$ok = qa_lang('admin/options_saved');
			}
			
			// form fields to display frontend for admin
			$fields = array();
			
			$fields[] = array(
				'type' => 'checkbox',
				'label' => qa_lang('q2apro_prettytags_lang/enable_plugin'),
				'tags' => 'name="q2apro_prettytags_enabled"',
				'value' => qa_opt('q2apro_prettytags_enabled'),
			);
			
			// link to q2apro.com
			$fields[] = array(
				'type' => 'static',
				'note' => '<span style="font-size:75%;color:#789;">'.strtr( qa_lang('q2apro_prettytags_lang/contact'), array( 
							'^1' => '<a target="_blank" href="http://www.q2apro.com/plugins/pretty-tags">',
							'^2' => '</a>'
						  )).'</span>',
			);
			
			return array(           
				'ok' => ($ok && !isset($error)) ? $ok : null,
				'fields' => $fields,
				'buttons' => array(
					array(
						'label' => qa_lang('main/save_button'),
						'tags' => 'name="q2apro_prettytags_save"',
					),
				),
			);
		}
	}


/*
	Omit PHP closing tag to help avoid accidental output
*/
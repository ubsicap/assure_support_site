<?php

	class sticky_sidebar_module {

		function allow_template($template)
		{
			return ($template!='admin');
		}
		// Default options
		function option_default($option)
		{
			switch ($option) {
				case 'sticky_sidebar_side_selector':
					return ".qa-sidepanel";
					break;			
				case 'sticky_sidebar_parent_selector':
					return ".qa-main-wrapper";
					break;	
				case 'sticky_sidebar_inner_selector':
					return "";
					break;
				case 'sticky_sidebar_bottom_spacing':
					return 0;
					break;	
				case 'sticky_sidebar_top_spacing':
					return 2;
					break;	
				case 'sticky_sidebar_screen_width':
					return 980;
					break;	
				case 'sticky_sidebar_snowflatfix':
					return true;
					break;	
				case 'sticky_sidebar_status':
					return true;
					break;			
			}
		}

		function admin_form()
		{
			
			$saved=false;
			// When user click on Save button, send input values to DB
			if (qa_clicked('sticky_sidebar_save_button')) {

				$trimchars="=;\"\' \t\r\n";
				qa_opt('sticky_sidebar_side_selector', trim(qa_post_text('sticky_sidebar_side_selector_field'), $trimchars));
				qa_opt('sticky_sidebar_parent_selector', trim(qa_post_text('sticky_sidebar_parent_selector_field'), $trimchars));
				qa_opt('sticky_sidebar_inner_selector', trim(qa_post_text('sticky_sidebar_inner_selector_field'), $trimchars));
				qa_opt('sticky_sidebar_bottom_spacing', trim(qa_post_text('sticky_sidebar_bottom_spacing_field'), $trimchars));
				qa_opt('sticky_sidebar_top_spacing', trim(qa_post_text('sticky_sidebar_top_spacing_field'), $trimchars));
				qa_opt('sticky_sidebar_screen_width', trim(qa_post_text('sticky_sidebar_screen_width_field'), $trimchars));
				qa_opt('sticky_sidebar_snowflatfix', (int)qa_post_text('sticky_sidebar_snowflatfix_field'));
				qa_opt('sticky_sidebar_status', (int)qa_post_text('sticky_sidebar_status_field'));
																
				$saved=true;
			}
			// Draw form fields and show current values from DB
			$form=array(
				'ok' => $saved ? 'SAVED!' : null,

				'fields' => array(
					array(
						'id' => 'sticky_sidebar_side_selector',
						'label' => 'Enter sidebar selector:',
						'value' => qa_html(qa_opt('sticky_sidebar_side_selector')),
						'tags' => 'name="sticky_sidebar_side_selector_field"',
						'note' => 'Default: .qa-sidepanel',
					),				
					array(
						'id' => 'sticky_sidebar_parent_selector',
						'label' => 'Enter parent selector:',
						'value' => qa_html(qa_opt('sticky_sidebar_parent_selector')),
						'tags' => 'name="sticky_sidebar_parent_selector_field"',
						'note' => 'Default: .qa-main-wrapper',
					),
					array(
						'id' => 'sticky_sidebar_inner_selector',
						'label' => 'Enter sidebar inner selector:',
						'value' => qa_html(qa_opt('sticky_sidebar_inner_selector')),
						'tags' => 'name="sticky_sidebar_inner_selector_field"',
						'note' => 'Default: empty (optional but recommended - <a href="https://abouolia.github.io/sticky-sidebar/#usage" target="_blank" rel="nofollow">more here</a>)',
					),
					array(
						'id' => 'sticky_sidebar_bottom_spacing',
						'label' => 'Bottom spacing in PX:',
						'value' => qa_html(qa_opt('sticky_sidebar_bottom_spacing')),
						'tags' => 'name="sticky_sidebar_bottom_spacing_field"',
						'note' => 'Default: 0',
					),
					array(
						'id' => 'sticky_sidebar_top_spacing',
						'label' => 'Top spacing in PX:',
						'value' => qa_html(qa_opt('sticky_sidebar_top_spacing')),
						'tags' => 'name="sticky_sidebar_top_spacing_field"',
						'note' => 'Default: 2',
					),
					array(
						'id' => 'sticky_sidebar_screen_width',
						'label' => 'Load script only if browser is wider than this value:',
						'value' => qa_html(qa_opt('sticky_sidebar_screen_width')),
						'tags' => 'name="sticky_sidebar_screen_width_field"',
						'note' => 'Default: 980. This will prevent loading script on mobile devices. Set 0 to load on all screens.',
					),
					array(
						'id' => 'sticky_sidebar_snowflatfix',					
						'label' => (int)qa_opt('sticky_sidebar_snowflatfix')?'SnowFlat fix: Enabled':'SnowFlat fix: Disabled',
						'type' => 'checkbox',
						'value' => (int)qa_opt('sticky_sidebar_snowflatfix'),
						'tags' => 'name="sticky_sidebar_snowflatfix_field"',
						'note' => 'Default: checked. This is CSS fix for SnowFlat theme for mobile view. If you are not using SnowFlat and something is not working correctly, you may uncheck this.<hr>',
					),
					array(
						'id' => 'sticky_sidebar_status',					
						'label' => (int)qa_opt('sticky_sidebar_status')?'Plugin is: Enabled <em>(Uncheck to disable)</em>':'Plugin is: Disabled <em>(Check to enable)</em>',
						'type' => 'checkbox',
						'value' => (int)qa_opt('sticky_sidebar_status'),
						'tags' => 'name="sticky_sidebar_status_field"',
					),											
				),
				// Draw Save button
				'buttons' => array(
					array(
						'label' => 'Save Changes',
						'tags' => 'name="sticky_sidebar_save_button"',
					),
				),
			);

			return $form;
		}

	}

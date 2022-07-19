<?php

class qa_html_theme_layer extends qa_html_theme_base
{

function footer()
	{

			if ((int)qa_opt('sticky_sidebar_status')) { // If plugin is enabled in admin panel
			// Get values from DB
			$sidebar_selector = qa_opt('sticky_sidebar_side_selector');
			$parent_selector = qa_opt('sticky_sidebar_parent_selector');
			$inner_selector = qa_opt('sticky_sidebar_inner_selector');
			$bottom_spacing = qa_opt('sticky_sidebar_bottom_spacing');
			$top_spacing = qa_opt('sticky_sidebar_top_spacing');
			$screen_width = qa_opt('sticky_sidebar_screen_width');
			
			// Output sticky-sidebar.js and script initialization in footer
			$this->output(
				
				"<script type='text/javascript' src='".QA_HTML_THEME_LAYER_URLTOROOT."sticky-sidebar.js'></script>",
				"<script type='text/javascript'>if ($(window).width() > ".$screen_width.") {var sidebar = new StickySidebar('".$sidebar_selector."', {containerSelector: '".$parent_selector."',innerWrapperSelector: '".$inner_selector."',topSpacing: ".$top_spacing.",bottomSpacing: ".$bottom_spacing."});}</script>"
				
			);			
		}
		qa_html_theme_base::footer();
	}
// Print style in head just to improve performance and prevent repainting on scrolling
function head_custom()
	{
			if ((int)qa_opt('sticky_sidebar_status')) { // If plugin is enabled in admin panel
			$inner_selector = (qa_opt('sticky_sidebar_inner_selector') ? qa_opt('sticky_sidebar_inner_selector') : ".inner-wrapper-sticky");
			$sidebar_selector = qa_opt('sticky_sidebar_side_selector');
			$this->output(
				"<style type='text/css'>".$sidebar_selector."{will-change: min-height;}".$inner_selector."{transform: translate(0, 0);transform: translate3d(0, 0, 0);will-change: position, transform;}</style>"
				
			);		
			if ((int)qa_opt('sticky_sidebar_snowflatfix')) { // If user decide to apply fix for SnowFlat theme
			$this->output(
				"<style type='text/css'>@media (max-width: 979px){.qa-sidepanel {height: 100%!important;position: fixed!important;}.qa-sidepanel.open ".$inner_selector."{position: relative!important;top: unset!important;left: unset!important;bottom: unset!important;width: unset!important;transform: none!important;}}</style>"
				
			);
			}
		}
		qa_html_theme_base::head_custom();
	}
}

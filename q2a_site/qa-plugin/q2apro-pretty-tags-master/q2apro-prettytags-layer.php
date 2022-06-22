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

	class qa_html_theme_layer extends qa_html_theme_base {
		
		function head_script(){
			qa_html_theme_base::head_script();
			// check if plugin is enabled, only load js-css-files if tags are needed: ask and edit question page
			if(qa_opt('q2apro_prettytags_enabled') && ($this->template=='ask' || isset($this->content['form_q_edit']))) {
				// mobile identifier
				$ismobile = qa_is_mobile_probably() ? 'true' : 'false';
				$this->output('<script type="text/javascript">
					var ismobile = '.$ismobile.';
				</script>');
			
				// load script
				$this->output('<script type="text/javascript" src="'.QA_HTML_THEME_LAYER_URLTOROOT.'script.js"></script>');
				
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
	background: #337ab7;
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
	background: red;
	}
</style>');
			}
		}
	} // end qa_html_theme_layer
	

/*
	Omit PHP closing tag to help avoid accidental output
*/
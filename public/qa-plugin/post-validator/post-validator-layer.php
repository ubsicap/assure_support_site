<?php

class qa_html_theme_layer extends qa_html_theme_base
{
	//not sure which is the best

	function head_script()
	{
		qa_html_theme_base::head_script();
		// check if plugin is enabled, only load js-css-files if tags are needed: ask and edit question page
		if (qa_opt('post_validator_enabled') && ($this->template == 'ask' || $this->template == 'question')) {
			// mobile identifier
			$ismobile = qa_is_mobile_probably() ? 'true' : 'false';
			$this->output('<script type="text/javascript">
					var ismobile = ' . $ismobile . ';
				</script>');
			// $this->output('<script type="text/javascript" src="' . QA_PLUGIN_DIR . 'post-validator/script.js"></script>');
			$this->output('<script type="text/javascript" src="' . QA_HTML_THEME_LAYER_URLTOROOT . 'script.js"></script>');
			$this->output(' 
	<style type="text/css">
.post-validator-error {
	color: #a94442;
	margin-top: 10px;
	margin-bottom: 10px;
	font-size: 16px;
}
.qa-form-tall-table > tbody > tr > td.post-validator-error,
.qa-form-wide-table > tbody > tr > td.post-validator-error{
  border: 1px solid #ebccd1;
  background-color: #f2dede;
  color: #a94442;
  margin-top: 10px;
  margin-bottom: 10px;
  border-radius: 4px;
}
.qa-form-wide-table > tbody > tr > td.post-validator-error {
  display: table-cell !important;
}
</style>');
		}
	}

	// end qa_html_theme_layer
}

<?php

class qa_html_theme_layer extends qa_html_theme_base
{

	function page_title_error()
	{
		if (qa_opt('reclaim_account_title_on') && isset($this->content['title']) && strpos($this->content['title'], qa_lang('users/register_title')) !== false) {
			$favorite = @$this->content['favorite'];

			if (isset($favorite))
				$this->output('<form ' . $favorite['form_tags'] . '>');

			$this->output('<h1>');
			$this->favorite();
			$this->title();
			$this->output('</h1>');
			$this->addSubtitle();
			if ($this->template == 'not-found' && qa_opt('donut_show_custom_404_page')) {
				$this->output(donut_include_template('page-not-found.php', false));
			} else if (isset($this->content['error']))
				$this->error(@$this->content['error']);

			if (isset($favorite)) {
				$this->form_hidden_elements(@$favorite['form_hidden']);
				$this->output('</form>');
			}
		} else {
			parent::page_title_error();
		}
	}
	public function addSubtitle()
	{
		$this->output('<h3>');
		$this->output('You can also <a href="https://support.bible/recover-account" style="text-decoration: solid;">reclaim your existing Paratext Support account</a>');
		$this->output('</h3>');
	}
}

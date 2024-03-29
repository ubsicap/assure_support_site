<?php

class qa_html_theme_layer extends qa_html_theme_base {

function head_script() {// insert Javascript into the <head>

        $google_GA4 = qa_opt('google_analytics_GA4');
        $google_domain = qa_opt('google_analytics_domain');
		$is_admin = (qa_get_logged_in_level() == 120) ? true : false;

    if (!empty($google_GA4)) {
        if (!($is_admin && qa_opt('google_analytics_show_for_admin'))) { // the loged in user is not the admin
				$this->content['script'][]=$google_GA4;
			}
		}
  qa_html_theme_base::head_script();
  }
};

<?php

	class qa_search_bar {

		function allow_template($template)
		{
			switch($template)
            {
                //permitted pages
                case "activity":
                case "hot":
                case "qa":
                case "questions":
                case "search":
                case "unanswered":
				case "question":
                    return true;
                default: //any other page is not permitted
                    return false;
            }
		}

		function allow_region($region)
		{
            switch($region)
            {
                //permitted reginos
                case "main":
                case "full":
                    return true;
                default: //any other region is not permitted
                    return false;
            }
		}

		function output_widget($region, $place, $themeobject, $template, $request, $qa_content)
		{
            $this->themeobject = $themeobject;
            $this->themeobject->search();
            //css for the search bar
            $this->themeobject->output('
                <style>
                    .qa-widgets-full.qa-widgets-full-top.col-xs-12 {
                        padding: 15px 0px;
                    }
					button.btn.qa-search-button {
						height: 42px;
					}
                </style>'
            );
            /*
				if(!qa_opt('event_logger_to_database'))
					return;
				$badges = qa_db_query_sub(
					'SELECT event,handle,params, UNIX_TIMESTAMP(datetime) AS datetime FROM ^eventlog WHERE event=$'.(qa_opt('badge_widget_date_max')?' AND DATE_SUB(CURDATE(),INTERVAL '.(int)qa_opt('badge_widget_date_max').' DAY) <= datetime':'').' ORDER BY datetime DESC'.(qa_opt('badge_widget_list_max')?' LIMIT '.(int)qa_opt('badge_widget_list_max'):''),
					'badge_awarded'
				);
				
				$first = true;
				
				while ( ($badge=qa_db_read_one_assoc($badges,true)) !== null ) {
					$params = array();
					
					$paramsa = explode("\t",$badge['params']);
					foreach($paramsa as $param) {
						$parama = explode('=',$param);
						$params[$parama[0]]=$parama[1];
					}
					
					$slug = $params['badge_slug'];
					$typea = qa_get_badge_type_by_slug($slug);
					if(!$typea)
						continue;
					$types = $typea['slug'];
					$typed = $typea['name'];
					
					$badge_name=qa_badge_name($slug);
					if(!qa_opt('badge_'.$slug.'_name')) qa_opt('badge_'.$slug.'_name',$badge_name);
					$var = qa_opt('badge_'.$slug.'_var');
					$name = qa_opt('badge_'.$slug.'_name');
					$desc = qa_badge_desc_replace($slug,$var,false);
					
					$string = '<span class="badge-'.$types.'" title="'.$desc.' ('.$typed.')">'.qa_html($name).'</span> <span class="wibawho">'.$badge['handle'].'</span>';
					
					if($first) {
						$themeobject->output('<h2>'.qa_lang('badges/badge_widget_title').'</h2>');
						$first = false;
					}
					$themeobject->output('<div class="badge-widget-entry">',$string,'</div>');
				}
            */
		}
	};


/*
	Omit PHP closing tag to help avoid accidental output
*/

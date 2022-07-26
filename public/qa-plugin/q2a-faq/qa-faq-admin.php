<?php
class qa_faq_admin
{

    function allow_template($template)
    {
        return ($template != 'admin');
    }

    function option_default($option)
    {
        switch ($option) {
            case 'faq_page_url':
                return 'faq';
            case 'faq_page_title':
                return qa_lang('qa-faq/page_title');
            case 'faq_page_slug':
                return 'FAQ';
            case 'faq_pre_html':
                return qa_lang('qa-faq/page_pre_html');
            case 'faq_post_html':
                return qa_lang('qa-faq/page_post_html');
            case 'faq_notify_text':
                return qa_lang('qa-faq/notify_text');
            case 'faq_css':
                return '.notify-container {
	left: 0;
	right: 0;
	top: 0;
	padding: 0;
	position: fixed;
	width: 100%;
	z-index: 10000;
}
.notify {
	background-color: #F6DF30;
	color: #444444;
	font-weight: bold;
	width: 100%;
	text-align: center;
	font-family: sans-serif;
	font-size: 14px;
	padding: 10px 0;
	position:relative;
}
.notify-close {
	color: #735005;
	cursor: pointer;
	font-size: 18px;
	line-height: 18px;
	padding: 0 3px;
	position: absolute;
	right: 8px;
	text-decoration: none;
	top: 8px;
}
.qa-faq-section {
	margin-bottom: 10px;
}
.qa-faq-section-title {
	border: 1px solid #CCC;
	font-size:125%;
	font-weight:bold;
	cursor:pointer;
}';
            case 'faq_section_0_title':
                return qa_lang('qa-faq/faq_section_0_title');
            case 'faq_section_0':
                return qa_lang('qa-faq/faq_section_0');
            case 'faq_section_1_title':
                return qa_lang('qa-faq/faq_section_1_title');
            case 'faq_section_1':
                return qa_lang('qa-faq/faq_section_1');
            case 'faq_section_2_title':
                return qa_lang('qa-faq/faq_section_2_title');
            case 'faq_section_2':
                return qa_lang('qa-faq/faq_section_2');
            case 'faq_section_3_title':
                return qa_lang('qa-faq/faq_section_3_title');
            case 'faq_section_3':
                return qa_lang('qa-faq/faq_section_3');
            case 'faq_section_4_title':
                return qa_lang('qa-faq/faq_section_4_title');
            case 'faq_section_4':
                return qa_lang('qa-faq/faq_section_4');
            case 'faq_section_5_title':
                return qa_lang('qa-faq/faq_section_5_title');
            case 'faq_section_5':
                return qa_lang('qa-faq/faq_section_5');
            case 'faq_section_6_title':
                return qa_lang('qa-faq/faq_section_6_title');
            case 'faq_section_6':
                return qa_lang('qa-faq/faq_section_6');
            default:
                return null;
        }
    }

    function admin_form(&$qa_content)
    {

        $qa_content['head_lines'][] = '<script>
	function moveFaqSection(idx,odx) {
		var self = [jQuery("#faq_section_"+idx+"_title").val(),jQuery("#faq_section_"+idx).val()];
		var other = [jQuery("#faq_section_"+odx+"_title").val(),jQuery("#faq_section_"+odx).val()];
		
		jQuery("#qa-faq-section-table-"+idx).fadeOut(200,function(){
			jQuery("#faq_section_"+idx+"_title").val(other[0]);
			jQuery("#faq_section_"+idx).val(other[1]);
			jQuery("#qa-faq-section-table-"+idx).fadeIn(200);
		});
		
		jQuery("#qa-faq-section-table-"+odx).fadeOut(200,function(){
			jQuery("#faq_section_"+odx+"_title").val(self[0]);
			jQuery("#faq_section_"+odx).val(self[1]);
			jQuery("#qa-faq-section-table-"+odx).fadeIn(200);
		});
		
	}
</script>';


        //	Process form input
        $ok = null;


        if (qa_clicked('faq_save')) {

            qa_opt('faq_css', qa_post_text('faq_css'));

            qa_opt('faq_page_url', qa_post_text('faq_page_url'));
            qa_opt('faq_page_title', qa_post_text('faq_page_title'));
            qa_opt('faq_page_slug', qa_post_text('faq_page_slug'));
            qa_opt('faq_pre_html', qa_post_text('faq_pre_html'));
            qa_opt('faq_post_html', qa_post_text('faq_post_html'));

            qa_opt('faq_notify_show', (bool)qa_post_text('faq_notify_show'));
            qa_opt('faq_notify_text', qa_post_text('faq_notify_text'));

            $idx = 0;
            while ($idx < (int)qa_post_text('faq_section_number')) {
                qa_opt('faq_section_' . $idx, qa_post_text('faq_section_' . $idx));
                qa_opt('faq_section_' . $idx . '_title', qa_post_text('faq_section_' . $idx . '_title'));
                $idx++;
            }

            $ok = qa_lang('admin/options_saved');
        } else if (qa_clicked('faq_reset')) {
            foreach ($_POST as $i => $v) {
                $def = $this->option_default($i);
                if ($def !== null) qa_opt($i, $def);
            }

            $idx = 0;
            while ($idx < (int)qa_post_text('faq_section_number')) {
                qa_opt('faq_section_' . $idx, $this->option_default('faq_section_' . $idx) ? $this->option_default('faq_section_' . $idx) : '');
                qa_opt('faq_section_' . $idx . '_title', $this->option_default('faq_section_' . $idx . '_title') ? $this->option_default('faq_section_' . $idx . '_title') : '');
                $idx++;
            }

            // reset in case removed

            $idx = 0;
            while ($this->option_default('faq_section_' . $idx)) {
                qa_opt('faq_section_' . $idx, $this->option_default('faq_section_' . $idx));
                qa_opt('faq_section_' . $idx . '_title', $this->option_default('faq_section_' . $idx . '_title'));
                $idx++;
            }
            $ok = qa_lang('admin/options_reset');
        }

        // Create the form for display

        $fields = array();

        $fields[] = array(
            'label' => qa_lang('qa-faq/admin_page_url'),
            'tags' => 'NAME="faq_page_url"',
            'value' => qa_opt('faq_page_url'),
            'note' => qa_lang('qa-faq/admin_page_url_note'),
        );

        $fields[] = array(
            'label' => qa_lang('qa-faq/admin_page_title'),
            'tags' => 'NAME="faq_page_title"',
            'value' => qa_opt('faq_page_title'),
        );

        $fields[] = array(
            'label' => qa_lang('qa-faq/admin_page_slug'),
            'tags' => 'NAME="faq_page_slug"',
            'value' => qa_opt('faq_page_slug'),
            'note' => qa_lang('qa-faq/admin_page_slug_note'),
        );
        $fields[] = array(
            'label' => qa_lang('qa-faq/admin_page_pre_html'),
            'tags' => 'NAME="faq_pre_html"',
            'value' => qa_html(qa_opt('faq_pre_html')),
            'note' => qa_lang('qa-faq/admin_page_html_note'),
        );
        $fields[] = array(
            'label' => qa_lang('qa-faq/admin_page_post_html'),
            'tags' => 'NAME="faq_post_html"',
            'value' => qa_html(qa_opt('faq_post_html')),
            'note' => qa_lang('qa-faq/admin_page_html_note'),
        );

        $fields[] = array(
            'label' => qa_lang('qa-faq/admin_page_css'),
            'tags' => 'NAME="faq_css"',
            'value' => qa_opt('faq_css'),
            'type' => 'textarea',
            'rows' => 20
        );
        $fields[] = array(
            'type' => 'blank',
        );

        $fields[] = array(
            'label' => qa_lang('qa-faq/admin_notify_show'),
            'tags' => 'NAME="faq_notify_show"',
            'value' => qa_opt('faq_notify_show'),
            'type' => 'checkbox',
        );

        $fields[] = array(
            'label' => qa_lang('qa-faq/admin_notify_text'),
            'tags' => 'NAME="faq_notify_text"',
            'value' => qa_html(qa_opt('faq_notify_text')),
            'note' => qa_lang('qa-faq/admin_notify_note'),
        );

        $fields[] = array(
            'type' => 'blank',
        );

        $fields[] = array(
            'type' => 'static',
            'value' => qa_lang('qa-faq/admin_substitutions'),
        );

        $sections = '<div id="qa-faq-sections">';

        $idx = 0;
        while (qa_opt('faq_section_' . $idx)) {
            $sections .= '
<table id="qa-faq-section-table-' . $idx . '" width="100%">
	<tr>
		<td width="30">
			<input type="button" id="faq-up-button-' . $idx . '" value="-" style="width:30px" title="move section up" onclick="moveFaqSection(' . $idx . ',' . ($idx - 1) . ')"' . ($idx == 0 ? ' disabled' : '') . '>
			<br/><br/>
			<input type="button" id="faq-down-button-' . $idx . '" value="+" style="width:30px" title="move section down" onclick="moveFaqSection(' . $idx . ',' . ($idx + 1) . ')"' . (!qa_opt('faq_section_' . ($idx + 1)) ? ' disabled' : '') . '>
		</td>
		<td>
			<b>Faq section ' . ($idx + 1) . ' title:</b><br/>
			<input class="qa-form-tall-text" type="text" value="' . qa_html(qa_opt('faq_section_' . $idx . '_title')) . '" id="faq_section_' . $idx . '_title" name="faq_section_' . $idx . '_title"><br/><br/>
			<b>Faq section ' . ($idx + 1) . ' content:</b><br/>
			<textarea class="qa-form-tall-text" rows="10" id="faq_section_' . $idx . '" name="faq_section_' . $idx . '">' . qa_html(qa_opt('faq_section_' . $idx)) . '</textarea>
		</td>
	</tr>
</table>
<hr/>';

            $idx++;
        }
        $sections .= '</div>';

        $fields[] = array(
            'type' => 'static',
            'value' => $sections
        );


        $fields[] = array(
            'type' => 'static',
            'value' => '
<script>
	var next_faq_section = ' . $idx . '; 
	function addFaqSection(){
		jQuery("#qa-faq-sections").append(\'<table id="qa-faq-section-table-\'+next_faq_section+\'" width="100%"><tr><td width="30"><input type="button" id="faq-up-button-\'+next_faq_section+\'" value="-" style="width:30px" title="move section up" onclick="moveFaqSection(\'+next_faq_section+\',\'+(next_faq_section-1)+\')"\'+(next_faq_section==0?\' disabled\':\'\')+\'><br/><br/><input type="button" id="faq-down-button-\'+next_faq_section+\'" value="+" style="width:30px" title="move section down" onclick="moveFaqSection(\'+next_faq_section+\',\'+(next_faq_section+1)+\')" disabled></td><td>Faq section \'+(next_faq_section+1)+\' title<br/><input type="text" id="faq_section_\'+next_faq_section+\'_title" name="faq_section_\'+next_faq_section+\'_title"><br/><br/>Faq section \'+(next_faq_section+1)+\' content<br/><textarea class="qa-form-tall-text" rows="10" id="faq_section_\'+next_faq_section+\'" name="faq_section_\'+next_faq_section+\'"></textarea></td></tr></table><hr/>\');
		
		jQuery("#faq-down-button-"+(next_faq_section-1)).removeAttr("disabled");
		next_faq_section++;
		jQuery("input[name=faq_section_number]").val(next_faq_section);
	}
</script>
<input type="button" value="add section" onclick="addFaqSection()">'
        );

        $form['hidden']['faq_section_number'] = $idx;

        return array(
            'ok' => ($ok && !isset($error)) ? $ok : null,

            'fields' => $fields,

            'hidden' => array(
                'faq_section_number' => $idx
            ),

            'buttons' => array(
                array(
                    'label' => qa_lang_html('main/save_button'),
                    'tags' => 'NAME="faq_save"',
                ),
                array(
                    'label' => qa_lang_html('admin/reset_options_button'),
                    'tags' => 'NAME="faq_reset"',
                ),
            ),
        );
    }
}

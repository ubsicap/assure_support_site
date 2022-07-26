
<?php

/*
    English language file for FAQ Plugin
*/

return array(
    'page_title' => 'Frequently Asked Questions',
    'page_pre_html' => 'The following is basic information about our forum.  Please read this before posting questions or answers if you are unfamiliar with this sort of forum.  Click on any question to show or hide the answer.',
    'page_post_html' => '<br/>If there is anything lacking, please use the <a href="^qa_path(feedback)">feedback form</a>.',
    'notify_text' => 'First time here?  Checkout the ^faq!',

    'section_0_title' => '',
    'section_0' => '',

    'admin_page_url' => 'FAQ page url',
    'admin_page_url_note' => '(set this in admin/pages as well!)',
    'admin_page_title' => 'FAQ page title',
    'admin_page_slug' => 'FAQ page slug',
    'admin_page_slug_note' => 'short title used in notification',
    'admin_page_pre_html' => 'Text at the top of the FAQ page (html allowed)',
    'admin_page_post_html' => 'Text at the bottom of the FAQ page (html allowed)',
    'admin_page_html_note' => 'filters allowed as for sections (below)',
    'admin_page_css' => 'FAQ custom CSS',
    'admin_notify_show' => 'Show notification for new visitors',
    'admin_notify_text' => 'Notification text for new visitors',
    'admin_notify_note' => '^faq is substituted by a link to the faq page, displaying the FAQ slug set above: ' . '<a href="' . qa_path_html(qa_opt('faq_page_url')) . '">' . qa_opt('faq_page_slug') . '</a>',
    'admin_substitutions' => 'in the fields below, the following values will be substituted:<br/><br/>
	^site_title - the site title (' . qa_opt('site_title') . ')<br/>
	^site_url - the site url (' . qa_opt('site_url') . ')<br/>
	^if_logged_in=`text` - text to show if user is logged in.<br/>
	^if_not_logged_in=`text` - text to show if user is not logged in.<br/>
	^login - login url (for links)<br/>
	^register - register url (for links)<br/>
	^profile_url - current user\'s profile page url (for links)<br/>
	^handle - current user\'s username<br/>
	^qa_path(url) - the relative path for given url<br/>
	^qa_opt(option) - any q2a option that exists in the database<br/>
	^pointstable - a preformatted table of points awarded by activity<br/>
	^privilegestable - a preformatted table of points required for each privilege',
);

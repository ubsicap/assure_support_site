
<?php

/*
    English language file for FAQ Plugin
*/

return array(
    'page_title' => 'Frequently Asked Questions',
    'page_pre_html' => 'The following is basic information about our forum.  Please read this before posting questions or answers if you are unfamiliar with this sort of forum.  Click on any question to show or hide the answer.',
    'page_post_html' => '<br/>If there is anything lacking, please use the <a href="^qa_path(feedback)">feedback form</a>.',
    'notify_text' => 'First time here?  Checkout the ^faq!',

    'faq_section_0_title' => 'What kinds of questions can I ask here? ',
    'faq_section_0' => 'This is a community surrounding a variety of Bible translation tools, such as <a href"https://paratext.org/">Paratext</a> and <a href="https://pubassist.paratext.org/">Publishing Assistant</a>. If you have questions about Bible translation tools, this is the right place to ask.<br><br>Before you ask, please make sure to search for a similar question. You can search for questions by their <a href"^site_url/categories">category</a> <a href"^site_url/tags">tag</a> or <a href"^site_url/search">title</a>.',
    'faq_section_1_title' => 'What kinds of questions should be avoided? ',
    'faq_section_1' => 'Please avoid asking questions that are too subjective or argumentative. This is not a place to debate politics, theology, or philosophy.',
    'faq_section_2_title' => 'What should I avoid in my answers? ',
    'faq_section_2' => '^site_title is a <strong>question and answer</strong> support forum for Bible translation software. It is <strong>not</strong> a discussion group or a place to debate politics, theology, or philosophy.<br/><br/>For brief discussion, or to thank someone for their answer, please post comments, not answers. To post a comment, click the "comment" button below an answer.<img src="../assets/comment.png" alt="Comment Button">',
    'faq_section_3_title' => 'How does point system work? ',
    'faq_section_3' => 'When a question or answer is voted up, the user who posted it will gain points. These points serve as a rough measure of the community trust in that person.<br/><br/>For example, if you ask an interesting question or useful answer, it  will likely be voted up. On the other hand if the question is poorly-worded or the answer is misleading - it will likely be voted down. Each up vote on a question will generate <strong>^qa_opt(points_per_q_voted_up) points</strong>, whereas each vote against will subtract <strong>^qa_opt(points_per_q_voted_down) points</strong>. The following table lists points gained per activity:<br/><br/>^pointstable',
    'faq_section_4_title' => 'How to change my picture (gravatar), and what is gravatar? ',
    'faq_section_4' => 'The picture that appears in user profiles is called a <strong>gravatar</strong>, which means <strong>globally recognized avatar</strong>.<br/><br/>Here is how it works: You upload your picture (or your favorite alter ego image) to the website <a href="http://gravatar.com"><strong>gravatar.com</strong></a> from where we later retrieve your image using a cryptographic key based on your email address.<br/><br/>This way all the websites you trust can show your image next to your posts and your email address remains private.<br/><br/>To personalize your account with an image, register at <a href="http://gravatar.com"><strong>gravatar.com</strong></a> (just be sure to use the same email address that you used to register with us).<br><br>If you do not wish to use gravatar, you may change your profile picture through the <a href="^site_url/account">"edit profile"</a> page.',
    'faq_section_5_title' => 'Still have questions? ',
    'faq_section_5' => 'Please <a href="^qa_path(ask, array("cat" => "' . '1' . '"))">ask your question</a> and help make our community better! ',

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

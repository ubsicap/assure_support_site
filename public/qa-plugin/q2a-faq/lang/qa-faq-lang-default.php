
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

    'faq_section_0_title' => 'What kinds of questions can I ask here? ',
    'faq_section_0' => 'Most importantly, questions should be <strong>relevant to our community</strong>.  Before you ask, please make sure to search for a similar question. You can search for questions by their title or tags.',
    'faq_section_1_title' => 'What kinds of questions should be avoided? ',
    'faq_section_1' => 'Please avoid asking questions that are not related to our community, too subjective or argumentative.',
    'faq_section_2_title' => 'What should I avoid in my answers? ',
    'faq_section_2' => '^site_title is a <strong>question and answer</strong> site - it is <strong>not</strong> a discussion group. Please avoid holding debates in your answers as they tend to dilute the quality of  the forum. <br/><br/>For brief discussion, or to thank someone for their answer, please post comments, not answers.',
    'faq_section_3_title' => 'Who moderates this community? ',
    'faq_section_3' => 'The short answer is: <strong>you.</strong>  This website is moderated by the users.  Points system allows users to earn rights to perform a variety of moderation tasks.',
    'faq_section_4_title' => 'How does point system work? ',
    'faq_section_4' => 'When a question or answer is voted up, the user who posted it will gain points. These points serve as a rough measure of the community trust in that person. Various moderation tasks are gradually assigned to the users based on those points. <br/><br/>For example, if you ask an interesting question or useful answer, it  will likely be voted up. On the other hand if the question is poorly-worded or the answer is misleading - it will likely be voted down. Each up vote on a question will generate <strong>^qa_opt(points_per_q_voted_up) points</strong>, whereas each vote against will subtract <strong>^qa_opt(points_per_q_voted_down) points</strong>. The following table lists points gained per activity:<br/><br/>^pointstable<br/><br/>The following table lists point requirements for each type of moderation task. <br/><br/>^privilegestable',
    'faq_section_5_title' => 'How to change my picture (gravatar), and what is gravatar? ',
    'faq_section_5' => 'The picture that appears in user profiles is called a <strong>gravatar</strong>, which means <strong>globally recognized avatar</strong>.<br/><br/>Here is how it works: You upload your picture (or your favorite alter ego image) to the website <a href="http://gravatar.com"><strong>gravatar.com</strong></a> from where we later retrieve your image using a cryptographic key based on your email address.<br/><br/>This way all the websites you trust can show your image next to your posts and your email address remains private.<br/><br/>Please <strong>personalize your account with an image</strong> - just register at <a href="http://gravatar.com"><strong>gravatar.com</strong></a> (just please be sure to use the same email address that you used to register with us). The default gray image is generated automatically.',
    'faq_section_6_title' => 'Still have questions? ',
    'faq_section_6' => 'Please <a href="^qa_path(ask)">ask your question</a> and help make our community better! ',

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

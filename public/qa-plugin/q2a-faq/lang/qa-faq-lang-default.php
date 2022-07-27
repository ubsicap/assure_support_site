
<?php

/*
    English language file for FAQ Plugin
*/

const ASSETS_DIR = './qa-plugin/q2a-faq/assets/';

return array(
    // Option values
    'page_title' => 'Frequently Asked Questions',
    'page_pre_html' => 'The following is basic information about our forum. Please read this before posting questions or answers if you are unfamiliar with this sort of forum. Click on any question to show or hide the answer.',
    'page_post_html' => '<br>Didn\'t find the answer you were looking for? Please <a href="^qa_path(ask)?cat=">ask your question</a> and help make our community better!', // If you have a "General" category, put it's numerical ID after `?cat=`
    'notify_text' => 'First time here? Checkout the ^faq!',


    // Default FAQ titles and contents
    'faq_section_0_title' => 'What is this forum?',
    'faq_section_0' => '^site_title is a community support forum for a variety of Bible translation tools, such as <a href"https://paratext.org/">Paratext</a> and <a href="https://pubassist.paratext.org/">Publishing Assistant</a>. It is a place to ask questions and get support for Bible translation projects.',

    'faq_section_1_title' => 'What kinds of questions can I ask here?',
    'faq_section_1' => 'Any have questions about Bible translation tools are appropriate here. Are you starting a new project and need to know what software to use? Do you need help resolving an issue in your translation software? Not sure how to accomplish a translation task? This forum is the correct place to ask these kinds of questions.<br><br>Before you ask, please make sure to search for a similar question. You can search for questions by their <a href"^site_url/categories">category</a> <a href"^site_url/tags">tag</a> or <a href"^site_url/search">title</a>.',

    'faq_section_2_title' => 'What kinds of questions should be avoided?',
    'faq_section_2' => 'Please avoid asking questions that are too subjective or argumentative. This is not a place to debate politics, theology, or philosophy.',

    'faq_section_3_title' => 'What should I avoid in my answers?',
    'faq_section_3' => '^site_title is a <strong>question and answer</strong> support forum for Bible translation software. It is <strong>not</strong> a discussion group or a place to debate politics, theology, or philosophy.<br/><br/>For brief discussion, or to thank someone for their answer, please post comments, not answers. To post a comment, click the "comment" button below an answer.<img src="' . ASSETS_DIR . 'comment.png" alt="Comment Button">',

    'faq_section_4_title' => 'What do the points mean? How does point system work?',
    'faq_section_4' => 'Points are a measure of a user\'s reputation and helpfulness on this forum. When a question or answer is voted up, the user who posted it will gain points.<br/><br/>For example, if you ask an interesting question or useful answer, it will likely be voted up. On the other hand if the question is poorly-worded or the answer is misleading - it will likely be voted down. Each up vote on a question will generate <strong>^qa_opt(points_per_q_voted_up) points</strong>, whereas each vote against will subtract <strong>^qa_opt(points_per_q_voted_down) points</strong>. The following table lists points gained per activity:<br/><br/>^pointstable',

    'faq_section_5_title' => 'How can I change my profile picture?',
    'faq_section_5' => 'The picture that appears in user profiles is called a <strong>gravatar</strong>, which means <strong>globally recognized avatar</strong>.Here is how it works: You upload your picture (or your favorite alter ego image) to the website <a href="http://gravatar.com"><strong>gravatar.com</strong></a> from where we later retrieve your image using a cryptographic key based on your email address.<br/><br/>This way all the websites you trust can show your image next to your posts and your email address remains private.<br/><br/>To personalize your account with an image, register at <a href="http://gravatar.com"><strong>gravatar.com</strong></a> (just be sure to use the same email address that you used to register with us).<br><br>If you do not wish to use gravatar, you may change your profile picture through the <a href="^site_url/account">"edit profile"</a> page.',

    'faq_section_6_title' => 'Why is everyone named "anon123456"?', 
    'faq_section_6' => '^site_title moved from support.paratext.org. Since the previous form was accessible by authorized users only and this forum is publicly available, all existing accounts were anonymized. That means that all usernames, email addresses, phone numbers, real names, and any other identifying information has been replaced with anonymous aliases or removed altogether.<br><br>We offer the ability to <a href="^site_url/recover-account">reclaim your account</a> to users of the previous site, or users can choose to <a href="^site_url/register">create a new account</a>.',

    'faq_section_7_title' => 'I am trying to reach support.paratext.org. What happened to my account?', 
    'faq_section_7' => 'The Paratext Supporter Forum has moved here, <a href="^site_url" target="_blank" rel="noopener noreferrer">^site_title</a>, as a part of the process of unifying the communities who use various Bible translation softwares. All data, such as users, posts, images, and roles, from the original support forum has been migrated to the new forum. Since this forum is publicly viewable to anyone without needing a verified account, all identifiable information (such as names, locations, and addresses) has been anonymized.<br><br>If you wish to retain all of your posts and data, you may <a href="^site_url/recover-account" rel="noopener noreferrer">reclaim your previous account</a>. Otherwise, you may <a href="^site_url/register" rel="noopener noreferrer">create a new account</a>, instead. Please note that if you choose to create a new account and use the same email address that was associated with your support.paratext.org account, you will <b>not</b> be able to reclaim any of your data.',

    
    // Option display names and notes
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

<?php

/*
	Question2Answer (c) Gideon Greenspan

	http://www.question2answer.org/


	File: qa-plugin/akismet-spam-filter/qa-akismet.php
	Version: See define()s at top of qa-include/qa-base.php
	Description: Akismet Spam Filter
*/

	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../');
		exit;
	}

	class qa_akismet {

		function admin_form(&$qa_content)
		{
			$saved = false;

			if (qa_clicked('akismet_save_button'))
			{
				qa_opt('akismet_api_key', qa_post_text('akismet_api_key_field'));

				qa_opt('akismet_user_points_moderation_on', (int)qa_post_text('akismet_user_points_moderation_on_field'));
				qa_opt('akismet_user_points', (int)qa_post_text('akismet_user_points_field'));

				qa_opt('akismet_post_links_moderation_on', (int)qa_post_text('akismet_post_links_moderation_on_field'));
				qa_opt('akismet_links_user_points', (int)qa_post_text('akismet_links_user_points_field'));
				qa_opt('akismet_mod_anon_posts', (int)qa_post_text('akismet_mod_anon_posts_field'));

				$check_key = $this->check_akismet_key();

				$saved=true;
			}

			$check_key = $this->check_akismet_key();

			qa_set_display_rules($qa_content, array(
				'akismet_user_points_display' => 'akismet_user_points_moderation_on_field',
				'akismet_user_points_links_display' => 'akismet_post_links_moderation_on_field',
				'akismet_mod_anon_posts_display' => 'akismet_post_links_moderation_on_field',
			));

			return array(
				'ok' => $saved ? 'Akismet settings saved' : null,

				'fields' => array(
					array(
						'label' => 'Akismet API Key:',
						'value' => qa_opt('akismet_api_key'),
						'tags' => 'NAME="akismet_api_key_field"',
						'error' => "$check_key",
					),
					array(
						'label' => '(Optional) Only enable the Akismet Spam Filter when :',
						'type' => 'checkbox',
						'value' => qa_opt('akismet_user_points_moderation_on'),
						'tags' => 'NAME="akismet_user_points_moderation_on_field" ID="akismet_user_points_moderation_on_field"',
					),
					array(
						'id' => 'akismet_user_points_display',
						'label' => 'The user has less than',
						'suffix' => 'points',
						'type' => 'number',
						'value' => (int)qa_opt('akismet_user_points'),
						'tags' => 'NAME="akismet_user_points_field"',
					),
					array(
						'label' => '(Optional) Moderate questions/answers/comments that contain links when :',
						'type' => 'checkbox',
						'value' => qa_opt('akismet_post_links_moderation_on'),
						'tags' => 'NAME="akismet_post_links_moderation_on_field" ID="akismet_post_links_moderation_on_field"',
					),
					array(
						'id' => 'akismet_user_points_links_display',
						'label' => 'The user has less than',
						'suffix' => 'points',
						'type' => 'number',
						'value' => (int)qa_opt('akismet_links_user_points'),
						'tags' => 'NAME="akismet_links_user_points_field" ID="akismet_links_user_points_field"',
					),
					array(
						'id' => 'akismet_mod_anon_posts_display',
						'label' => 'The user is not logged in (Anonymous)',
						'type' => 'checkbox',
						'value' => (int)qa_opt('akismet_mod_anon_posts'),
						'tags' => 'NAME="akismet_mod_anon_posts_field" ID=NAME="akismet_mod_anon_posts_field"',
					),
				),

				'buttons' => array(
					array(
						'label' => 'Save Changes',
						'tags' => 'NAME="akismet_save_button"',
					),
				),
			);
		}

		var $directory;
		function load_module($directory, $urltoroot)
		{
			$this->directory=$directory;
		}

		function check_akismet_key()
		{
			require_once $this->directory.'akismet.class.php';

			$akismet_key = qa_opt('akismet_api_key');
			$akismet = new akismet($akismet_key);

			if ($akismet->valid_key())
			{
				$valid = '<font color="green">Akismet API key is valid - spam protection is enabled.</font>';
			}
			else if (!strlen(trim($akismet_key)))
			{
				$valid = 'To use Akismet spam filter, you need to <a href="https://akismet.com/signup/" target="_blank">sign up for an API key</a>.';
			}
			else
			{
				$valid = 'Invalid Akismet API key! - spam protection is disabled.';
			}

			return $valid;
		}


		function filter_question(&$question, &$errors, $oldquestion)
		{
			require_once $this->directory.'akismet.class.php';

			$akismet_key = qa_opt('akismet_api_key');
			$akismet = new akismet($akismet_key);

			// combine question title and content for spam processing
			$combined_content = $question['title'].' '.$question['text'];

			$content = $question['content'];
			$text = $question['text'];

			$check_content = array(
				'comment_author_email' => $question['email'],
				'comment_content'      => $combined_content,
			);

			$userid = qa_get_logged_in_userid();
			$user_points = qa_get_logged_in_points();

			$akismet_user_mod_on = qa_opt('akismet_user_points_moderation_on');
			$akismet_min_user_points = qa_opt('akismet_user_points');

			$akismet_post_links_on = qa_opt('akismet_post_links_moderation_on');
			$akismet_post_links_points = qa_opt('akismet_links_user_points');

			$akismet_mod_anon_posts = qa_opt('akismet_mod_anon_posts');

			if(!$akismet->error)
			{
				if ($akismet_user_mod_on && $user_points < $akismet_min_user_points)
				{
					// User does not have the required points - use the akismet spam filter
					if ($akismet->is_spam($check_content))
					{
						$question['queued']=true;
					}
				}

				else if ($akismet_user_mod_on && $user_points > $akismet_min_user_points)
				{
					// User has more points than required - do not use the akismet spam filter
				}

				else
				{
					// Optional option is not enabled - enable akismet spam filter for everyone
					if ($akismet->is_spam($check_content))
					{
						$question['queued']=true;
					}
				}
			}

			if (isset($akismet_post_links_on))
			{
				if ($user_points < $akismet_post_links_points)
				{
					if (stristr($content,'href='))
					{
						$question['queued']=true;
					}
					if (stristr($text,'href='))
					{
						$question['queued']=true;
					}
				}
			}

			if (!isset($userid) && isset($akismet_mod_anon_posts))
			{
				if (stristr($content,'href='))
				{
					$question['queued']=true;
				}
				if (stristr($text,'href='))
				{
					$question['queued']=true;
				}
			}
		}


		function filter_answer(&$answer, &$errors, $question, $oldanswer)
		{
			require_once $this->directory.'akismet.class.php';

			$akismet_key = qa_opt('akismet_api_key');
			$akismet = new akismet($akismet_key);

			$content = $answer['content'];
			$text = $answer['text'];

			$check_content = array(
				'comment_content' => $answer['text'],
			);

			$userid = qa_get_logged_in_userid();
			$user_points = qa_get_logged_in_points();

			$akismet_user_mod_on = qa_opt('akismet_user_points_moderation_on');
			$akismet_min_user_points = qa_opt('akismet_user_points');

			$akismet_post_links_on = qa_opt('akismet_post_links_moderation_on');
			$akismet_post_links_points = qa_opt('akismet_links_user_points');

			$akismet_mod_anon_posts = qa_opt('akismet_mod_anon_posts');

			if(!$akismet->error)
			{
				if ($akismet_user_mod_on && $user_points < $akismet_min_user_points)
				{
					// User does not have the required points - use the akismet spam filter
					if ($akismet->is_spam($check_content))
					{
						$answer['queued']=true;
					}
				}

				else if ($akismet_user_mod_on && $user_points > $akismet_min_user_points)
				{
					// User has more points than required - do not use the akismet spam filter
				}

				else
				{
					// Optional option is not enabled - enable akismet spam filter for everyone
					if ($akismet->is_spam($check_content))
					{
						$answer['queued']=true;
					}
				}
			}

			if (isset($akismet_post_links_on))
			{
				if ($user_points < $akismet_post_links_points)
				{
					if (stristr($content,'href='))
					{
						$answer['queued']=true;
					}
					if (stristr($text,'href='))
					{
						$answer['queued']=true;
					}
				}
			}

			if (!isset($userid) && isset($akismet_mod_anon_posts))
			{
				if (stristr($content,'href='))
				{
					$answer['queued']=true;
				}
				if (stristr($text,'href='))
				{
					$answer['queued']=true;
				}
			}
		}


		function filter_comment(&$comment, &$errors, $question, $parent, $oldcomment)
		{
			require_once $this->directory.'akismet.class.php';

			$akismet_key = qa_opt('akismet_api_key');
			$akismet = new akismet($akismet_key);

			$content = $comment['content'];
			$text = $comment['text'];

			$check_content = array(
				'comment_content' => $comment['text'],
			);

			$userid = qa_get_logged_in_userid();
			$user_points = qa_get_logged_in_points();

			$akismet_user_mod_on = qa_opt('akismet_user_points_moderation_on');
			$akismet_min_user_points = qa_opt('akismet_user_points');

			$akismet_post_links_on = qa_opt('akismet_post_links_moderation_on');
			$akismet_post_links_points = qa_opt('akismet_links_user_points');

			$akismet_mod_anon_posts = qa_opt('akismet_mod_anon_posts');

			if(!$akismet->error)
			{
				if ($akismet_user_mod_on && $user_points < $akismet_min_user_points)
				{
					// User does not have the required points - use the akismet spam filter
					if ($akismet->is_spam($check_content))
					{
						$comment['queued']=true;
					}
				}

				else if ($akismet_user_mod_on && $user_points > $akismet_min_user_points)
				{
					// User has more points than required - do not use the akismet spam filter
				}

				else
				{
					// Optional option is not enabled - enable akismet spam filter for everyone
					if ($akismet->is_spam($check_content))
					{
						$comment['queued']=true;
					}
				}
			}

			if (isset($akismet_post_links_on))
			{
				if ($user_points < $akismet_post_links_points)
				{
					if (stristr($content,'href='))
					{
						$comment['queued']=true;
					}
					if (stristr($text,'href='))
					{
						$comment['queued']=true;
					}
				}
			}

			if (!isset($userid) && isset($akismet_mod_anon_posts))
			{
				if (stristr($content,'href='))
				{
					$comment['queued']=true;
				}
				if (stristr($text,'href='))
				{
					$comment['queued']=true;
				}
			}
		}
	}


/*
	Omit PHP closing tag to help avoid accidental output
*/
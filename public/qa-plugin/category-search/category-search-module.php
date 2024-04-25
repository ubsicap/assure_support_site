<?php
class category_search_module
{

	public function process_search($query, $start, $count, $userid, $absoluteurls, $fullcontent, $categoryId = null)
	{
		require_once QA_INCLUDE_DIR . 'db/selects.php';
		require_once QA_INCLUDE_DIR . 'util/string.php';
		
		$words = qa_string_to_words($query);

		$questions = qa_db_select_with_pending(
			$this->qa_db_search_posts_selectspec($userid, $words, $words, $words, $words, trim($query), $start, $fullcontent, $count, $categoryId)
		);

		$results = array();

		foreach ($questions as $question) {
			qa_search_set_max_match($question, $type, $postid); // to link straight to best part

			$results[] = array(
				'question' => $question,
				'match_type' => $type,
				'match_postid' => $postid,
			);
		}

		return $results;
	}

	function qa_db_search_posts_selectspec($voteuserid, $titlewords, $contentwords, $tagwords, $handlewords, $handle, $start, $full = false, $count = null, $categoryId = null)
	{
		$count = isset($count) ? min($count, QA_DB_RETRIEVE_QS_AS) : QA_DB_RETRIEVE_QS_AS;

		// add LOG(postid)/1000000 here to ensure ordering is deterministic even if several posts have same score
		// The score also gives a bonus for hot questions, where the bonus scales linearly with hotness. The hottest
		// question gets a bonus equivalent to a matching unique tag, and the least hot question gets zero bonus.

		$selectspec = qa_db_posts_basic_selectspec($voteuserid, $full);

		$selectspec['columns'][] = 'score';
		$selectspec['columns'][] = 'matchparts';
		$selectspec['source'] .= " JOIN (SELECT questionid, SUM(score)+2*(LOG(#)*(MAX(^posts.hotness)-(SELECT MIN(hotness) FROM ^posts WHERE type='Q'))/((SELECT MAX(hotness) FROM ^posts WHERE type='Q')-(SELECT MIN(hotness) FROM ^posts WHERE type='Q')))+LOG(questionid)/1000000 AS score, GROUP_CONCAT(CONCAT_WS(':', matchposttype, matchpostid, ROUND(score,3))) AS matchparts FROM (";
		$selectspec['sortdesc'] = 'score';
		array_push($selectspec['arguments'], QA_IGNORED_WORDS_FREQ);

		$selectparts = 0;

		if (!empty($titlewords)) {
			// At the indexing stage, duplicate words in title are ignored, so this doesn't count multiple appearances.

			$selectspec['source'] .= ($selectparts++ ? " UNION ALL " : "") .
				"(SELECT postid AS questionid, LOG(#/titlecount) AS score, 'Q' AS matchposttype, postid AS matchpostid FROM ^titlewords JOIN ^words ON ^titlewords.wordid=^words.wordid WHERE word IN ($) AND titlecount<#)";

			array_push($selectspec['arguments'], QA_IGNORED_WORDS_FREQ, $titlewords, QA_IGNORED_WORDS_FREQ);
		}

		if (!empty($contentwords)) {
			// (1-1/(1+count)) weights words in content based on their frequency: If a word appears once in content
			// it's equivalent to 1/2 an appearance in the title (ignoring the contentcount/titlecount factor).
			// If it appears an infinite number of times, it's equivalent to one appearance in the title.
			// This will discourage keyword stuffing while still giving some weight to multiple appearances.
			// On top of that, answer matches are worth half a question match, and comment/note matches half again.

			$selectspec['source'] .= ($selectparts++ ? " UNION ALL " : "") .
				"(SELECT questionid, (1-1/(1+count))*LOG(#/contentcount)*(CASE ^contentwords.type WHEN 'Q' THEN 1.0 WHEN 'A' THEN 0.5 ELSE 0.25 END) AS score, ^contentwords.type AS matchposttype, ^contentwords.postid AS matchpostid FROM ^contentwords JOIN ^words ON ^contentwords.wordid=^words.wordid WHERE word IN ($) AND contentcount<#)";

			array_push($selectspec['arguments'], QA_IGNORED_WORDS_FREQ, $contentwords, QA_IGNORED_WORDS_FREQ);
		}

		if (!empty($tagwords)) {
			// Appearances in the tag words count like 2 appearances in the title (ignoring the tagcount/titlecount factor).
			// This is because tags express explicit semantic intent, whereas titles do not necessarily.

			$selectspec['source'] .= ($selectparts++ ? " UNION ALL " : "") .
				"(SELECT postid AS questionid, 2*LOG(#/tagwordcount) AS score, 'Q' AS matchposttype, postid AS matchpostid FROM ^tagwords JOIN ^words ON ^tagwords.wordid=^words.wordid WHERE word IN ($) AND tagwordcount<#)";

			array_push($selectspec['arguments'], QA_IGNORED_WORDS_FREQ, $tagwords, QA_IGNORED_WORDS_FREQ);
		}

		if (!empty($handlewords)) {
			if (QA_FINAL_EXTERNAL_USERS) {
				require_once QA_INCLUDE_DIR . 'app/users.php';

				$userids = qa_get_userids_from_public($handlewords);

				if (count($userids)) {
					$selectspec['source'] .= ($selectparts++ ? " UNION ALL " : "") .
						"(SELECT postid AS questionid, LOG(#/qposts) AS score, 'Q' AS matchposttype, postid AS matchpostid FROM ^posts JOIN ^userpoints ON ^posts.userid=^userpoints.userid WHERE ^posts.userid IN ($) AND type='Q')";

					array_push($selectspec['arguments'], QA_IGNORED_WORDS_FREQ, $userids);
				}
			} else {
				$selectspec['source'] .= ($selectparts++ ? " UNION ALL " : "") .
					"(SELECT postid AS questionid, LOG(#/qposts) AS score, 'Q' AS matchposttype, postid AS matchpostid FROM ^posts JOIN ^users ON ^posts.userid=^users.userid JOIN ^userpoints ON ^userpoints.userid=^users.userid WHERE handle IN ($) AND type='Q')";

				array_push($selectspec['arguments'], QA_IGNORED_WORDS_FREQ, $handlewords);
			}
		}

		if (strlen($handle)) { // to allow searching for multi-word usernames (only works if search query contains full username and nothing else)
			if (QA_FINAL_EXTERNAL_USERS) {
				$userids = qa_get_userids_from_public(array($handle));

				if (count($userids)) {
					$selectspec['source'] .= ($selectparts++ ? " UNION ALL " : "") .
						"(SELECT postid AS questionid, LOG(#/qposts) AS score, 'Q' AS matchposttype, postid AS matchpostid FROM ^posts JOIN ^userpoints ON ^posts.userid=^userpoints.userid WHERE ^posts.userid=$ AND type='Q')";

					array_push($selectspec['arguments'], QA_IGNORED_WORDS_FREQ, reset($userids));
				}
			} else {
				$selectspec['source'] .= ($selectparts++ ? " UNION ALL " : "") .
					"(SELECT postid AS questionid, LOG(#/qposts) AS score, 'Q' AS matchposttype, postid AS matchpostid FROM ^posts JOIN ^users ON ^posts.userid=^users.userid JOIN ^userpoints ON ^userpoints.userid=^users.userid WHERE handle=$ AND type='Q')";

				array_push($selectspec['arguments'], QA_IGNORED_WORDS_FREQ, $handle);
			}
		}

		if ($selectparts == 0) {
			$selectspec['source'] .= '(SELECT NULL as questionid, 0 AS score, NULL AS matchposttype, NULL AS matchpostid FROM ^posts WHERE postid IS NULL)';
		}

		$selectspec['source'] .= ") x LEFT JOIN ^posts ON ^posts.postid=questionid GROUP BY questionid ORDER BY score DESC) y ON ^posts.postid=y.questionid";

		if ($categoryId !== null) {
			$selectspec['source'] .= ' WHERE ^posts.categoryid = #';
			array_push($selectspec['arguments'], $categoryId);
		}

		$selectspec['source'] .= " LIMIT #, #";
		array_push($selectspec['arguments'], $start, $count);

		return $selectspec;
	}
}

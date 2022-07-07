<?php
/*
	Question2Answer Tagging Tools plugin
	Copyright (C) 2011 Scott Vivian
	License: https://www.gnu.org/licenses/gpl.html
*/

// Tagging Tools helper functions
class qa_tt_helper
{
	// converts a config string of synonyms to an array [[A,B],[C,D]]
	public static function synonyms_to_array($config)
	{
		$synonyms = [];
		$lines = explode("\n", $config);

		foreach ($lines as $line) {
			$items = explode(',', $line);
			if (!isset($items[1]))
				$items[1] = '';

			// ignore tags being replaced by the same one
			if ($items[0] === $items[1])
				continue;

			$synonyms[] = [
				'from' => trim($items[0]),
				'to' => trim($items[1]),
			];
		}

		return $synonyms;
	}

	// converts each tag to a synonym if it exists
	public static function convert_tags($tags, $synonyms)
	{
		$newtags = [];

		foreach ($tags as $tag) {
			$tag = strtolower($tag);
			foreach ($synonyms as $syn) {
				if ($tag === $syn['from']) {
					$tag = $syn['to'];
					break; // don't need to check more synonyms
				}
			}

			if ($tag != '')
				$newtags[] = $tag;
		}

		return $newtags;
	}
}

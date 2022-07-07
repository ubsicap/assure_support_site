<?php

/*
	This file is part of PUPI - Maximum Tag Length, a Question2Answer plugin
	that limits the amount of characters allowed per each question tag

	Copyright (C) 2014 Gabriel Zanetti <http://github.com/pupi1985>

	PUPI - Maximum Tag Length is free software: you can redistribute it and/or
	modify it under the terms of the GNU General Public License as published
	by the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	PUPI - Maximum Tag Length is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General
	Public License for more details.

	You should have received a copy of the GNU General Public License along
	with PUPI - Maximum Tag Length. If not, see <http://www.gnu.org/licenses/>.
*/

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../');
	exit;
}

class PUPI_MTL_Module_Filter {

	public function filter_question(&$question, &$errors, $oldquestion) {
		require_once 'pupi_mtl_module_admin.php';
		if (qa_opt(PUPI_MTL_Module_Admin::SETTING_PLUGIN_ENABLED)) {
			if (isset($question['tags'])) {
				foreach ($question['tags'] as $tag) {
					$maximumTagLength = qa_opt(PUPI_MTL_Module_Admin::SETTING_MAXIMUM_TAG_LENGTH);
					if (strlen($tag) > $maximumTagLength) {
						$error = PUPI_MTL_Module_Admin::translate(
							PUPI_MTL_Module_Admin::LANG_ID_QUESTION_PAGE_ERROR_LENGTH,
							$maximumTagLength
						);
						$error .= PUPI_MTL_Module_Admin::translate(
							PUPI_MTL_Module_Admin::LANG_ID_QUESTION_PAGE_ERROR_TAG,
							sprintf('"%s"', $tag)
						);
						$errors['tags'] = $error;
						break;
					}
				}
			}
		}
	}

}
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

class PUPI_MTL_Module_Admin {

	const BUTTON_SAVE = 'pupi_mtl_button_save';

	// Settings
	const SETTING_PLUGIN_ENABLED = 'pupi_mtl_plugin_enabled';
	const SETTING_MAXIMUM_TAG_LENGTH = 'pupi_mtl_maximum_tag_length';

	// Default setting values
	const SETTING_PLUGIN_ENABLED_DEFAULT = false;
	const SETTING_MAXIMUM_TAG_LENGTH_DEFAULT = 15;

	// Language keys
	const LANG_ID_ADMIN_SETTINGS_SAVED = 'admin_settings_saved';
	const LANG_ID_ADMIN_SAVE_SETTINGS_BUTTON = 'admin_save_settings_button';
	const LANG_ID_ADMIN_PLUGIN_ENABLED = 'admin_plugin_enabled';
	const LANG_ID_ADMIN_MAXIMUM_TAG_LENGTH = 'admin_maximum_tag_length';
	const LANG_ID_QUESTION_PAGE_ERROR_LENGTH = 'question_page_error_length';
	const LANG_ID_QUESTION_PAGE_ERROR_TAG = 'question_page_error_tag';

	public static function translate($id, $parameter = null) {
		$fullIdentifier = 'pupi_mtl/' . $id;
		return isset($parameter) ?  qa_lang_html_sub($fullIdentifier, $parameter) : qa_lang_html($fullIdentifier);
	}

	public function option_default($option) {
		switch ($option) {
			case self::SETTING_PLUGIN_ENABLED:
				return self::SETTING_PLUGIN_ENABLED_DEFAULT;
			case self::SETTING_MAXIMUM_TAG_LENGTH:
				return self::SETTING_MAXIMUM_TAG_LENGTH_DEFAULT;
			default:
		}
	}

	public function admin_form(&$qa_content) {
		$ok = null;
		if (qa_clicked(self::BUTTON_SAVE)) {
			$this->savePluginEnabledSetting();
			$this->saveMaximumTagLengthSetting();
			$ok = $this->translate(self::LANG_ID_ADMIN_SETTINGS_SAVED);
		}
		$fields = array_merge(
			$this->getPluginEnabledField(),
			$this->getMaximumTagLengthField()
		);
		return array(
			'ok' => $ok,
			'style' => 'wide',
			'fields' => $fields,
			'buttons' => $this->getButtons(),
		);
	}

	private function getButtons() {
		return array(
			'save' => array(
				'tags' => 'name="' . self::BUTTON_SAVE . '"',
				'label' => $this->translate(self::LANG_ID_ADMIN_SAVE_SETTINGS_BUTTON),
			),
		);
	}

	// All field returning methods

	private function getPluginEnabledField() {
		return array(array(
			'label' => $this->translate(self::LANG_ID_ADMIN_PLUGIN_ENABLED),
			'style' => 'tall',
			'tags' => 'name="' . self::SETTING_PLUGIN_ENABLED . '"',
			'type' => 'checkbox',
			'value' => (bool) qa_opt(self::SETTING_PLUGIN_ENABLED),
		));
	}

	private function getMaximumTagLengthField() {
		return array(array(
			'label' => $this->translate(self::LANG_ID_ADMIN_MAXIMUM_TAG_LENGTH),
			'style' => 'tall',
			'tags' => 'name="' . self::SETTING_MAXIMUM_TAG_LENGTH . '"',
			'type' => 'number',
			'value' => (int) qa_opt(self::SETTING_MAXIMUM_TAG_LENGTH),
		));
	}

	// All save methods

	private function savePluginEnabledSetting() {
		qa_opt(self::SETTING_PLUGIN_ENABLED, (bool) qa_post_text(self::SETTING_PLUGIN_ENABLED));
	}

	private function saveMaximumTagLengthSetting() {
		$minValue = max(1, (int) qa_post_text(self::SETTING_MAXIMUM_TAG_LENGTH));
		qa_opt(self::SETTING_MAXIMUM_TAG_LENGTH, $minValue);
	}

}

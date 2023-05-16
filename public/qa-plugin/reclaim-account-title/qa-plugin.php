<?php

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
  header('Location: ../../');
  exit;
}

qa_register_plugin_layer('reclaim-account-title-layer.php', 'Recalim Account Title');
qa_register_plugin_module('module', 'reclaim-account-title-admin.php', 'reclaim_account_title_admin', 'Recalim Account Title Admin');
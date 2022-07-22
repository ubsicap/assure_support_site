<?php

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
    header('Location: ../../');
    exit;
}

qa_register_plugin_module('process', 'processes/PUPI_SRS_Admin.php', 'PUPI_SRS_Admin', 'PUPI_SRS Admin');
qa_register_plugin_module('process', 'processes/PUPI_SRS_Setup.php', 'PUPI_SRS_Setup', 'PUPI_SRS Setup');

qa_register_plugin_module('filter', 'PUPI_SRS_RegistrationFilter.php', 'PUPI_SRS_RegistrationFilter', 'PUPI_SRS Registration Filter');

qa_register_plugin_module('event', 'events/PUPI_SRS_EventListener.php', 'PUPI_SRS_EventListener', 'PUPI_SRS Event Listener');

qa_register_plugin_phrases('lang/pupi_srs_*.php', 'pupi_srs');

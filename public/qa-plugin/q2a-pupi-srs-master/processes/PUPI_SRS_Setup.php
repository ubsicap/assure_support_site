<?php

class PUPI_SRS_Setup
{
    const LATEST_DB_VERSION = 2;
    const SETTING_PLUGIN_DB_VERSION = 'pupi_srs_plugin_db_version';

    public function init_queries($tableslc)
    {
        require_once QA_INCLUDE_DIR . 'app/users.php';
        require_once QA_INCLUDE_DIR . 'db/maxima.php';

        $queries = array();

        $currenDbVersion = $this->getDatabaseVersionFromDb();
        if ($currenDbVersion < self::LATEST_DB_VERSION) {
            $currenDbVersion++;
            switch ($currenDbVersion) {
                case 0:  // Initial value where nothing should already be installed
                case 1:
                    if (!in_array(qa_db_add_table_prefix('pupi_srs_standarized_emails'), $tableslc)) {
                        $queries[] =
                            'CREATE TABLE IF NOT EXISTS `^pupi_srs_standarized_emails` (' .
                            '   `email` VARCHAR(' . QA_DB_MAX_EMAIL_LENGTH . ') NOT NULL, ' .
                            '   `multiple_attempts` BIT NOT NULL, ' .
                            '   PRIMARY KEY (`email`)' .
                            ') ENGINE = InnoDB CHARSET = utf8';
                    }
                    $queries[] = $this->getUpdateVersionQuery(1);
                case 2:
                    $queries[] = 'DELETE FROM `^pupi_srs_standarized_emails`';
                    $queries[] = 'ALTER TABLE `^pupi_srs_standarized_emails` DROP COLUMN `multiple_attempts`';
                    $queries[] = 'ALTER TABLE `^pupi_srs_standarized_emails` ADD COLUMN `registered_email` VARCHAR(' . QA_DB_MAX_EMAIL_LENGTH . ') NOT NULL AFTER `email`';
                    $queries[] = 'ALTER TABLE `^pupi_srs_standarized_emails` ADD COLUMN `last_registration_attempt` DATE NOT NULL AFTER `registered_email`';

                    $queries[] = $this->getUpdateVersionQuery(2);
            }
        }

        return $queries;
    }

    private function getUpdateVersionQuery(int $version)
    {
        return qa_db_apply_sub(
            'INSERT INTO `^options` (`title`, `content`) ' .
            'VALUES (#, #) ' .
            'ON DUPLICATE KEY UPDATE `content` = VALUES(`content`)',
            array(self::SETTING_PLUGIN_DB_VERSION, $version)
        );
    }

    public function getDatabaseVersionFromDb()
    {
        return (int)qa_db_read_one_value(qa_db_query_sub(
            'SELECT `content` FROM `^options` ' .
            'WHERE `title` = $',
            self::SETTING_PLUGIN_DB_VERSION
        ), true);
    }
}

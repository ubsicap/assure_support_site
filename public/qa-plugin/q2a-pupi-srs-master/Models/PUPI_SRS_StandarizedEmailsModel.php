<?php

class PUPI_SRS_StandarizedEmailsModel
{
    public function getStandarizedEmailRecordFromDatabase(string $email)
    {
        $sql =
            'SELECT `email`, `registered_email` FROM `^pupi_srs_standarized_emails` ' .
            'WHERE `email` = $';

        return qa_db_read_one_assoc(qa_db_query_sub($sql, $email), true);
    }

    public function insertUpdateEmailInDatabase(string $email, string $registeredEmail)
    {
        $sql =
            'INSERT INTO `^pupi_srs_standarized_emails`(`email`, `registered_email`, `last_registration_attempt`) ' .
            'VALUES($, $, CURRENT_DATE()) ' .
            'ON DUPLICATE KEY UPDATE `registered_email` = VALUES(`registered_email`), `last_registration_attempt` = VALUES(`last_registration_attempt`)';

        qa_db_query_sub($sql, $email, $registeredEmail);
    }
}

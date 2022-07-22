<?php

abstract class PUPI_SRS_AbstractOnlineUserValidator extends PUPI_SRS_AbstractValidator
{
    /**
     * Return whether the given email and IP address are likely to belong to a SPAM user.
     * If any error is thrown, return an exception with the message to log in the server error log.
     *
     * @throws Exception
     */
    public abstract function isSpamUser(string $email, string $ip): bool;
}

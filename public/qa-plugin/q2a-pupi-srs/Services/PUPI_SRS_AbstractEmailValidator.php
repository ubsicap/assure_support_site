<?php

abstract class PUPI_SRS_AbstractEmailValidator extends PUPI_SRS_AbstractValidator
{
    /**
     * Return whether the email can be processed.
     */
    public abstract function canProcessEmail(string $email, string $user, string $domain): string;

    /**
     * Return whether the email is sintactically valid.
     */
    public abstract function isValid(string $email, string $user, string $domain): bool;

    /**
     * Return the email in a standard format.
     */
    public abstract function getStandarizedEmail(string $email, string $user, string $domain): string;

    protected function removeAllAfterPlusSign(string $string)
    {
        $plusSignPos = strpos($string, '+');
        if ($plusSignPos !== false) {
            $string = substr($string, 0, $plusSignPos);
        }

        return $string;
    }
}

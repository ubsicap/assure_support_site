<?php

class PUPI_SRS_Gmail extends PUPI_SRS_AbstractEmailValidator
{
    public function __construct()
    {
        $this->name = 'Gmail';
    }

    public function canProcessEmail(string $email, string $user, string $domain): string
    {
        return $domain === 'gmail.com' || $domain === 'googlemail.com';
    }

    public function isValid(string $email, string $user, string $domain): bool
    {
        $regex = '/^[a-z0-9](([.+])?[a-z0-9]){5,}$/i';

        // Don't allow 5+ dots in username
        if (substr_count($user, '.') >= 5) {
            return false;
        }

        return preg_match($regex, $user, $matches) === 1;
    }

    public function getStandarizedEmail(string $email, string $user, string $domain): string
    {
        $user = str_replace('.', '', $user);

        $user = $this->removeAllAfterPlusSign($user);

        return $user . '@' . $domain;
    }
}

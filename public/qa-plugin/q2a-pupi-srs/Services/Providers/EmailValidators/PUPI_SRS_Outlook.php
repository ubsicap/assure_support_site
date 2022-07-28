<?php

class PUPI_SRS_Outlook extends PUPI_SRS_AbstractEmailValidator
{
    public function __construct()
    {
        $this->name = 'Outlook';
    }

    public function canProcessEmail(string $email, string $user, string $domain): string
    {
        return $domain === 'outlook.com';
    }

    public function isValid(string $email, string $user, string $domain): bool
    {
        // Rules for Outlook are a bit unclear
        return true;
    }

    public function getStandarizedEmail(string $email, string $user, string $domain): string
    {
        $user = $this->removeAllAfterPlusSign($user);

        return $user . '@' . $domain;
    }
}

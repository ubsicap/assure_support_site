<?php

abstract class PUPI_SRS_AbstractValidator
{
    /**
     * Name of the service that helps in identification and logging.
     */
    protected $name;

    /**
     * Whether this service will be rate limited or not.
     */
    protected $rateLimitEnabled = false;

    /**
     * Setting to store the date and time of the last check that was made.
     */
    protected $lastCheckSetting = null;

    /**
     * Setting to store the count of checks made before the limit is reached.
     */
    protected $checkCountSetting = null;

    /**
     * Setting to store the check limit before stopping the checks.
     */
    protected $checkLimitSetting = null;

    /**
     * Value used as the default for the $checkLimitSetting setting.
     */
    protected $checkLimitDefaultValue = null;

    public static function isSameDay(string $currentDateTime, string $lastCheckDateTime): bool
    {
        return date('Ymd', strtotime($currentDateTime)) === date('Ymd', strtotime($lastCheckDateTime));
    }

    public static function isSameMonth(string $currentDateTime, string $lastCheckDateTime): bool
    {
        return date('Ym', strtotime($currentDateTime)) === date('Ym', strtotime($lastCheckDateTime));
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Return the amount of remaining checks until the count gets reset.
     */
    public function getRemainingChecks(): int
    {
        return $this->rateLimitEnabled ? $this->getLimitCheck() - (int)qa_opt($this->checkCountSetting) : PHP_INT_MAX;
    }

    /**
     * Increment checks done resetting the value, if necessary.
     */
    public function incrementChecksDone()
    {
        if (!$this->rateLimitEnabled) {
            return;
        }

        $this->updateLastCheckedSetting();

        $checkCount = (int)qa_opt($this->checkCountSetting);
        qa_opt($this->checkCountSetting, $checkCount + 1);
    }

    /**
     * Remove all pending checks.
     */
    public function removeAllPendingChecks()
    {
        if (!$this->rateLimitEnabled) {
            return;
        }

        $this->updateLastCheckedSetting();

        qa_opt($this->checkCountSetting, $this->getLimitCheck());
    }


    /**
     * Return the checkLimitSetting value applying the default value, if necessary.
     */
    protected function getLimitCheck(): int
    {
        $limit = qa_opt($this->checkLimitSetting);

        if ($limit === '') {
            qa_opt($this->checkLimitSetting, $this->checkLimitDefaultValue);

            return $this->checkLimitDefaultValue;
        }

        return (int)$limit;
    }

    /**
     * Return whether to reset the check counter or not.
     */
    public function shouldResetCheckCount(): bool
    {
        return false;
    }

    /**
     * Reset check count.
     */
    public function resetCheckCount()
    {
        qa_opt($this->checkCountSetting, 0);
    }

    /**
     * Return all fields needed to display the admin form. Array keys should be included.
     */
    public function getAdminFormFields(): array
    {
        return [];
    }

    /**
     * Executed when saving the admin form.
     */
    public function saveAdminForm()
    {
    }

    public function isRateLimitEnabled(): bool
    {
        return $this->rateLimitEnabled;
    }

    private function updateLastCheckedSetting()
    {
        $currentDateTime = date('Y-m-d H:i:s', qa_opt('db_time'));
        qa_opt($this->lastCheckSetting, $currentDateTime);
    }
}

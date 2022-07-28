<?php

class PUPI_SRS_IsTempMail extends PUPI_SRS_AbstractOnlineUserValidator
{
    const ISTEMPMAIL_KEY_SETTING = 'pupi_srs_istempmail_key';

    /**
     * API key for the IsTempMail service.
     *
     * @var string
     */
    private $key;

    public function __construct()
    {
        $this->name = 'IsTempMail';
        $this->key = qa_opt(self::ISTEMPMAIL_KEY_SETTING);

        $this->rateLimitEnabled = true;

        $this->lastCheckSetting = 'pupi_srs_istempmail_last_check';
        $this->checkCountSetting = 'pupi_srs_istempmail_check_count';
        $this->checkLimitSetting = 'pupi_srs_istempmail_check_limit';

        $this->checkLimitDefaultValue = 200;
    }

    /**
     * @throws Exception
     */
    public function isSpamUser(string $email, string $ip): bool
    {
        if (empty($this->key)) {
            throw new Exception('Invalid API key');
        }

        $url = sprintf('https://www.istempmail.com/api/check/%s/%s', urlencode($this->key), urlencode($email));
        $dataString = qa_retrieve_url($url);

        $data = json_decode($dataString, true);

        if (empty($data)) {
            throw new Exception('Error fetching data from server');
        }

        if (isset($data['error_description']) && strpos($data['error_description'], 'upgrade your account') !== false) {
            $this->removeAllPendingChecks();

            return false;
        }

        if (!isset($data['blocked'])) {
            throw new Exception('Unknown error. Data returned: ' . $dataString);
        }

        if ($data['blocked']) {
            return true;
        }

        if (isset($data['unresolvable']) && $data['unresolvable']) {
            return true;
        }

        return false;
    }

    public function shouldResetCheckCount(): bool
    {
        $lastCheckDateTime = qa_opt($this->lastCheckSetting);

        if (empty($lastCheckDateTime)) {
            return true;
        }

        return !PUPI_SRS_AbstractOnlineUserValidator::isSameMonth(date('Y-m-d H:i:s', qa_opt('db_time')), $lastCheckDateTime);
    }

    public function getAdminFormFields(): array
    {
        return [
            self::ISTEMPMAIL_KEY_SETTING => [
                'label' => 'API key:', // Intentionally untranslated to make Providers be a single file
                'value' => qa_html(qa_opt(self::ISTEMPMAIL_KEY_SETTING)),
                'tags' => sprintf('name="%s"', self::ISTEMPMAIL_KEY_SETTING),
            ],
            $this->checkLimitSetting => [
                'label' => 'API monthly requests limit:', // Intentionally untranslated to make Providers be a single file
                'value' => qa_html($this->getLimitCheck()),
                'tags' => sprintf('name="%s"', $this->checkLimitSetting),
                'note' => 'IsTempMail has a monthly limit of 200 API requests',
            ],
        ];
    }

    public function saveAdminForm()
    {
        qa_opt(self::ISTEMPMAIL_KEY_SETTING, qa_post_text(self::ISTEMPMAIL_KEY_SETTING));
        qa_opt($this->checkLimitSetting, qa_post_text($this->checkLimitSetting));
    }
}

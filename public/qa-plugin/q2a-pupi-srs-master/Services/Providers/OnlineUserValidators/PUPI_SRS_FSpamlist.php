<?php

class PUPI_SRS_FSpamlist extends PUPI_SRS_AbstractOnlineUserValidator
{
    const FSPAMLIST_KEY_SETTING = 'pupi_srs_fspamlist_key';

    /**
     * API key for the FSpamlist service.
     *
     * @var string
     */
    private $key;

    public function __construct()
    {
        $this->name = 'FSpamlist';
        $this->key = qa_opt(self::FSPAMLIST_KEY_SETTING);

        $this->rateLimitEnabled = true;

        $this->lastCheckSetting = 'pupi_srs_fspamlist_last_check';
        $this->checkCountSetting = 'pupi_srs_fspamlist_check_count';
        $this->checkLimitSetting = 'pupi_srs_fspamlist_check_limit';

        $this->checkLimitDefaultValue = 10000;
    }

    /**
     * @throws Exception
     */
    public function isSpamUser(string $email, string $ip): bool
    {
        if (empty($this->key)) {
            throw new Exception('Invalid API key');
        }

        $url = sprintf('https://fspamlist.com/api.php?spammer=%s,,%s&key=%s&json', urlencode($email), urlencode($ip), urlencode($this->key));
        $dataString = qa_retrieve_url($url);

        $data = json_decode($dataString, true);

        if (empty($data)) {
            throw new Exception('Error fetching data from server');
        }

        if (!is_array($data)) {
            $this->throwUnknownErrorException($data);
        }

        foreach ($data as $spamTest) {
            if (!isset($spamTest['isspammer'])) {
                $this->throwUnknownErrorException($data);
            }

            if ($spamTest['isspammer'] === 'true') {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws Exception
     */
    private function throwUnknownErrorException($data)
    {
        throw new Exception('Unknown error. Data returned: ' . $data);
    }

    public function shouldResetCheckCount(): bool
    {
        $lastCheckDateTime = qa_opt($this->lastCheckSetting);

        if (empty($lastCheckDateTime)) {
            return true;
        }

        return !PUPI_SRS_AbstractOnlineUserValidator::isSameDay(date('Y-m-d H:i:s', qa_opt('db_time')), $lastCheckDateTime);
    }

    public function getAdminFormFields(): array
    {
        return [
            self::FSPAMLIST_KEY_SETTING => [
                'label' => 'API key:', // Intentionally untranslated to make Providers be a single file
                'value' => qa_html(qa_opt(self::FSPAMLIST_KEY_SETTING)),
                'tags' => sprintf('name="%s"', self::FSPAMLIST_KEY_SETTING),
            ],
            $this->checkLimitSetting => [
                'label' => 'API daily requests limit:', // Intentionally untranslated to make Providers be a single file
                'value' => qa_html($this->getLimitCheck()),
                'tags' => sprintf('name="%s"', $this->checkLimitSetting),
                'note' => 'FSpamlist has a daily limit of 10,000 API requests',
            ],
        ];
    }

    public function saveAdminForm()
    {
        qa_opt(self::FSPAMLIST_KEY_SETTING, qa_post_text(self::FSPAMLIST_KEY_SETTING));
        qa_opt($this->checkLimitSetting, qa_post_text($this->checkLimitSetting));
    }
}

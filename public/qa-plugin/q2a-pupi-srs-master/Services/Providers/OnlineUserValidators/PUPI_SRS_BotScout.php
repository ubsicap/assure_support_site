<?php

class PUPI_SRS_BotScout extends PUPI_SRS_AbstractOnlineUserValidator
{
    const BOTSCOUT_KEY_SETTING = 'pupi_srs_botscout_key';

    /**
     * API key for the BotScout service.
     *
     * @var string
     */
    private $key;

    public function __construct()
    {
        $this->name = 'BotScout';
        $this->key = qa_opt(self::BOTSCOUT_KEY_SETTING);

        $this->rateLimitEnabled = true;

        $this->lastCheckSetting = 'pupi_srs_botscout_last_check';
        $this->checkCountSetting = 'pupi_srs_botscout_check_count';
        $this->checkLimitSetting = 'pupi_srs_botscout_check_limit';

        $this->checkLimitDefaultValue = 400;
    }

    /**
     * @throws Exception
     */
    public function isSpamUser(string $email, string $ip): bool
    {
        if (empty($this->key)) {
            throw new Exception('Invalid API key');
        }

        $url = sprintf('http://botscout.com/test/?multi&mail=%s&ip=%s&key=%s', urlencode($email), urlencode($ip), urlencode($this->key));
        $data = qa_retrieve_url($url);

        if (empty($data)) {
            throw new Exception('Error fetching data from server');
        }

        if (strpos($data, '! ') === 0) {
            throw new Exception('API Error: ' . substr($data, 2));
        }

        $this->incrementChecksDone();

        $dataExploded = explode('|', $data);

        if ($dataExploded[0] === 'Y') {
            // Allow up to 5 IP address reports
            if ((int)$dataExploded[3] > 5) {
                return true;
            }

            // Don't allow any email report
            if ((int)$dataExploded[5] > 0) {
                return true;
            }

            return false;
        }

        if ($dataExploded[0] === 'N') {
            return false;
        }

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
            self::BOTSCOUT_KEY_SETTING => [
                'label' => 'API key:', // Intentionally untranslated to make Providers be a single file
                'value' => qa_html(qa_opt(self::BOTSCOUT_KEY_SETTING)),
                'tags' => sprintf('name="%s"', self::BOTSCOUT_KEY_SETTING),
            ],
            $this->checkLimitSetting => [
                'label' => 'API daily requests limit:', // Intentionally untranslated to make Providers be a single file
                'value' => qa_html($this->getLimitCheck()),
                'tags' => sprintf('name="%s"', $this->checkLimitSetting),
                'note' => 'BotScout has a daily limit of "normally 300 per day" so 400 is a decent default value',
            ],
        ];
    }

    public function saveAdminForm()
    {
        qa_opt(self::BOTSCOUT_KEY_SETTING, qa_post_text(self::BOTSCOUT_KEY_SETTING));
        qa_opt($this->checkLimitSetting, qa_post_text($this->checkLimitSetting));
    }
}

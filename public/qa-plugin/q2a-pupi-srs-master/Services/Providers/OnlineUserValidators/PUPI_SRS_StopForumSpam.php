<?php

class PUPI_SRS_StopForumSpam extends PUPI_SRS_AbstractOnlineUserValidator
{
    public function __construct()
    {
        $this->name = 'StopForumSpam';

        $this->rateLimitEnabled = true;

        $this->lastCheckSetting = 'pupi_srs_stopforumspam_last_check';
        $this->checkCountSetting = 'pupi_srs_stopforumspam_check_count';
        $this->checkLimitSetting = 'pupi_srs_stopforumspam_check_limit';

        $this->checkLimitDefaultValue = 100000;
    }

    /**
     * @throws Exception
     */
    public function isSpamUser(string $email, string $ip): bool
    {
        $url = sprintf('http://api.stopforumspam.org/api?email=%s&ip=%s&json', urlencode($email), urlencode($ip));
        $data = qa_retrieve_url($url);

        $data = json_decode($data, true);

        if (empty($data)) {
            throw new Exception('Error fetching data from server');
        }

        if ($data['success'] === 0) {
            throw new Exception('API Error: ' . $data['error'] ?? '');
        }

        if (isset($data['email']['error'])) {
            throw new Exception('API Error: ' . $data['email']['error'] ?? '');
        }

        if (isset($data['ip']['error'])) {
            throw new Exception('API Error: ' . $data['ip']['error'] ?? '');
        }

        if ($data['email']['blacklisted'] ?? 0 === 1) {
            return true;
        }

        if ($data['ip']['blacklisted'] ?? 0 === 1) {
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

        return !PUPI_SRS_AbstractOnlineUserValidator::isSameDay(date('Y-m-d H:i:s', qa_opt('db_time')), $lastCheckDateTime);
    }

    public function getAdminFormFields(): array
    {
        return [
            $this->checkLimitSetting => [
                'label' => 'API daily requests limit:', // Intentionally untranslated to make Providers be a single file
                'value' => qa_html($this->getLimitCheck()),
                'tags' => sprintf('name="%s"', $this->checkLimitSetting),
                'note' => 'StopForumSpam has a daily limit of 100,000 API requests',
            ],
        ];
    }

    public function saveAdminForm()
    {
        qa_opt($this->checkLimitSetting, qa_post_text($this->checkLimitSetting));
    }
}

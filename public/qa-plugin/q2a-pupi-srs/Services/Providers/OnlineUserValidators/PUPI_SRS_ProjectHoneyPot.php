<?php

class PUPI_SRS_ProjectHoneyPot extends PUPI_SRS_AbstractOnlineUserValidator
{
    const PROJECTHONEYPOT_KEY_SETTING = 'pupi_srs_projecthoneypot_key';

    /**
     * API key for the ProjectHoneyPot service.
     *
     * @var string
     */
    private $key;

    public function __construct()
    {
        $this->name = 'ProjectHoneyPot';
        $this->key = qa_opt(self::PROJECTHONEYPOT_KEY_SETTING);
    }

    /**
     * @throws Exception
     */
    public function isSpamUser(string $email, string $ip): bool
    {
        if (empty($this->key)) {
            throw new Exception('Invalid API key');
        }

        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return false;
        }

        $reversedIp = array_reverse(explode('.', $ip));
        $lookupDns = $this->key . '.' . implode('.', $reversedIp) . '.dnsbl.httpbl.org';

        $response = gethostbyname($lookupDns);

        if ($response === $lookupDns) {
            return false;
        }

        if (!filter_var($response, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $this->throwUnknownErrorException($response);
        }

        $explodedResponse = explode('.', $response);

        if ((int)$explodedResponse[0] !== 127) {
            $this->throwUnknownErrorException($response);
        }

        // This is a search engine, not a bot
        if ((int)$explodedResponse[3] === 0) {
            return false;
        }

        // If more than 120 days have passed since the last report
        if ((int)$explodedResponse[1] > 120) {
            return false;
        }

        // If threat score is 1 or higher
        if ((int)$explodedResponse[2] >= 1) {
            return true;
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

    public function getAdminFormFields(): array
    {
        return [
            self::PROJECTHONEYPOT_KEY_SETTING => [
                'label' => 'API key:', // Intentionally untranslated to make Providers be a single file
                'value' => qa_html(qa_opt(self::PROJECTHONEYPOT_KEY_SETTING)),
                'tags' => sprintf('name="%s"', self::PROJECTHONEYPOT_KEY_SETTING),
            ],
        ];
    }

    public function saveAdminForm()
    {
        qa_opt(self::PROJECTHONEYPOT_KEY_SETTING, qa_post_text(self::PROJECTHONEYPOT_KEY_SETTING));
    }
}

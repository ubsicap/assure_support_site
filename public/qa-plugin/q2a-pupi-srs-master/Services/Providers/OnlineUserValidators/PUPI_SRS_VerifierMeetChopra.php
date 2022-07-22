<?php

class PUPI_SRS_VerifierMeetChopra extends PUPI_SRS_AbstractOnlineUserValidator
{
    const VERIFIERMEETCHOPRA_KEY_SETTING = 'pupi_srs_verifiermeetchopra_key';

    /**
     * API key for the VerifierMeetChopra service.
     *
     * @var string
     */
    private $key;

    public function __construct()
    {
        $this->name = 'VerifierMeetChopra';
        $this->key = qa_opt(self::VERIFIERMEETCHOPRA_KEY_SETTING);
    }

    /**
     * @throws Exception
     */
    public function isSpamUser(string $email, string $ip): bool
    {
        if (empty($this->key)) {
            throw new Exception('Invalid API key');
        }

        $url = sprintf('https://verifier.meetchopra.com/verify/%s?token=%s', urlencode($email), urlencode($this->key));
        $dataString = qa_retrieve_url($url);

        $data = json_decode($dataString, true);

        if (empty($data)) {
            throw new Exception('Error fetching data from server');
        }

        if (!isset($data['status'])) {
            throw new Exception('Unknown error. Data returned: ' . $dataString);
        }

        if (!$data['status']) {
            return true;
        }

        if (isset($data['error'])) {
            throw new Exception('API Error: ' . $data['error']['message'] ?? '');
        }

        return false;
    }

    public function getAdminFormFields(): array
    {
        return [
            self::VERIFIERMEETCHOPRA_KEY_SETTING => [
                'label' => 'API key:', // Intentionally untranslated to make Providers be a single file
                'value' => qa_html(qa_opt(self::VERIFIERMEETCHOPRA_KEY_SETTING)),
                'tags' => sprintf('name="%s"', self::VERIFIERMEETCHOPRA_KEY_SETTING),
            ],
        ];
    }

    public function saveAdminForm()
    {
        qa_opt(self::VERIFIERMEETCHOPRA_KEY_SETTING, qa_post_text(self::VERIFIERMEETCHOPRA_KEY_SETTING));
    }
}

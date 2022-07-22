<?php

class PUPI_SRS_EmailValidatorManager
{
    /** @var string */
    private static $standarizationResultsCache = null;

    /** @var string */
    private $directory;

    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    public static function getStandarizationResultsCache()
    {
        return self::$standarizationResultsCache;
    }

    /**
     * Return an array with the key 'status' which can contain the values 'q2a-duplicate', 'service-duplicate',
     * 'invalid' and 'valid'. When the status is duplicated by the service, then the key 'registeredEmail' is
     * included in the array. The status 'q2a-duplicate' means the email can be found by the Q2A core was found
     * by the core, 'service-duplicate' means that the email is not fould as a Q2A duplicate but it is duplicate
     * for one of the services, 'invalid' means the email is syntactically incorrect and 'valid' means the email
     * is syntactically correct and has no duplicate.
     */
    public function getVerificationResult(string $email): array
    {
        // If Q2A is going to flag it as duplicate anyways, avoid checking and incrementing counts
        if ($this->isDuplicatedForQ2A($email)) {
            return ['status' => 'q2a-duplicate'];
        }

        require_once $this->directory . 'Services/PUPI_SRS_ServiceManager.php';

        $services = PUPI_SRS_ServiceManager::getAllEmailValidators($this->directory);

        $isValid = $this->isValid($email, $services);

        if (!$isValid) {
            return ['status' => 'invalid'];
        }

        $standarizationResults = $this->getStandarizationResults($email, $services);
        self::$standarizationResultsCache = $standarizationResults;

        if (is_null($standarizationResults['standarizedByService'])) {
            return ['status' => 'valid'];
        }

        require_once $this->directory . 'Models/PUPI_SRS_StandarizedEmailsModel.php';

        $standarizedEmailsModel = new PUPI_SRS_StandarizedEmailsModel();
        $standarizedEmailRecord = $standarizedEmailsModel->getStandarizedEmailRecordFromDatabase($standarizationResults['email']);

        $foundInDatabase = isset($standarizedEmailRecord);

        if ($foundInDatabase) {
            $standarizedEmailsModel->insertUpdateEmailInDatabase(
                $standarizationResults['email'],
                $standarizedEmailRecord['registered_email']
            );
        }

        $this->updateStats($services, $standarizationResults['standarizedByService'], $foundInDatabase);

        return $foundInDatabase ?
            [
                'status' => 'service-duplicate',
                'registeredEmail' => $standarizedEmailRecord['registered_email'] ?? null,
            ]
            : ['status' => 'valid'];
    }

    private function updateStats(array $services, $serviceName, bool $foundInDatabase)
    {
        $newStats = PUPI_SRS_ServiceManager::createStatsSettingsForSevices($services);

        PUPI_SRS_ServiceManager::migrateOldStatsToNewStats($services, $newStats, 'pupi_srs_emails_stats');

        PUPI_SRS_ServiceManager::incrementServiceStats($newStats, $serviceName, $foundInDatabase);

        PUPI_SRS_ServiceManager::saveStats('pupi_srs_emails_stats', $newStats);
    }

    private function isDuplicatedForQ2A(string $email): bool
    {
        require_once QA_INCLUDE_DIR . 'db/users.php';

        return !empty(qa_db_user_find_by_email($email));
    }

    public function getStandarizationResults(string $email, array $services): array
    {
        $email = strtolower($email);
        $standarizedByService = null;

        foreach ($services as $service) {
            $domainStartPosition = strrpos($email, '@') + 1;
            $domain = substr($email, $domainStartPosition);
            $user = substr($email, 0, $domainStartPosition - 1);

            if ($service->canProcessEmail($email, $user, $domain)) {
                $email = $service->getStandarizedEmail($email, $user, $domain);
                $standarizedByService = $service->getName();

                break;
            }
        }

        return [
            'email' => $email,
            'standarizedByService' => $standarizedByService,
        ];
    }

    public function isValid(string $email, array $services): bool
    {
        $domainStartPosition = strrpos($email, '@') + 1;
        $domain = substr($email, $domainStartPosition);
        $user = substr($email, 0, $domainStartPosition - 1);

        foreach ($services as $service) {
            if ($service->canProcessEmail($email, $user, $domain)) {
                return $service->isValid($email, $user, $domain);
            }
        }

        return true;
    }
}

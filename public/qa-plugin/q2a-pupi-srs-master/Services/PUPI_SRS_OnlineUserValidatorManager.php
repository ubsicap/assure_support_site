<?php

class PUPI_SRS_OnlineUserValidatorManager
{
    /** @var string */
    private $directory;

    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    public function isSpammer(string $email, string $ipAddress): bool
    {
        require_once $this->directory . 'Services/PUPI_SRS_ServiceManager.php';

        $services = PUPI_SRS_ServiceManager::getAllOnlineUsersValidators($this->directory);
        $newStats = PUPI_SRS_ServiceManager::createStatsSettingsForSevices($services);

        PUPI_SRS_ServiceManager::migrateOldStatsToNewStats($services, $newStats, 'pupi_srs_services_stats');

        $services = $this->getUsableServices($services);

        $isSpamUser = false;
        foreach ($services as $service) {
            $serviceName = $service->getName();
            try {
                $isSpamUser = $service->isSpamUser($email, $ipAddress);

                PUPI_SRS_ServiceManager::incrementServiceStats($newStats, $serviceName, $isSpamUser);
                $service->incrementChecksDone();

                if ($isSpamUser) {
                    break;
                }
            } catch (Exception $e) {
                error_log(sprintf('<PUPI_SRS - %s> %s', $serviceName, $e->getMessage()));
            }
        }

        PUPI_SRS_ServiceManager::saveStats('pupi_srs_services_stats', $newStats);

        return $isSpamUser;
    }

    /**
     * @param array $services
     *
     * @return array
     */
    private function getUsableServices(array $services): array
    {
        $services = array_filter($services, function ($service) {
            if ($service->shouldResetCheckCount()) {
                $service->resetCheckCount();

                return true;
            } else if ($service->getRemainingChecks() > 0) {
                return true;
            } else {
                return false;
            }
        });

        usort($services, function (PUPI_SRS_AbstractOnlineUserValidator $s1, PUPI_SRS_AbstractOnlineUserValidator $s2) {
            return $s2->getRemainingChecks() - $s1->getRemainingChecks();
        });

        return $services;
    }
}

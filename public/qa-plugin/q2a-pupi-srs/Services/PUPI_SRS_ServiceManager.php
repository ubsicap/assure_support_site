<?php

class PUPI_SRS_ServiceManager
{
    const MONTHS_BACK_HISTORY = 3;

    const EMAIL_VALIDATORS = 'EmailValidators';
    const ONLINE_USER_VALIDATORS = 'OnlineUserValidators';

    public static function getAllEmailValidators(string $pluginDirectory): array
    {
        require_once $pluginDirectory . '/Services/PUPI_SRS_AbstractValidator.php';
        require_once $pluginDirectory . '/Services/PUPI_SRS_AbstractEmailValidator.php';

        return self::getAllServicesForCategory($pluginDirectory, self::EMAIL_VALIDATORS);
    }

    public static function getAllOnlineUsersValidators(string $pluginDirectory): array
    {
        require_once $pluginDirectory . '/Services/PUPI_SRS_AbstractValidator.php';
        require_once $pluginDirectory . '/Services/PUPI_SRS_AbstractOnlineUserValidator.php';

        return self::getAllServicesForCategory($pluginDirectory, self::ONLINE_USER_VALIDATORS);
    }

    private static function getAllServicesForCategory(string $pluginDirectory, string $category): array
    {
        $result = [];
        require_once $pluginDirectory . '/Services/PUPI_SRS_AbstractValidator.php';

        $serviceFiles = glob($pluginDirectory . '/Services/Providers/' . $category . '/*.php');

        foreach ($serviceFiles as $serviceFile) {
            require_once $serviceFile;

            $serviceClassname = basename($serviceFile, '.php');
            $result[] = new $serviceClassname();
        }

        return $result;
    }

    public static function createStatsSettingsForSevices(array $services): array
    {
        $result = [
            'version' => 1,
            'providers' => [],
        ];

        $currentMonth = date('Y-m-01', qa_opt('db_time'));

        $periods = [];
        for ($i = 0; $i < self::MONTHS_BACK_HISTORY; $i++) {
            $yearMonth = date('Y-m', strtotime(sprintf('%s -%d month', $currentMonth, $i)));
            $periods[$yearMonth] = [
                'totalTests' => 0,
                'spamUsers' => 0,
            ];
        }

        foreach ($services as $service) {
            $name = $service->getName();

            $result['providers'][$name] = [
                'totalTests' => 0,
                'spamUsers' => 0,
                'periods' => $periods,
            ];
        }

        return $result;
    }

    public static function migrateOldStatsToNewStats(array $services, array &$newStats, string $setting)
    {
        $oldStats = json_decode(qa_opt($setting), true);
        if (is_null($oldStats)) {
            return;
        }

        foreach ($services as $service) {
            $serviceName = $service->getName();
            self::migrateOldStatsToNewStatsForService($oldStats, $newStats['providers'][$serviceName], $serviceName);
        }
    }

    private static function migrateOldStatsToNewStatsForService(array $oldStats, array &$newProviderStats, string $name)
    {
        if (isset($oldStats['providers'][$name]['periods'])) {
            foreach ($newProviderStats['periods'] as $yearMonth => $dummy) {
                if (isset($oldStats['providers'][$name]['periods'][$yearMonth])) {
                    $newProviderStats['periods'][$yearMonth] = $oldStats['providers'][$name]['periods'][$yearMonth];
                }
            }
        }

        if (isset($oldStats['providers'][$name]['totalTests'])) {
            $newProviderStats['totalTests'] = $oldStats['providers'][$name]['totalTests'];
        }

        if (isset($oldStats['providers'][$name]['spamUsers'])) {
            $newProviderStats['spamUsers'] = $oldStats['providers'][$name]['spamUsers'];
        }
    }

    public static function incrementServiceStats(array &$newStats, string $serviceName, bool $isSpamUser)
    {
        $currentMonth = key($newStats['providers'][$serviceName]['periods']);

        $newStats['providers'][$serviceName]['periods'][$currentMonth]['totalTests']++;
        $newStats['providers'][$serviceName]['totalTests']++;
        if ($isSpamUser) {
            $newStats['providers'][$serviceName]['periods'][$currentMonth]['spamUsers']++;
            $newStats['providers'][$serviceName]['spamUsers']++;
        }
    }

    public static function saveStats(string $setting, array $newStats)
    {
        qa_opt($setting, json_encode($newStats));
    }
}

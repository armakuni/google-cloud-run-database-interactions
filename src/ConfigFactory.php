<?php


namespace Armakuni\Demo\PhpCounter;


class ConfigFactory
{
    /**
     * @return array
     */
    public function build(): array
    {
        $googleConfig = [];

        if ($keyFilePath = getenv('GOOGLE_DATASTORE_KEYFILE')) {
            $googleConfig['keyFilePath'] = $keyFilePath;
        }

        if ($projectId = getenv('GOOGLE_PROJECT_ID')) {
            $googleConfig['projectId'] = $projectId;
        }

        return $googleConfig;
    }

}
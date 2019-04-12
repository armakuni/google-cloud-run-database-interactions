<?php


use Google\Cloud\Datastore\DatastoreClient;
use OpenCensus\Trace\Exporter\StackdriverExporter;
use OpenCensus\Trace\Integrations\Curl;
use OpenCensus\Trace\Integrations\Grpc;
use OpenCensus\Trace\Tracer;

/**
 * @return array
 */
function createGoogleClientConfig(): array
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


/**
 * @param array $googleConfig
 * @return StackdriverExporter
 */
function createStackDriverExporter(array $googleConfig): StackdriverExporter
{
    $exporter = new StackdriverExporter(
        [
            'clientConfig' => $googleConfig,
        ]
    );

    return $exporter;
}

/**
 * @param DatastoreClient $datastore
 * @return int
 */
function incrementCounter(DatastoreClient $datastore): int
{
    $counterKey = $datastore->key('Counter');
    $counterKey = $datastore->allocateId($counterKey);
    $counter = $datastore->entity($counterKey, []);
    $datastore->upsert($counter);
    $query = $datastore->query()
        ->keysOnly();
    $results = $datastore->runQuery($query);

    return iterator_count($results);
}

/**
 * @param array $googleConfig
 */
function startTracer(array $googleConfig): void
{
    $exporter = createStackDriverExporter($googleConfig);

    Grpc::load();
    Curl::load();
    Tracer::start($exporter);
}

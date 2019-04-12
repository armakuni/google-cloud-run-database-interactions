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
 */
function incrementCounter(DatastoreClient $datastore): void
{
    $taskKey = $datastore->key('Counter', 1);
    $transaction = $datastore->transaction();
    $counter = $transaction->lookup($taskKey);
    $current = 0;

    if ($counter !== null) {
        $current = $counter['counter'] + 1;
    }

    $counter = $datastore->entity($taskKey, ["counter" => $current]);
    $transaction->upsert($counter);
    $transaction->commit();

    echo $counter['counter'];
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

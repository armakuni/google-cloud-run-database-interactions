<?php

require __DIR__ . "/vendor/autoload.php";

use Google\Cloud\Datastore\DatastoreClient;
use OpenCensus\Trace\Exporter\StackdriverExporter;
use OpenCensus\Trace\Integrations\Grpc;
use OpenCensus\Trace\Tracer;

$googleConfig = [];

if ($keyFilePath = getenv('GOOGLE_DATASTORE_KEYFILE')) {
    $googleConfig['keyFilePath'] = $keyFilePath;
}

if ($projectId = getenv('GOOGLE_PROJECT_ID')) {
    $googleConfig['projectId'] = $projectId;
}

Grpc::load();
$exporter = new StackdriverExporter(
    [
        'clientConfig' => $googleConfig
    ]
);

Tracer::start($exporter);

$datastore = new DatastoreClient($googleConfig);

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
<?php

require __DIR__ . "/vendor/autoload.php";
require __DIR__ . "/functions.php";

use Google\Cloud\Datastore\DatastoreClient;
use OpenCensus\Trace\Tracer;


$googleConfig = createGoogleClientConfig();
startTracer($googleConfig);

$datastore = Tracer::inSpan(
    ['name' => 'init'],
    function () use ($googleConfig) {
        return new DatastoreClient($googleConfig);
    }
);

$count = Tracer::inSpan(
    ['name' => 'count'],
    function () use ($googleConfig, $datastore) {
        return incrementCounter($datastore);
    }
);

Tracer::inSpan(
    ['name' => 'render'],
    function () use ($count) {
        echo $count;
    }
);

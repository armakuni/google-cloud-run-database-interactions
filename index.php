<?php

require __DIR__."/vendor/autoload.php";
require __DIR__."/functions.php";

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

Tracer::inSpan(
    ['name' => 'count'],
    function () use ($googleConfig, $datastore) {
        incrementCounter($datastore);
    }
);

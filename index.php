<?php

require __DIR__ . "/vendor/autoload.php";

use Armakuni\Demo\PhpCounter\ConfigFactory;
use Armakuni\Demo\PhpCounter\CounterService;
use Armakuni\Demo\PhpCounter\StackdriverExporterFactory;
use Armakuni\Demo\PhpCounter\TraceService;
use Google\Cloud\Datastore\DatastoreClient;
use OpenCensus\Trace\Tracer;


$googleConfig = (new ConfigFactory())->build();
$exporter = (new StackdriverExporterFactory($googleConfig))->build();
(new TraceService($exporter))->start();


$datastore = Tracer::inSpan(
    ['name' => 'init'],
    function () use ($googleConfig) {
        return new DatastoreClient($googleConfig);
    }
);

$count = Tracer::inSpan(
    ['name' => 'count'],
    function () use ($datastore) {
        return (new CounterService($datastore))->incrementCounter();
    }
);

Tracer::inSpan(
    ['name' => 'render'],
    function () use ($count) {
        echo $count;
    }
);

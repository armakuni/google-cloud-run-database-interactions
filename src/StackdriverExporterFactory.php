<?php


namespace Armakuni\Demo\PhpCounter;


use OpenCensus\Trace\Exporter\StackdriverExporter;

class StackdriverExporterFactory
{

    private $googleConfig;

    public function __construct($googleConfig)
    {
        $this->googleConfig = $googleConfig;
    }

    public function build() {
        $exporter = new StackdriverExporter(
            [
                'clientConfig' => $this->googleConfig,
            ]
        );

        return $exporter;
    }
}
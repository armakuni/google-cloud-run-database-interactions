<?php


namespace Armakuni\Demo\PhpCounter;


use Google\Cloud\Datastore\DatastoreClient;

class CounterService
{
    private $datastore;

    /**
     * CounterService constructor.
     * @param DatastoreClient $datastore
     */
    public function __construct(DatastoreClient $datastore)
    {
        $this->datastore = $datastore;
    }

    /**
     * @return int
     */
    public function incrementCounter(): int
    {
        $this->addOne();
        return $this->getCount();
    }

    private function addOne(): void
    {
        $counterKey = $this->datastore->key('Counter');
        $counterKey = $this->datastore->allocateId($counterKey);
        $counter = $this->datastore->entity($counterKey, []);
        $this->datastore->upsert($counter);
    }

    /**
     * @return int
     */
    private function getCount(): int
    {
        $query = $this->datastore->query()
            ->keysOnly();
        $results = $this->datastore->runQuery($query);

        return iterator_count($results);
    }

}
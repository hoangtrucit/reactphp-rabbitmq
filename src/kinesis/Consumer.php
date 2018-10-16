<?php

require 'vendor/autoload.php';

use Aws\Kinesis\KinesisClient;

class Consumer{

    /** @var KinesisClient */
    private $kinesisClient;

    /** @var \Aws\Sdk */
    private $sdk;

    /** @var String */
    private $shardId;

    /** @var String */
    private $streamName = 'content-metrics';

    /** @var String */
    private $region = 'ap-southeast-2';

    /** @var String */
    private $shardIterator;

    /** @var int */
    private $numberOfRecordsPerBatch = 100;

    public function __construct(String $shardId){
        $this->sdk = new \Aws\Sdk();
        $this->kinesisClient = $this->sdk->createKinesis(['region' => $this->region, 'version' => '2013-12-02']);
        $this->shardId = $shardId;
        $this->getShardIterator();
    }

    private function getShardIterator() {
        try{
            $res = $this->kinesisClient->getShardIterator([
                'ShardId' => $this->shardId,
                'ShardIteratorType' => 'LATEST',
                'StreamName' => $this->streamName,
            ]);
            $this->shardIterator = $res->get('ShardIterator');
        }catch (Throwable $throwable){
            echo "Exception getShardIterator $throwable->getMessage()";
        }
    }

    public function run(){
        echo "consumer with shardId $this->shardId \n";
        $res = $this->kinesisClient->getRecords([
            'Limit' => $this->numberOfRecordsPerBatch,
            'ShardIterator' => $this->shardIterator
        ]);

        $this->shardIterator = $res->get('NextShardIterator');

        foreach ($res->search('Records[].[SequenceNumber, Data]') as $data) {
            print_r($data);
            echo "\n";
        }

        echo "end consumer with shardId $this->shardId \n";
    }
}

$consumerShardOne = new Consumer("shardId-000000000000");
$consumerShardTwo = new Consumer("shardId-000000000001");


$loop = React\EventLoop\Factory::create();

$loop->addPeriodicTimer(10, function() use(&$consumerShardOne) {
    $consumerShardOne->run();
});

$loop->addPeriodicTimer(11, function() use(&$consumerShardTwo) {
    $consumerShardTwo->run();
});

$loop->run();


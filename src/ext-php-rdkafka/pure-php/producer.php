<?php

require_once('../../../vendor/autoload.php');

use RdKafka\Conf;
use RdKafka\Message;
use RdKafka\Producer;
use RdKafka\TopicConf;

error_reporting(E_ALL);

$conf = new Conf();
// will be visible in broker logs
$conf->set('client.id', 'pure-php-producer');
// set broker
$conf->set('metadata.broker.list', 'kafka:9096');
// set compression (supported are: none,gzip,lz4,snappy,zstd)
$conf->set('compression.codec', 'snappy');
// set timeout, producer will retry for 5s
$conf->set('message.timeout.ms', '5000');

// SASL Authentication
//$conf->set('sasl.mechanisms', '');
//$conf->set('ssl.endpoint.identification.algorithm', 'https');
//$conf->set('sasl.username', '');
//$conf->set('sasl.password', '');

// SSL Authentication
//$conf->set('security.protocol', 'ssl');
//$conf->set('ssl.ca.location', __DIR__.'/../keys/ca.pem');
//$conf->set('ssl.certificate.location', __DIR__.'/../keys/kafka.cert');
//$conf->set('ssl.key.location', __DIR__.'/../keys/kafka.key');

$producer = new Producer($conf);
// initialize producer topic
$topic = $producer->newTopic('pure-php-test-topic');
// Produce 10 test messages
$amountTestMessages = 10;

// Loop to produce some test messages
for ($i = 0; $i < $amountTestMessages; ++$i) {
    // Let the partitioner decide the target partition, default partitioner is: RD_KAFKA_MSG_PARTITIONER_CONSISTENT_RANDOM
    // You can use a predefined partitioner or write own logic to decide the target partition
    $partition = RD_KAFKA_PARTITION_UA;

    //produce message with payload, key and headers
    $topic->producev(
        $partition,
        RD_KAFKA_MSG_F_BLOCK, // will block produce if queue is full
        sprintf('test message-%d',$i),
        sprintf('test-key-%d', $i),
        [
            'some' => sprintf('header value %d', $i)
        ]
    );
    echo sprintf('Successfully sent message number: %d', $i);

    // Poll for events e.g. producer callbacks, to handle errors, etc.
    // 0 = non-blocking
    // -1 = blocking
    // any other int value = timeout in ms
    $producer->poll(0);
}

// Shutdown producer, flush messages that are in queue. Give up after 20s
$result = $producer->flush(20000);

if (RD_KAFKA_RESP_ERR_NO_ERROR !== $result) {
    echo 'Was not able to shutdown within 20s. Messages might be lost!' . PHP_EOL;
}


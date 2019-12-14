<?php

require_once('../../../vendor/autoload.php');

use RdKafka\Conf;
use RdKafka\Message;
use RdKafka\Producer;
use RdKafka\TopicConf;

error_reporting(E_ALL);

$conf = new Conf();
// set broker
$conf->set('metadata.broker.list', 'kafka:9097');
// set compression
$conf->set('compression.codec', 'gzip');


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
// Set the topic configuration:
$topicConfig = new TopicConf();
// Set timout for trying to produce the message
$topicConfig->set('message.timeout.ms', 5000);
// initialize producer topic
$topic = $producer->newTopic('test-topic', $topicConfig);

try {
    while(true) {
        // Let the partitioner decide the target partition
        $partition = RD_KAFKA_PARTITION_UA;
        //produce message with payload, key and headers
        $topic->producev($partition, 0, 'test message','test-key', ['some' => 'header value']);

        // Trigger producer callbacks, to handle errors, etc.
        while ($producer->getOutQLen() > 0) {
            $producer->poll(100);
        }
    }
} catch (\RuntimeException $e) {
    echo 'ERROR: ' , $e->getMessage() , ' (code: ' , $e->getCode() , ')' , PHP_EOL;
    exit;
}

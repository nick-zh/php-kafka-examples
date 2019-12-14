<?php

require_once('../../../vendor/autoload.php');

use RdKafka\Conf;
use RdKafka\Consumer;
use RdKafka\TopicConf;

$conf = new Conf();
// set consumer group
$conf->set('group.id', 'test-consumer');
// set broker
$conf->set('metadata.broker.list', 'kafka:9097');
// don't auto commit, give the application the control to do that (default is: true)
$conf->set('enable.auto.offset.store', 'false');
// start at the very beginning of the topic when reading for the first time
$conf->set('auto.offset.reset', 'earliest');

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

$consumer = new Consumer($conf);

$topicConf = new TopicConf();

// get consumer topic
$topic = $consumer->newTopic("test", $topicConf);
// initialize consumption, continue from offset (or offset.reset)
$topic->consumeStart(0, RD_KAFKA_OFFSET_STORED);

while (true) {
    // Try to consume messages for the given timout (20s)
    $message = $consumer->consume(20000);

    switch ($message->err) {
        case RD_KAFKA_RESP_ERR_NO_ERROR:
            echo sprintf(
                    'Read message with key:%s payload:%s topic:%s partition:%d offset:%d',
                    $message->key,
                    $message->payload,
                    $message->topic_name,
                    $message->partition,
                    $message->offset
                ) . PHP_EOL;
            break;
        case RD_KAFKA_RESP_ERR__PARTITION_EOF:
            echo 'Reached end of partition, waiting for more messages...' . PHP_EOL;
            break;
        case RD_KAFKA_RESP_ERR__TIMED_OUT:
            echo 'Timed out, waiting for more messages...' . PHP_EOL;
            break;
        default:
            echo rd_kafka_err2str($message->err);
            break;
    }
}

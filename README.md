# PHP Kafka Examples
This repository has PHP examples for Kafka consumers / producers for:
- [php-rdkafka](https://github.com/arnaud-lb/php-rdkafka): Examples just using the PHP extension
- [php-kafka-lib](https://github.com/jobcloud/php-kafka-lib): PHP library that relies on [php-rdkafka](https://github.com/arnaud-lb/php-rdkafka) and supports [avro](https://github.com/flix-tech/avro-serde-php)

## Examples
- [php-rdkafka](src/ext-php-rdkafka/pure-php)
- [php-kafka-lib](src/ext-php-rdkafka/php-kafka-lib)

## Start containers for examples
Be sure to start the docker containers.  
To do so, run this in the project root:
```bash
docker-compose up -d
docker-compose exec php bash
```
Then follow the instructions in the example folders.
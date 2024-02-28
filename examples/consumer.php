<?php

use Hoffman\SimplePhpQueue\Queue;
use Hoffman\SimplePhpQueue\Storage\StorageType;

require __DIR__ . '/../vendor/autoload.php';

$queue = new Queue(
  storage: StorageType::FILE,
  queueFile: "./",
  debug: true
);

$queue->listen(function ($item) {
  echo $item . PHP_EOL;
});

<?php

use Hoffman\SimplePhpQueue\Queue;
use Hoffman\SimplePhpQueue\Storage\StorageType;

require __DIR__ . '/../vendor/autoload.php';

$queue = new Queue(StorageType::SQLITE, "./");
for ($i = 0; $i < 150; $i++) {
  $queue->enqueue("test $i");
}

<?php

use Slowmove\SimplePhpQueue\Queue;
use Slowmove\SimplePhpQueue\Storage\StorageType;

require __DIR__ . '/../vendor/autoload.php';

$queue = new Queue(
  storage: StorageType::FILE,
  storagePath: "./",
  storageName: "my_queue"
);
for ($i = 0; $i < 150; $i++) {
  $queue->enqueue("test $i");
}

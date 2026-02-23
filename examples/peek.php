<?php

use Slowmove\SimplePhpQueue\Queue;
use Slowmove\SimplePhpQueue\Storage\StorageType;

require __DIR__ . '/../vendor/autoload.php';

$queue = new Queue(
  storage: StorageType::FILE,
  queueFile: ""
);

$next = $queue->peek();
echo "Next item in queue: " . ($next ?? '(empty)') . PHP_EOL;

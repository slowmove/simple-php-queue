Simple PHP Queue
===

Simple file system based PHP queue, with multiple adapters.

Currently implemented

- `textfile`
- `sqlite`
- `redis` (send in connection string instead of file path, default to localhost)
- `beanstalkd` https://beanstalkd.github.io/

## Requirements

- `PHP 8.1+`

## Usage examples

Examples available to run [here](./examples/)

### Producer

```php
<?php

use Slowmove\SimplePhpQueue\Queue;
use Slowmove\SimplePhpQueue\Storage\StorageType;

$queue = new Queue(StorageType::SQLITE, "./queue.db");
for ($i = 0; $i < 150; $i++) {
  $queue->enqueue("test $i");
}
```

### Consumer

```php
<?php 

use Slowmove\SimplePhpQueue\Queue;
use Slowmove\SimplePhpQueue\Storage\StorageType;

$queue = new Queue(StorageType::SQLITE, "./queue.db", true);

$queue->listen(function ($item) {
  echo $item . PHP_EOL;
});
```

### Types

Enum available [here](./src/Storage/StorageType.php);

```php
enum StorageType: string
{
  case FILE = 'file';
  case SQLITE = 'sqlite';
  case REDIS = 'redis';
  case BEANSTALKD = 'beanstalkd';
}
```

## Methods

- `enqueue(string $data): bool`
- `dequeue(): ?string`
- `listen(callable $fn, int $delayWhenEmpty = 5): void`
- `length(): int`
- `content(): array`
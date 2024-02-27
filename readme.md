Simple PHP Queue
===

Simple file system based PHP queue, with multiple adapters.

Currently implemented

- `textfile`
- `sqlite`

## Usage examples

### Producer

```php
<?php

use Hoffman\SimplePhpQueue\Queue;
use Hoffman\SimplePhpQueue\Storage\StorageType;

$queue = new Queue(StorageType::SQLITE, "./queue.db");
for ($i = 0; $i < 150; $i++) {
  $queue->enqueue("test $i");
}
```

### Consumer

```php
<?php 

use Hoffman\SimplePhpQueue\Queue;
use Hoffman\SimplePhpQueue\Storage\StorageType;

$queue = new Queue(StorageType::SQLITE, "./", true);

$queue->listen(function ($item) {
  echo $item . PHP_EOL;
});
```


## Methods

- `enqueue(string $data): bool`
- `dequeue(): ?string`
- `listen(callable $fn, $delayWhenEmpty = 5): void`
- `length(): int`
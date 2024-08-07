<?php

namespace Slowmove\SimplePhpQueue;

use Slowmove\SimplePhpQueue\Storage\StorageFactory;
use Slowmove\SimplePhpQueue\Storage\StorageInterface;
use Slowmove\SimplePhpQueue\Storage\StorageType;

class Queue
{
  private StorageInterface $storage;
  private bool $debug;

  public function __construct(StorageType $storage, string $queueFile, bool $debug = false)
  {
    $this->debug = $debug;
    $this->storage = StorageFactory::getStorage($storage, $queueFile);
  }

  public function enqueue(string $data): bool
  {
    return $this->storage->enqueue($data);
  }

  public function dequeue(): ?string
  {
    return $this->storage->dequeue();
  }

  public function exist($value): ?string
  {
    return $this->storage->exist($value);
  }

  public function listen(callable $fn, int $delayWhenEmpty = 5): void
  {
    $delaySeconds = $delayWhenEmpty;
    while (true) {
      if (($item = $this->dequeue()) !== null) {
        $fn($item);
      } else {
        if ($this->debug) {
          echo "Queue is empty. Sleeping for $delaySeconds seconds..." . PHP_EOL;
        }
        sleep($delaySeconds);
      }
    }
  }

  public function length(): int
  {
    return $this->storage->length();
  }

  public function content(): array
  {
    return $this->storage->content();
  }
}

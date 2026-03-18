<?php

namespace Slowmove\SimplePhpQueue;

use Slowmove\SimplePhpQueue\Storage\StorageFactory;
use Slowmove\SimplePhpQueue\Storage\StorageInterface;
use Slowmove\SimplePhpQueue\Storage\StorageType;

class Queue
{
  private StorageInterface $storage;

  public function __construct(
    StorageType $storage,
    string $storagePath = "",
    string $storageName = 'queue',
  ) {
    $this->storage = StorageFactory::getStorage($storage, $storagePath, $storageName);
  }

  public function enqueue(string $data): bool
  {
    return $this->storage->enqueue($data);
  }

  public function dequeue(): ?string
  {
    return $this->storage->dequeue();
  }

  public function exist($value): bool
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

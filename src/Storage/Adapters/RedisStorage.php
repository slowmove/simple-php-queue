<?php

namespace Slowmove\SimplePhpQueue\Storage\Adapters;

use Slowmove\SimplePhpQueue\Storage\StorageInterface;
use Predis\Client;


class RedisStorage implements StorageInterface
{
  const DEFAULT_STORAGE_PATH = 'tcp://127.0.0.1:6379';

  private Client $redisClient;
  private string $storageKey;

  public function __construct(
    string $connectionString = self::DEFAULT_STORAGE_PATH,
    string $storageKey = 'queue'
  ) {
    $this->redisClient = new Client($connectionString);
    $this->storageKey = $storageKey;
  }

  public function enqueue(string $data): bool
  {
    $res = $this->redisClient->lpush($this->storageKey, $data);
    return !!$res;
  }

  public function dequeue(): ?string
  {
    return $this->redisClient->rpop($this->storageKey);
  }

  public function exist(string $value): bool
  {
    $exist = $this->redisClient->executeRaw(["LPOS", $this->storageKey, $value]);
    return boolval($exist);
  }

  public function length(): int
  {
    return $this->redisClient->llen($this->storageKey);
  }

  public function content(): array
  {
    return $this->redisClient->lrange($this->storageKey, 0, -1);
  }
}

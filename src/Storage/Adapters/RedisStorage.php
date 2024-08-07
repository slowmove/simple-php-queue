<?php

namespace Slowmove\SimplePhpQueue\Storage\Adapters;

use Slowmove\SimplePhpQueue\Storage\StorageInterface;
use Predis\Client;


class RedisStorage implements StorageInterface
{
  const DEFAULT_STORAGE_PATH = 'tcp://127.0.0.1:6379';
  const DEFAULT_STORAGE_NAME = 'queue';

  private Client $redisClient;

  public function __construct(string $storagePath)
  {
    $connectionString = $storagePath ?: self::DEFAULT_STORAGE_PATH;
    $this->redisClient = new Client($connectionString);
  }

  public function enqueue(string $data): bool
  {
    $res = $this->redisClient->lpush(self::DEFAULT_STORAGE_NAME, $data);
    return !!$res;
  }

  public function dequeue(): ?string
  {
    return $this->redisClient->rpop(self::DEFAULT_STORAGE_NAME);
  }

  public function exist(string $value): bool
  {
    $exist = $this->redisClient->executeRaw(["LPOS", self::DEFAULT_STORAGE_NAME, $value]);
    return boolval($exist);
  }

  public function length(): int
  {
    return $this->redisClient->llen(self::DEFAULT_STORAGE_NAME);
  }

  public function content(): array
  {
    return $this->redisClient->lrange(self::DEFAULT_STORAGE_NAME, 0, -1);
  }
}

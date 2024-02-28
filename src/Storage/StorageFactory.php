<?php

namespace Slowmove\SimplePhpQueue\Storage;

use Slowmove\SimplePhpQueue\Storage\Adapters\BeanstalkdStorage;
use Slowmove\SimplePhpQueue\Storage\Adapters\FileStorage;
use Slowmove\SimplePhpQueue\Storage\Adapters\RedisStorage;
use Slowmove\SimplePhpQueue\Storage\Adapters\SqliteStorage;

class StorageFactory
{
  public static function getStorage(StorageType $type, string $storagePath = ""): StorageInterface
  {
    return match ($type) {
      StorageType::FILE       => new FileStorage($storagePath),
      StorageType::SQLITE     => new SqliteStorage($storagePath),
      StorageType::REDIS      => new RedisStorage($storagePath),
      StorageType::BEANSTALKD => new BeanstalkdStorage($storagePath),
    };
  }
}

<?php

namespace Slowmove\SimplePhpQueue\Storage;

use Slowmove\SimplePhpQueue\Storage\Adapters\BeanstalkdStorage;
use Slowmove\SimplePhpQueue\Storage\Adapters\FileStorage;
use Slowmove\SimplePhpQueue\Storage\Adapters\RedisStorage;
use Slowmove\SimplePhpQueue\Storage\Adapters\SqliteStorage;

class StorageFactory
{
  public static function getStorage(StorageType $type, string $storagePath = "", string $storageName = 'queue'): StorageInterface
  {
    return match ($type) {
      StorageType::FILE       => new FileStorage($storagePath, $storageName),
      StorageType::SQLITE     => new SqliteStorage($storagePath, $storageName),
      StorageType::REDIS      => new RedisStorage($storagePath, $storageName),
      StorageType::BEANSTALKD => new BeanstalkdStorage($storagePath, $storageName),
    };
  }
}

<?php

namespace Hoffman\SimplePhpQueue\Storage;

use Hoffman\SimplePhpQueue\Storage\Adapters\FileStorage;
use Hoffman\SimplePhpQueue\Storage\Adapters\RedisStorage;
use Hoffman\SimplePhpQueue\Storage\Adapters\SqliteStorage;

class StorageFactory
{
  public static function getStorage(StorageType $type, string $storagePath = "")
  {
    return match ($type) {
      StorageType::FILE    => new FileStorage($storagePath),
      StorageType::SQLITE  => new SqliteStorage($storagePath),
      StorageType::REDIS   => new RedisStorage($storagePath),
    };
  }
}

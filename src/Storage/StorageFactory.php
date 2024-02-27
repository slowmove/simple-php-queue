<?php

namespace Hoffman\SimplePhpQueue\Storage;

use Hoffman\SimplePhpQueue\Storage\Adapters\FileStorage;
use Hoffman\SimplePhpQueue\Storage\Adapters\SqliteStorage;

class StorageFactory
{
  public static function getStorage(StorageType $type, string $storagePath = "")
  {
    if ($type == StorageType::FILE) {
      return new FileStorage($storagePath);
    }

    if ($type == StorageType::SQLITE) {
      return new SqliteStorage($storagePath);
    }
  }
}

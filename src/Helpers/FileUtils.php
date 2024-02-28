<?php

namespace Slowmove\SimplePhpQueue\Helpers;

class FileUtils
{
  public static function isFilePath(string $path): bool
  {
    $fileInfo = pathinfo($path);
    return !empty($fileInfo['extension']);
  }

  public static function isDirectoryPath(string $path): bool
  {
    return !self::isFilePath($path);
  }

  public static function createDirectory(string $path): bool
  {
    if (is_dir($path)) {
      return true;
    }
    return mkdir($path, 0777, true);
  }

  public static function createFile(string $path): bool
  {
    $folderPath = pathinfo($path)["dirname"];
    if (is_file($path) && !is_dir($path)) return true;

    self::createDirectory($folderPath);

    return touch($path);
  }
}

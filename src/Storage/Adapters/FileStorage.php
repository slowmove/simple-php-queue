<?php

namespace Slowmove\SimplePhpQueue\Storage\Adapters;

use Slowmove\SimplePhpQueue\Helpers\FileUtils;
use Slowmove\SimplePhpQueue\Storage\StorageInterface;

class FileStorage implements StorageInterface
{
  private string $queueFile;

  public function __construct(
    string $storagePath,
    string $storageName = 'queue'
  ) {
    if (empty($storagePath)) {
      $storagePath = ".";
    }
    $this->queueFile = FileUtils::isFilePath($storagePath)
      ? $storagePath
      : rtrim($storagePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $storageName . '.txt';
    FileUtils::createFile($this->queueFile);
  }

  public function enqueue(string $data): bool
  {
    $fileHandle = fopen($this->queueFile, 'a');
    if (!$fileHandle) {
      return false;
    }

    flock($fileHandle, LOCK_EX);

    fwrite($fileHandle, $data . PHP_EOL);

    flock($fileHandle, LOCK_UN);
    fclose($fileHandle);

    return true;
  }

  public function dequeue(): ?string
  {
    $fileHandle = fopen($this->queueFile, 'r+');
    if (!$fileHandle) {
      return null;
    }

    flock($fileHandle, LOCK_EX);

    $data = null;
    $lines = [];

    while (($line = fgets($fileHandle)) !== false) {
      $lines[] = rtrim($line, PHP_EOL);
    }

    if (!empty($lines)) {
      $data = array_shift($lines);
      ftruncate($fileHandle, 0);
      rewind($fileHandle);
      fwrite($fileHandle, implode(PHP_EOL, $lines));
    }

    flock($fileHandle, LOCK_UN);
    fclose($fileHandle);

    return $data;
  }

  public function exist(string $value): bool
  {
    $lines = file($this->queueFile, FILE_SKIP_EMPTY_LINES);
    if (!$lines) {
      return false;
    }
    foreach ($lines as $line) {
      if (trim($line) === trim($value)) {
        return true;
      }
    }
    return false;
  }

  public function length(): int
  {
    $lines = file($this->queueFile, FILE_SKIP_EMPTY_LINES);
    if (!$lines) {
      return 0;
    }
    return count($lines);
  }

  public function content(): array
  {
    return file($this->queueFile, FILE_SKIP_EMPTY_LINES);
  }
}

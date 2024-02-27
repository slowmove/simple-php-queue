<?php

namespace Hoffman\SimplePhpQueue\Storage\Adapters;

use Hoffman\SimplePhpQueue\Storage\StorageInterface;

class FileStorage implements StorageInterface
{
  private string $queueFile;
  private bool $debug = false;

  public function __construct(string $storagePath, $debug = false)
  {
    if (!is_dir($storagePath)) {
      mkdir($storagePath, 0777, true);
    }
    if (!is_writable($storagePath)) {
      throw new \Exception("Storage path $storagePath is not writable");
    }
    if (!is_readable($storagePath)) {
      throw new \Exception("Storage path $storagePath is not readable");
    }

    $this->queueFile = (mb_strpos($storagePath, ".txt") > -1) ?: $storagePath . '/queue.txt';
    $this->debug = $debug;
  }

  public function enqueue(string $data): bool
  {
    $fileHandle = fopen($this->queueFile, 'a');
    if (!$fileHandle) {
      return false;
    }

    flock($fileHandle, LOCK_EX);

    fwrite($fileHandle, $data . PHP_EOL);

    if ($this->debug) {
      echo "Enqueued item: $data" . PHP_EOL;
      echo "===" . PHP_EOL;
    }

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
    if ($this->debug && !empty($data)) {
      echo "Dequeued item: $data" . PHP_EOL;
      echo "===" . PHP_EOL;
    }

    flock($fileHandle, LOCK_UN);
    fclose($fileHandle);

    return $data;
  }

  public function length(): int
  {
    $lines = file($this->queueFile, FILE_SKIP_EMPTY_LINES);
    if (!$lines) {
      return 0;
    }
    return count($lines);
  }
}

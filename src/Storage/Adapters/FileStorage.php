<?php

namespace Slowmove\SimplePhpQueue\Storage\Adapters;

use Slowmove\SimplePhpQueue\Helpers\FileUtils;
use Slowmove\SimplePhpQueue\Storage\StorageInterface;

class FileStorage implements StorageInterface
{
  private string $queueFile;
  private bool $debug = false;

  public function __construct(string $storagePath, bool $debug = false)
  {
    $this->queueFile = FileUtils::isFilePath($storagePath) ? $storagePath : rtrim($storagePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'queue.txt';
    FileUtils::createFile($this->queueFile);
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

  public function content(): array
  {
    return file($this->queueFile, FILE_SKIP_EMPTY_LINES);
  }
}

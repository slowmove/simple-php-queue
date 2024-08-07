<?php

namespace Slowmove\SimplePhpQueue\Storage\Adapters;

use Slowmove\SimplePhpQueue\Helpers\FileUtils;
use Slowmove\SimplePhpQueue\Storage\StorageInterface;

class SqliteStorage implements StorageInterface
{
  private string $queueFile;
  private \SQLite3 $connection;
  private bool $debug = false;

  public function __construct(string $storagePath, bool $debug = false)
  {
    if (empty($storagePath)) {
      $storagePath = ".";
    }
    $this->queueFile = FileUtils::isFilePath($storagePath) ? $storagePath : rtrim($storagePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'queue.db';
    FileUtils::createFile($this->queueFile);
    $this->debug = $debug;

    $this->connection = new \SQLite3($this->queueFile);
    $this->connection->query("CREATE TABLE IF NOT EXISTS queue (id INTEGER PRIMARY KEY AUTOINCREMENT, data TEXT)");
  }

  public function enqueue(string $data): bool
  {
    $stmt = $this->connection->prepare("INSERT INTO queue (data) VALUES (:data)");
    $stmt->bindValue(':data', $data);
    $result = $stmt->execute();
    return !!$result;
  }

  public function dequeue(): ?string
  {
    $result = $this->connection->querySingle("SELECT * FROM queue ORDER BY id asc LIMIT 1", true);
    if ($result) {
      extract($result);
      $this->connection->query("DELETE FROM queue WHERE id = $id");
    }
    return $data ?? null;
  }

  public function exist(string $value): bool
  {
    $result = $this->connection->querySingle("SELECT COUNT(*) FROM queue WHERE data = '$value'");
    return !!$result;
  }

  public function length(): int
  {
    return $this->connection->querySingle("SELECT COUNT(*) FROM queue");
  }

  public function content(): array
  {
    $result = $this->connection->query("SELECT data FROM queue ORDER BY id asc");
    $data = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
      $data[] = $row["data"];
    }
    return $data;
  }
}

<?php

namespace Hoffman\SimplePhpQueue\Storage\Adapters;

use Hoffman\SimplePhpQueue\Storage\StorageInterface;

class SqliteStorage implements StorageInterface
{
  private string $queueFile;
  private \SQLite3 $connection;
  private bool $debug = false;

  public function __construct(string $storagePath, bool $debug = false)
  {
    $this->queueFile = (mb_strpos($storagePath, ".db") > -1) ?: $storagePath . '/queue.db';
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

  public function length(): int
  {
    return $this->connection->querySingle("SELECT COUNT(*) FROM queue");
  }
}

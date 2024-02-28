<?php

namespace Slowmove\SimplePhpQueue\Storage;

interface StorageInterface
{
  public function __construct(string $storagePath);

  public function enqueue(string $data): bool;

  public function dequeue(): ?string;

  public function length(): int;

  public function content(): array;
}

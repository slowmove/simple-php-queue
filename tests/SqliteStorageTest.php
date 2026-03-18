<?php

use Slowmove\SimplePhpQueue\Storage\Adapters\SqliteStorage;

beforeEach(function () {
    $this->tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'simple-php-queue-' . bin2hex(random_bytes(8));
    mkdir($this->tempDir, 0777, true);
});

afterEach(function () {
    $cleanup = function (string $path) use (&$cleanup): void {
        if (!is_dir($path)) {
            if (is_file($path)) {
                unlink($path);
            }

            return;
        }

        $entries = array_diff(scandir($path), ['.', '..']);

        foreach ($entries as $entry) {
            $childPath = $path . DIRECTORY_SEPARATOR . $entry;
            if (is_dir($childPath)) {
                $cleanup($childPath);
                continue;
            }

            unlink($childPath);
        }

        rmdir($path);
    };

    if (isset($this->tempDir) && is_dir($this->tempDir)) {
        $cleanup($this->tempDir);
    }
});

it('creates a sqlite database file when initialized with a directory path', function () {
    new SqliteStorage($this->tempDir);

    expect(is_file($this->tempDir . DIRECTORY_SEPARATOR . 'queue.db'))->toBeTrue();
});

it('stores values in fifo order and reports queue state', function () {
    $storage = new SqliteStorage($this->tempDir);

    expect($storage->length())->toBe(0);
    expect($storage->exist('first'))->toBeFalse();
    expect($storage->enqueue('first'))->toBeTrue();
    expect($storage->enqueue('second'))->toBeTrue();

    expect($storage->length())->toBe(2);
    expect($storage->exist('first'))->toBeTrue();
    expect($storage->exist('second'))->toBeTrue();
    expect($storage->content())->toBe([
        'first',
        'second',
    ]);

    expect($storage->dequeue())->toBe('first');
    expect($storage->dequeue())->toBe('second');
    expect($storage->dequeue())->toBeNull();
    expect($storage->length())->toBe(0);
});

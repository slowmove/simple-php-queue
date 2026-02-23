<?php

use Slowmove\SimplePhpQueue\Storage\Adapters\FileStorage;
use Slowmove\SimplePhpQueue\Storage\Adapters\SqliteStorage;

// ─── FileStorage ─────────────────────────────────────────────────────────────

describe('FileStorage', function () {
    beforeEach(function () {
        $this->file = tempnam(sys_get_temp_dir(), 'queue_file_') . '.txt';
        $this->storage = new FileStorage($this->file);
    });

    afterEach(function () {
        if (file_exists($this->file)) {
            unlink($this->file);
        }
    });

    it('peek returns null on an empty queue', function () {
        expect($this->storage->peek())->toBeNull();
    });

    it('peek returns the next item without removing it', function () {
        $this->storage->enqueue('first');
        $this->storage->enqueue('second');

        expect($this->storage->peek())->toBe('first');
        expect($this->storage->length())->toBe(2);
    });

    it('peek is idempotent — calling it twice returns the same item', function () {
        $this->storage->enqueue('only');

        expect($this->storage->peek())->toBe('only');
        expect($this->storage->peek())->toBe('only');
    });

    it('dequeue after peek removes the item that was peeked', function () {
        $this->storage->enqueue('alpha');
        $this->storage->enqueue('beta');

        $peeked = $this->storage->peek();
        $dequeued = $this->storage->dequeue();

        expect($peeked)->toBe($dequeued);
        expect($this->storage->length())->toBe(1);
    });

    it('peek returns null after all items are dequeued', function () {
        $this->storage->enqueue('sole');
        $this->storage->dequeue();

        expect($this->storage->peek())->toBeNull();
    });

    it('enqueue and dequeue work in FIFO order', function () {
        $this->storage->enqueue('one');
        $this->storage->enqueue('two');
        $this->storage->enqueue('three');

        expect($this->storage->dequeue())->toBe('one');
        expect($this->storage->dequeue())->toBe('two');
        expect($this->storage->dequeue())->toBe('three');
        expect($this->storage->dequeue())->toBeNull();
    });

    it('length reflects the current number of items', function () {
        expect($this->storage->length())->toBe(0);
        $this->storage->enqueue('x');
        expect($this->storage->length())->toBe(1);
        $this->storage->enqueue('y');
        expect($this->storage->length())->toBe(2);
        $this->storage->dequeue();
        expect($this->storage->length())->toBe(1);
    });

    it('exist returns true for an item in the queue', function () {
        $this->storage->enqueue('hello');
        expect($this->storage->exist('hello'))->toBeTrue();
    });

    it('exist returns false for an item not in the queue', function () {
        expect($this->storage->exist('ghost'))->toBeFalse();
    });
});

// ─── SqliteStorage ────────────────────────────────────────────────────────────

describe('SqliteStorage', function () {
    beforeEach(function () {
        $this->file = tempnam(sys_get_temp_dir(), 'queue_sqlite_') . '.db';
        $this->storage = new SqliteStorage($this->file);
    });

    afterEach(function () {
        if (file_exists($this->file)) {
            unlink($this->file);
        }
    });

    it('peek returns null on an empty queue', function () {
        expect($this->storage->peek())->toBeNull();
    });

    it('peek returns the next item without removing it', function () {
        $this->storage->enqueue('first');
        $this->storage->enqueue('second');

        expect($this->storage->peek())->toBe('first');
        expect($this->storage->length())->toBe(2);
    });

    it('peek is idempotent — calling it twice returns the same item', function () {
        $this->storage->enqueue('only');

        expect($this->storage->peek())->toBe('only');
        expect($this->storage->peek())->toBe('only');
    });

    it('dequeue after peek removes the item that was peeked', function () {
        $this->storage->enqueue('alpha');
        $this->storage->enqueue('beta');

        $peeked = $this->storage->peek();
        $dequeued = $this->storage->dequeue();

        expect($peeked)->toBe($dequeued);
        expect($this->storage->length())->toBe(1);
    });

    it('peek returns null after all items are dequeued', function () {
        $this->storage->enqueue('sole');
        $this->storage->dequeue();

        expect($this->storage->peek())->toBeNull();
    });

    it('enqueue and dequeue work in FIFO order', function () {
        $this->storage->enqueue('one');
        $this->storage->enqueue('two');
        $this->storage->enqueue('three');

        expect($this->storage->dequeue())->toBe('one');
        expect($this->storage->dequeue())->toBe('two');
        expect($this->storage->dequeue())->toBe('three');
        expect($this->storage->dequeue())->toBeNull();
    });

    it('length reflects the current number of items', function () {
        expect($this->storage->length())->toBe(0);
        $this->storage->enqueue('x');
        expect($this->storage->length())->toBe(1);
        $this->storage->enqueue('y');
        expect($this->storage->length())->toBe(2);
        $this->storage->dequeue();
        expect($this->storage->length())->toBe(1);
    });

    it('exist returns true for an item in the queue', function () {
        $this->storage->enqueue('hello');
        expect($this->storage->exist('hello'))->toBeTrue();
    });

    it('exist returns false for an item not in the queue', function () {
        expect($this->storage->exist('ghost'))->toBeFalse();
    });
});

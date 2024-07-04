<?php

namespace Slowmove\SimplePhpQueue\Storage\Adapters;

use Pheanstalk\Pheanstalk;
use Pheanstalk\Values\Job;
use Pheanstalk\Values\TubeName;
use Pheanstalk\Values\TubeStats;
use Slowmove\SimplePhpQueue\Storage\StorageInterface;

class BeanstalkdStorage implements StorageInterface
{
  const DEFAULT_STORAGE_PATH = '127.0.0.1';
  const DEFAULT_STORAGE_PORT = 11300;
  const DEFAULT_STORAGE_NAME = 'queue';

  private Pheanstalk $beanstalkdClient;
  private TubeName $tube;

  public function __construct(string $storagePath)
  {
    $host = self::DEFAULT_STORAGE_PATH;
    $port = self::DEFAULT_STORAGE_PORT;

    if ($storagePath && strpos($storagePath, ":") > -1) {
      $connectionString = explode(':', $storagePath);
      $host = $connectionString[0];
      $port = $connectionString[1];
    } else if ($storagePath) {
      $host = $storagePath;
    }

    $this->beanstalkdClient = Pheanstalk::create($host, $port);
    $this->tube = new TubeName(self::DEFAULT_STORAGE_NAME);
  }

  public function enqueue(string $data): bool
  {
    $this->beanstalkdClient->useTube($this->tube);
    $jobId = $this->beanstalkdClient->put($data);
    return !!$jobId;
  }

  public function dequeue(): ?string
  {
    $this->beanstalkdClient->watch($this->tube);
    $job = $this->beanstalkdClient->reserveWithTimeout(5);
    if ($job instanceof Job) {
      $retval = $job->getData();
      $this->beanstalkdClient->delete($job);
      return $retval;
    }
    return null;
  }

  public function exist(string $value): bool
  {
    throw new \Exception('Not implemented yet');
  }

  public function length(): int
  {
    try {
      $tubeStats = $this->beanstalkdClient->statsTube($this->tube);
      if ($tubeStats instanceof TubeStats) {
        return $tubeStats->totalJobs;
      }
      return 0;
    } catch (\Throwable $th) {
      return 0;
    }
  }

  public function content(): array
  {
    if ($this->length() === 0) {
      return [];
    }
    $jobs = [];
    $items = [];
    for ($i = 0; $i < $this->length(); $i++) {
      $this->beanstalkdClient->watch($this->tube);
      $job = $this->beanstalkdClient->reserve();
      $items[$i] = $job->getData();
      $jobs[] = $job;
    }
    foreach ($jobs as $job) {
      $this->beanstalkdClient->release($job);
    }
    return $items;
  }
}

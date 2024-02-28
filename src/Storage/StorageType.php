<?php

namespace Slowmove\SimplePhpQueue\Storage;

enum StorageType: string
{
  case FILE = 'file';
  case SQLITE = 'sqlite';
  case REDIS = 'redis';
  case BEANSTALKD = 'beanstalkd';
}

<?php

arch("Adapters should implement Storage Interface")
  ->expect("Slowmove\SimplePhpQueue\Storage\Adapters")
  ->toImplement("Slowmove\SimplePhpQueue\Storage\StorageInterface");

arch("Adapters should be named with Storage as suffix")
  ->expect("Slowmove\SimplePhpQueue\Storage\Adapters")
  ->toHaveSuffix("Storage");

<?php

arch("Adapters should implement Storage Interface")
  ->expect("Hoffman\SimplePhpQueue\Storage\Adapters")
  ->toImplement("Hoffman\SimplePhpQueue\Storage\StorageInterface");

arch("Adapters should be named with Storage as suffix")
  ->expect("Hoffman\SimplePhpQueue\Storage\Adapters")
  ->toHaveSuffix("Storage");

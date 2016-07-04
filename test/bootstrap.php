<?php

require __DIR__ . '/../vendor/autoload.php';

$loader = new \Aura\Autoload\Loader();
$loader->addPrefix('test\jmversteeg\contextual', __DIR__ . '/jmversteeg/contextual');
$loader->addPrefix('jmversteeg\contextual', __DIR__ . '/../src/jmversteeg/contextual');
$loader->register();
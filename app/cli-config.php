<?php

/** @var Container $container */

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use UMA\DIC\Container;

$container = require_once __DIR__ . '/bootstrap.php';

return ConsoleRunner::createHelperSet($container->get(EntityManager::class));
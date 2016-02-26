<?php

$finder = Symfony\CS\Finder\DefaultFinder::create();
$finder->in([
    __DIR__ . '/src',
    __DIR__ . '/tests/unit'
]);

$config = Symfony\CS\Config\Config::create();
$config->setUsingCache(true);
$config->setUsingLinter(false);
$config->finder($finder);
$config->level(Symfony\CS\FixerInterface::PSR2_LEVEL);

return $config;
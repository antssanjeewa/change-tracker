<?php
// generate-changelog.php
require_once __DIR__ . '/ChangeTracker.php';

$tracker = new ChangeTracker();

$options = [
  'dir' => $argv[1] ?? 'app',
  'output' => $argv[2] ?? 'changelog.html',
];

$tracker->generateApiDoc($options);
<?php

namespace ChangeTracker;

class Parser
{
  public function parseFile(string $content, string $filename): array
  {
    $methods = [];
    $pattern = '/\/\*\*(.*?)\*\/\s*(?:public|private|protected)\s+function\s+(\w+)/s';

    preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

    foreach ($matches as $match) {
      $docblock = $match[1];
      $methodName = $match[2];

      $changes = $this->parseChangesFromDocblock($docblock);
      if (!empty($changes)) {
        $methods[] = [
          'method' => $methodName,
          'file' => basename($filename),
          'changes' => $changes
        ];
      }
    }

    return $methods;
  }

  private function parseChangesFromDocblock(string $docblock): array
  {
    $changes = [];
    $lines = explode("\n", $docblock);
    $currentBranch = 'main';

    foreach ($lines as $line) {
      $line = trim($line, " *");
      if (empty($line))
        continue;

      if (preg_match('/\*\*\s*(.+?)\s*\*\*/', $line, $m)) {
        $currentBranch = trim($m[1]);
        continue;
      }

      if (preg_match('/(\d{4}-\d{2}-\d{2})\s+(.+)/', $line, $m)) {
        $changes[] = [
          'date' => $m[1],
          'branch' => $currentBranch,
          'description' => trim($m[2])
        ];
      }
    }

    return $changes;
  }
}
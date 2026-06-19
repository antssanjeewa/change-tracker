<?php

namespace ChangeTracker;

class ChangeTracker
{
  private Parser $parser;
  private HtmlGenerator $htmlGenerator;

  public function __construct()
  {
    $this->parser = new Parser();
    $this->htmlGenerator = new HtmlGenerator();
  }

  public function generateApiDoc(array $options = []): void
  {
    $dirs = $options['dirs'] ?? config('change-tracker.scan_directories', ['app']);
    $output = $options['output'] ?? config('change-tracker.default_output', 'changelog.html');

    echo "🔍 Scanning directories for documentation...\n";

    $controllers = [];

    foreach ((array) $dirs as $dir) {
      echo "   → Scanning: {$dir}\n";
      $files = $this->getPhpFiles($dir);

      foreach ($files as $file) {
        $content = file_get_contents($file);
        $methods = $this->parser->parseFile($content, $file);

        if (!empty($methods)) {
          $className = basename($file, '.php');
          $controllers[$className] = $methods;
        }
      }
    }

    if (empty($controllers)) {
      echo "⚠️  No documented methods found.\n";
      return;
    }

    $html = $this->htmlGenerator->generate($controllers);
    file_put_contents($output, $html);

    echo "✅ Beautiful API Documentation generated successfully!\n";
    echo "📄 Output: " . realpath($output) . "\n";
  }

  private function getPhpFiles(string $dir): array
  {
    $files = [];
    if (!is_dir($dir)) {
      return $files;
    }

    $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));

    foreach ($iterator as $item) {
      if ($item->isFile() && $item->getExtension() === 'php') {
        $files[] = $item->getPathname();
      }
    }
    return $files;
  }
}
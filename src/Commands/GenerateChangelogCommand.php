<?php

namespace ChangeTracker\Commands;

use Illuminate\Console\Command;
use ChangeTracker\ChangeTracker;

class GenerateChangelogCommand extends Command
{
  protected $signature = 'generate:changelog 
                            {dirs? : Comma separated directories to scan (default from config)}
                            {output=changelog.html : Output HTML file name}';

  protected $description = 'Generate beautiful HTML API ChangeLog Documentation from custom docblocks';

  public function handle()
  {
    $dirsInput = $this->argument('dirs');
    $output = $this->argument('output');

    $dirs = $dirsInput
      ? explode(',', str_replace(' ', '', $dirsInput))
      : config('change-tracker.scan_directories');

    $this->info("🔍 Starting documentation generation...");

    $tracker = new ChangeTracker();

    $tracker->generateApiDoc([
      'dirs' => $dirs,
      'output' => $output
    ]);

    $this->info("✅ Done!");
  }
}
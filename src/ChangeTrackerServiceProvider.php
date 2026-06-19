<?php

namespace ChangeTracker;

use Illuminate\Support\ServiceProvider;
use ChangeTracker\Commands\GenerateChangelogCommand;

class ChangeTrackerServiceProvider extends ServiceProvider
{
  public function register()
  {
    $this->mergeConfigFrom(__DIR__ . '/../config/change-tracker.php', 'change-tracker');
  }

  public function boot()
  {
    if ($this->app->runningInConsole()) {
      $this->commands([
        GenerateChangelogCommand::class,
      ]);

      $this->publishes([
        __DIR__ . '/../config/change-tracker.php' => config_path('change-tracker.php'),
      ], 'change-tracker-config');
    }
  }
}
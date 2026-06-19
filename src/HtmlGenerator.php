<?php

namespace ChangeTracker;

class HtmlGenerator
{
  public function generate(array $controllers): string
  {
    $html = $this->getHeader();
    $html .= $this->getSidebar($controllers);
    $html .= $this->getMainContent($controllers);
    $html .= $this->getFooter();

    return $html;
  }

  private function getHeader(): string
  {
    return <<<HTML
<!DOCTYPE html>
<html lang="si" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API ChangeLog Documentation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: system-ui, sans-serif; }
        .sidebar { position: fixed; top: 0; left: 0; height: 100vh; overflow-y: auto; }
        .topbar { position: fixed; top: 0; left: 20rem; right: 0; z-index: 50; }
        .main-content { margin-left: 20rem; padding-top: 4rem; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
<div class="flex min-h-screen">
HTML;
  }

  private function getSidebar(array $controllers): string
  {
    $html = <<<HTML
    <!-- Fixed Sidebar -->
    <div class="sidebar w-80 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 p-6">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 flex items-center gap-2">
                <i class="fas fa-code"></i> API ChangeLog
            </h1>
        </div>
        <div class="mb-4 text-sm font-medium text-gray-500 dark:text-gray-400">CONTROLLERS</div>
        <ul class="space-y-1">
HTML;

    foreach (array_keys($controllers) as $index => $controller) {
      $active = $index === 0 ? 'bg-indigo-50 dark:bg-gray-700 text-indigo-700 dark:text-indigo-300' : '';
      $html .= "<li>
                <a href='#{$controller}' onclick='scrollToController(\"{$controller}\")' 
                   class='block px-4 py-3 rounded-xl transition-colors {$active} hover:bg-indigo-50 dark:hover:bg-gray-700'>
                    {$controller}
                </a>
            </li>";
    }

    $html .= "</ul></div>";
    return $html;
  }

  private function getMainContent(array $controllers): string
  {
    $html = <<<HTML
    <!-- Fixed Topbar -->
    <div class="topbar bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-8 py-4 flex items-center justify-between">
        <div class="font-medium">API Documentation</div>
        <div class="flex items-center gap-4">
            <button onclick="toggleDarkMode()" class="px-4 py-2 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600">
                <i class="fas fa-moon dark:hidden"></i>
                <i class="fas fa-sun hidden dark:inline"></i>
            </button>
            <span class="text-sm text-gray-500 dark:text-gray-400" id="generated-date"></span>
        </div>
    </div>

    <!-- Scrollable Content -->
    <div class="main-content flex-1 p-8 mt-20">
        <div class="max-w-5xl mx-auto">
HTML;

    foreach ($controllers as $controller => $methods) {
      $html .= "
            <div id='{$controller}' class='mb-12 scroll-mt-20'>
                <div onclick='toggleController(this)' class='controller-header flex items-center justify-between bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 mb-4 cursor-pointer'>
                    <h2 class='text-2xl font-semibold text-indigo-700 dark:text-indigo-400'>{$controller}</h2>
                    <i class='fas fa-chevron-down transition-transform'></i>
                </div>
                <div class='controller-content space-y-6'>";

      foreach ($methods as $m) {
        $html .= $this->getMethodHtml($m);
      }
      $html .= "</div></div>";
    }

    return $html;
  }

  private function getMethodHtml(array $method): string
  {
    $html = "
                <div class='bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 border border-gray-100 dark:border-gray-700'>
                    <div class='flex justify-between items-center mb-5'>
                        <span class='font-mono text-xl font-bold text-indigo-600 dark:text-indigo-400'>{$method['method']}()</span>
                        <span class='text-sm text-gray-500 dark:text-gray-400'>{$method['file']}</span>
                    </div>
                    <table class='w-full border-collapse'>
                        <thead>
                            <tr class='bg-gray-100 dark:bg-gray-700'>
                                <th class='p-3 text-left rounded-tl-xl'>Date</th>
                                <th class='p-3 text-left'>Branch</th>
                                <th class='p-3 text-left rounded-tr-xl'>Description</th>
                            </tr>
                        </thead>
                        <tbody class='divide-y divide-gray-100 dark:divide-gray-700'>";

    foreach ($method['changes'] as $c) {
      $html .= "
                            <tr>
                                <td class='p-3 font-medium'>{$c['date']}</td>
                                <td class='p-3'><span class='px-3 py-1 bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300 rounded-full text-xs'>{$c['branch']}</span></td>
                                <td class='p-3 text-gray-700 dark:text-gray-300'>{$c['description']}</td>
                            </tr>";
    }

    $html .= "</tbody></table></div>";
    return $html;
  }

  private function getFooter(): string
  {
    return <<<HTML
        </div>
    </div>
</div>

<script>
    function toggleDarkMode() {
        document.documentElement.classList.toggle('dark');
        localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'));
    }

    if (localStorage.getItem('darkMode') === 'true' || window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.documentElement.classList.add('dark');
    }

    document.getElementById('generated-date').textContent = new Date().toLocaleDateString('si-LK', { year: 'numeric', month: 'long', day: 'numeric' });

    function scrollToController(id) {
        document.getElementById(id).scrollIntoView({ behavior: 'smooth' });
    }

    function toggleController(header) {
        const content = header.nextElementSibling;
        const icon = header.querySelector('i');
        content.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    }

    document.addEventListener('DOMContentLoaded', () => {
        const contents = document.querySelectorAll('.controller-content');
        contents.forEach((content, i) => { if (i !== 0) content.classList.add('hidden'); });
    });
</script>
</body>
</html>
HTML;
  }
}
# Change Tracker - Laravel API Documentation Generator

Read your custom docblock comments and generate HTML API ChangeLog Documentation.

## Features

- Custom comment format parsing (`/** ... @Changes ... */`)
- Beautiful Tailwind CSS HTML output
- Laravel Artisan Command සහායෙන් භාවිතය
- Config file support (publishable)
- Sidebar navigation සහිත Modern UI

## Installation

```bash
composer require antssanjeewa/change-tracker:dev-main

```

## Usage

### Artisan Command භාවිතය

```bash
# Default භාවිතය
php artisan generate:changelog

# Custom options සමග
php artisan generate:changelog app/Http/Controllers public/api-docs.html
```

### Config භාවිතය (Optional)

```bash
php artisan vendor:publish --tag=change-tracker-config
```

ඊට පසු `config/change-tracker.php` ගොනුවේ default values වෙනස් කළ හැක.

## Example Comment Format

```php
/**
 *         STORE
 *------------------------
 *
 * @Changes
 *  ** report **
 *      2023-02-28      Create Request file and move Validation
 *  ** feature/request_23_04 **
 *      2023-03-07      Add Update Member ID Function
 */
public function store(StoreUserRequest $request)
```

## Publishing Assets (Optional)

```bash
php artisan vendor:publish --tag=change-tracker-config
```

## License

MIT License

---

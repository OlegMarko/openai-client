# OpenAI PHP Client

![CI](https://github.com/OlegMarko/openai-client/actions/workflows/ci.yml/badge.svg)
![Packagist Version](https://img.shields.io/packagist/v/fixik/openai-client)
![Total Downloads](https://poser.pugx.org/fixik/openai-client/downloads)
![Coverage](https://codecov.io/gh/OlegMarko/openai-client/branch/main/graph/badge.svg)
![License](https://poser.pugx.org/fixik/openai-client/license)

A powerful PHP client for the OpenAI API with support for:
- Chat
- Embeddings
- Images
- Fine-tuning

## Installation
```bash
composer require fixik/openai-client
```

## Publish config (Laravel):
```bash
php artisan vendor:publish --provider="Fixik\OpenAI\Laravel\OpenAIServiceProvider"
```

## Add to .env:
```bash
OPENAI_API_KEY=your_key
```

## Usage Example PHP
```php
use Fixik\OpenAI\OpenAIClient;

$apiKey = 'YOUR_OPENAI_API_KEY_HERE';

$openaiClient = new OpenAIClient($apiKey);

$message = "Write a haiku about modern PHP development.";

$openaiClient->chat()->send('gpt-3.5-turbo', $message);
$openaiClient->embeddings()->create('gpt-3.5-turbo', $message);
$openaiClient->fineTune()->create([
    'model' => 'gpt-3.5-turbo',
    'training_file' => 'file-id',
]);
$openaiClient->fineTune()->retrieve($jobId);
$openaiClient->images()->generate($message);
```

## Usage Example Laravel
```php
$message = "Write a haiku about modern PHP development.";

OpenAI::chat()->send('gpt-3.5-turbo', $message);
OpenAI::embeddings()->create('gpt-3.5-turbo', $message);
OpenAI::fineTune()->create([
    'model' => 'gpt-3.5-turbo',
    'training_file' => 'file-id',
]);
OpenAI::fineTune()->retrieve($jobId);
OpenAI::images()->generate($message);
```

## Testing

### Run Pest:
```bash
./vendor/bin/pest
```

## Static Analyzers

### Run PHPStan:
```bash
./vendor/bin/phpstan analyse src --level=max
```

### Run Psalm:
```bash
./vendor/bin/psalm --no-cache
```

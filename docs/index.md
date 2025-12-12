# OpenAI PHP Client

A powerful PHP SDK for OpenAI chat, embeddings, images, fine-tuning + Laravel support.

![CI](https://github.com/OlegMarko/openai-client/actions/workflows/ci.yml/badge.svg)
![Packagist Version](https://img.shields.io/packagist/v/fixik/openai-client)
[![Total Downloads](https://poser.pugx.org/fixik/openai-client/downloads)](https://packagist.org/packages/fixik/openai-client)
![Coverage](https://codecov.io/gh/OlegMarko/openai-client/branch/main/graph/badge.svg)
![License](https://poser.pugx.org/fixik/openai-client/license)

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

## Usage Example
```php
use Fixik\OpenAI;

$response = OpenAI::chat->send('gpt-4.1', 'Hello AI');
echo $response;
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

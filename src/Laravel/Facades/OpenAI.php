<?php

namespace Fixik\OpenAI\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Fixik\OpenAI\OpenAIClient;
use Fixik\OpenAI\Services\ChatService;
use Fixik\OpenAI\Services\EmbeddingService;
use Fixik\OpenAI\Services\FineTuneService;
use Fixik\OpenAI\Services\ImageService;

/**
 * @method static ChatService chat()
 * @method static EmbeddingService embeddings()
 * @method static FineTuneService fineTune()
 * @method static ImageService images()
 * @see OpenAIClient
 */
class OpenAI extends Facade
{
    #[\Override]
    protected static function getFacadeAccessor(): string
    {
        return 'openai';
    }
}

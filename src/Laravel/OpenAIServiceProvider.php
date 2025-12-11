<?php

namespace Fixik\OpenAI\Laravel;

use Illuminate\Support\ServiceProvider;
use Fixik\OpenAI\OpenAIClient;

class OpenAIServiceProvider extends ServiceProvider
{
    #[\Override]
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/openai.php', 'openai');
        $this->app->singleton(OpenAIClient::class, function ($app) {
            return new OpenAIClient(config('openai.api_key'));
        });
        $this->app->alias(OpenAIClient::class, 'openai');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/openai.php' => config_path('openai.php')
        ]);
    }
}

<?php

namespace Fixik\OpenAI\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class OpenAI extends Facade
{
    #[\Override]
    protected static function getFacadeAccessor(): string
    {
        return 'openai';
    }
}

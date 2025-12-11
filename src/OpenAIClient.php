<?php

namespace Fixik\OpenAI;

use GuzzleHttp\Client;
use Fixik\OpenAI\Services\ChatService;
use Fixik\OpenAI\Services\EmbeddingService;
use Fixik\OpenAI\Services\FineTuneService;
use Fixik\OpenAI\Services\ImageService;

class OpenAIClient
{
    protected Client $http;
    protected string $apiKey;
    protected string $baseUrl = 'https://api.openai.com/v1/';

    public ChatService $chat;
    public EmbeddingService $embeddings;
    public FineTuneService $fineTune;
    public ImageService $images;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->http = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json'
            ]
        ]);
        $this->chat = new ChatService($this->http);
        $this->embeddings = new EmbeddingService($this->http);
        $this->fineTune = new FineTuneService($this->http);
        $this->images = new ImageService($this->http);
    }
}

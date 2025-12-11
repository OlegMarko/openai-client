<?php

namespace Fixik\OpenAI\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ImageService
{
    public function __construct(protected Client $http) {}

    /**
     * @throws GuzzleException
     */
    public function generate(string $prompt, string $size = '1024x1024'): array
    {
        $r = $this->http->post('images/generations', ['json' => ['prompt' => $prompt, 'size' => $size]]);

        return json_decode($r->getBody(), true);
    }
}

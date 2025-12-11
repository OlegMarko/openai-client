<?php

namespace Fixik\OpenAI\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class EmbeddingService
{
    public function __construct(protected Client $http) {}

    /**
     * @throws GuzzleException
     */
    public function create(string $model, string $input): array
    {
        $res = $this->http->post('embeddings', ['json' => ['model' => $model, 'input' => $input]]);

        return json_decode($res->getBody(), true);
    }
}

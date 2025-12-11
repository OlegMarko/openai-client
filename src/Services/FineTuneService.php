<?php

namespace Fixik\OpenAI\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class FineTuneService
{
    public function __construct(protected Client $http) {}

    /**
     * @throws GuzzleException
     */
    public function create(array $params): array
    {
        $r = $this->http->post('fine_tuning/jobs', ['json' => $params]);

        return json_decode($r->getBody(), true);
    }

    /**
     * @throws GuzzleException
     */
    public function retrieve(string $id): array
    {
        $r = $this->http->get("fine_tuning/jobs/{$id}");

        return json_decode($r->getBody(), true);
    }
}

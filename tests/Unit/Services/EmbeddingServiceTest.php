<?php

namespace Tests\Unit\Services;

use Fixik\OpenAI\Services\EmbeddingService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

it('sends the correct request to the API and returns decoded response', function () {
    $expectedResponseData = [
        'object' => 'list',
        'data' => [
            [
                'object' => 'embedding',
                'embedding' => [0.00938, -0.0035, 0.00041],
                'index' => 0,
            ],
        ],
        'model' => 'text-embedding-ada-002',
        'usage' => ['prompt_tokens' => 8, 'total_tokens' => 8],
    ];

    $mockHandler = new MockHandler([
        new Response(200, ['Content-Type' => 'application/json'], json_encode($expectedResponseData)),
    ]);
    $handlerStack = HandlerStack::create($mockHandler);
    $mockHttpClient = new Client(['handler' => $handlerStack]);

    $service = new EmbeddingService($mockHttpClient);

    $model = 'text-embedding-ada-002';
    $input = 'Hello, world!';

    $result = $service->create($model, $input);

    expect($result)->toBeArray()
        ->and($result)->toEqual($expectedResponseData);

    $lastRequest = $mockHandler->getLastRequest();
    expect((string)$lastRequest->getUri())->toBe('embeddings')
        ->and($lastRequest->getHeaderLine('Content-Type'))->toBe('application/json');

    $requestBody = json_decode((string)$lastRequest->getBody(), true);
    expect($requestBody['model'])->toBe($model)
        ->and($requestBody['input'])->toBe($input);

})->group('openai-service', 'embedding-service');

it('throws GuzzleException when the API call fails', function () {
    $mockHandler = new MockHandler([
        new Response(500, [], 'Internal Server Error'),
    ]);
    $handlerStack = HandlerStack::create($mockHandler);
    $mockHttpClient = new Client(['handler' => $handlerStack]);

    $service = new EmbeddingService($mockHttpClient);

    $service->create('some-model', 'some-input');

})->throws(ServerException::class)->group('openai-service', 'embedding-service', 'exception');
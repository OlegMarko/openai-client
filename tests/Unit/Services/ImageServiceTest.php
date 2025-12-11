<?php

namespace Tests\Unit\Services;

use Fixik\OpenAI\Services\ImageService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;

it('sends the correct parameters and returns decoded image generation response on success', function () {
    $expectedImageUrl = 'https://oaidalleapiprodscus.blob.core.windows.net/private/org-abc/user-xyz/image_1234.png';
    $expectedResponseData = [
        'created' => 1677656950,
        'data' => [
            ['url' => $expectedImageUrl],
        ],
    ];

    $mock = new MockHandler([
        new Response(200, ['Content-Type' => 'application/json'], json_encode($expectedResponseData))
    ]);

    $history = [];
    $historyMiddleware = Middleware::history($history);

    $handlerStack = HandlerStack::create($mock);
    $handlerStack->push($historyMiddleware);

    $client = new Client(['handler' => $handlerStack, 'base_uri' => 'https://api.openai.com/v1/']);
    $service = new ImageService($client);

    $prompt = 'A hyperrealistic photo of a cat wearing a tiny crown.';
    $size = '512x512';

    $result = $service->generate($prompt, $size);

    expect($result)
        ->toBeArray()
        ->toEqual($expectedResponseData)
        ->and($result['data'][0]['url'])->toBe($expectedImageUrl)
        ->and($history)->toHaveCount(1, 'A request was not sent to the Guzzle client.');

    /** @var RequestInterface $request */
    $request = $history[0]['request'];

    expect($request->getUri()->getPath())->toBe('/v1/images/generations')
        ->and($request->getMethod())->toBe('POST');

    $body = json_decode((string)$request->getBody(), true);
    expect($body)
        ->toHaveKey('prompt')
        ->toHaveKey('size')
        ->and($body['prompt'])->toBe($prompt)
        ->and($body['size'])->toBe($size);
});

it('throws GuzzleException when the API returns an error status during image generation', function () {
    $apiErrorResponse = [
        'error' => [
            'message' => 'Your request was blocked due to a content violation.',
            'type' => 'invalid_request_error',
            'code' => 'content_policy_violation',
        ],
    ];

    $mock = new MockHandler([
        new Response(400, ['Content-Type' => 'application/json'], json_encode($apiErrorResponse))
    ]);

    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);
    $service = new ImageService($client);

    $service->generate('an unsafe prompt that will fail');

})->throws(ClientException::class);
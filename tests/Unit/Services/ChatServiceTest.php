<?php

namespace Tests\Unit\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Middleware;
use Fixik\OpenAI\Services\ChatService;
use Psr\Http\Message\RequestInterface;

it('chat service returns the extracted content on successful API call', function () {
    $expectedContent = 'This is the AI response content.';

    $apiResponseData = [
        'id' => 'chatcmpl-1234567890',
        'object' => 'chat.completion',
        'created' => 1677656950,
        'model' => 'gpt-3.5-turbo',
        'choices' => [
            [
                'index' => 0,
                'message' => [
                    'role' => 'assistant',
                    'content' => $expectedContent,
                ],
                'finish_reason' => 'stop',
            ],
        ],
        'usage' => [
            'prompt_tokens' => 10,
            'completion_tokens' => 20,
            'total_tokens' => 30,
        ],
    ];

    $mock = new MockHandler([
        new Response(200, [], json_encode($apiResponseData))
    ]);

    $history = [];
    $historyMiddleware = Middleware::history($history);

    $handlerStack = HandlerStack::create($mock);
    $handlerStack->push($historyMiddleware);

    $client = new Client(['handler' => $handlerStack, 'base_uri' => 'https://api.openai.com/v1/']); // Add base_uri for full assertion

    $service = new ChatService($client);
    $result = $service->send('gpt-3.5-turbo', 'What is Pest PHP?');

    expect($result)
        ->toBeString()
        ->toBe($expectedContent)
        ->and($history)->toHaveCount(1, 'A request was not sent to the Guzzle client.');

    /** @var RequestInterface $request */
    $request = $history[0]['request'];

    expect($request->getUri()->getPath())->toBe('/v1/chat/completions')
        ->and($request->getMethod())->toBe('POST');

    $body = json_decode((string)$request->getBody(), true);
    expect($body)
        ->toHaveKey('model')
        ->toHaveKey('messages')
        ->and($body['model'])->toBe('gpt-3.5-turbo')
        ->and($body['messages'][0]['content'])->toBe('What is Pest PHP?');
});

it('throws GuzzleException when the API returns an error status', function () {
    $apiErrorResponse = [
        'error' => [
            'message' => 'Incorrect API key provided.',
            'type' => 'invalid_request_error',
            'param' => null,
            'code' => 'invalid_api_key',
        ],
    ];

    $mock = new MockHandler([
        new Response(400, ['Content-Type' => 'application/json'], json_encode($apiErrorResponse))
    ]);

    $handlerStack = HandlerStack::create($mock);

    $client = new Client(['handler' => $handlerStack]);
    $service = new ChatService($client);

    $service->send('gpt-3.5-turbo', 'Try to send a message with a bad key.');

})->throws(ClientException::class);
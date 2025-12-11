<?php

namespace Tests\Unit\Services;

use Fixik\OpenAI\Services\FineTuneService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;

it('sends the correct parameters for creating a fine-tuning job and returns decoded response', function () {
    $jobId = 'ftjob-1234567890abcdef';
    $expectedResponseData = [
        'object' => 'fine_tuning.job',
        'id' => $jobId,
        'model' => 'gpt-3.5-turbo-0613',
        'status' => 'validating_files',
        'training_file' => 'file-abcdef1234567890',
    ];

    $mock = new MockHandler([
        new Response(200, ['Content-Type' => 'application/json'], json_encode($expectedResponseData))
    ]);

    $history = [];
    $handlerStack = HandlerStack::create($mock);
    $handlerStack->push(Middleware::history($history));

    $client = new Client(['handler' => $handlerStack, 'base_uri' => 'https://api.openai.com/v1/']);
    $service = new FineTuneService($client);

    $params = [
        'model' => 'gpt-3.5-turbo',
        'training_file' => 'file-abcdef1234567890',
    ];

    $result = $service->create($params);

    expect($result)
        ->toBeArray()
        ->toEqual($expectedResponseData)
        ->and($result['id'])->toBe($jobId);

    /** @var RequestInterface $request */
    $request = $history[0]['request'];
    expect($request->getUri()->getPath())->toBe('/v1/fine_tuning/jobs')
        ->and($request->getMethod())->toBe('POST');

    $body = json_decode((string)$request->getBody(), true);
    expect($body)->toEqual($params);
});

it('throws GuzzleException when fine-tuning create API call fails', function () {
    $mock = new MockHandler([
        new Response(400, ['Content-Type' => 'application/json'], json_encode(['error' => ['message' => 'Invalid file ID.']]))
    ]);

    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);
    $service = new FineTuneService($client);

    $service->create(['model' => 'gpt-3.5-turbo', 'training_file' => 'invalid-file-id']);

})->throws(ClientException::class);

it('sends the correct ID and returns job status on successful retrieve API call', function () {
    $jobId = 'ftjob-1234567890abcdef';
    $expectedResponseData = [
        'object' => 'fine_tuning.job',
        'id' => $jobId,
        'model' => 'gpt-3.5-turbo-0613',
        'status' => 'succeeded',
    ];

    $mock = new MockHandler([
        new Response(200, ['Content-Type' => 'application/json'], json_encode($expectedResponseData))
    ]);

    $history = [];
    $handlerStack = HandlerStack::create($mock);
    $handlerStack->push(Middleware::history($history));

    $client = new Client(['handler' => $handlerStack, 'base_uri' => 'https://api.openai.com/v1/']);
    $service = new FineTuneService($client);

    $result = $service->retrieve($jobId);

    expect($result)
        ->toBeArray()
        ->toEqual($expectedResponseData)
        ->and($result['status'])->toBe('succeeded');

    /** @var RequestInterface $request */
    $request = $history[0]['request'];
    $expectedPath = "/v1/fine_tuning/jobs/{$jobId}";

    expect($request->getUri()->getPath())->toBe($expectedPath)
        ->and($request->getMethod())->toBe('GET');
});

it('throws GuzzleException when fine-tuning retrieve API call fails (e.g., job not found)', function () {
    $jobId = 'non-existent-job';

    $mock = new MockHandler([
        new Response(404, ['Content-Type' => 'application/json'], json_encode(['error' => ['message' => 'The job was not found.']]))
    ]);

    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);
    $service = new FineTuneService($client);

    $service->retrieve($jobId);

})->throws(ClientException::class);
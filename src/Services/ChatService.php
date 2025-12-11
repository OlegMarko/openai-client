<?php

namespace Fixik\OpenAI\Services;

use GuzzleHttp\Client;
use Fixik\OpenAI\Helpers\ResponseHandler;
use GuzzleHttp\Exception\GuzzleException;

class ChatService
{
    public function __construct(protected Client $http) {}

    /**
     * @throws GuzzleException
     */
    public function send(string $model, string $message)
    {
        $res = $this->http->post('chat/completions', [
            'json' => [
                'model' => $model,
                'messages' => [['role' => 'user', 'content' => $message]]
            ]
        ]);

        $data = json_decode($res->getBody(), true);

        return ResponseHandler::get($data, 'choices.0.message.content', '');
    }
}

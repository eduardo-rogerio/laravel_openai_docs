<?php

namespace App\AI;

use OpenAI\Laravel\Facades\OpenAI;

class Chat
{
    protected array $messages = [];

    public function messages(): array
    {
        return $this->messages;
    }

    public function reply(string $message): ?string
    {
        return $this->send($message);
    }

    public function send(string $message): ?string
    {
        $this->messages[] = [
            "role" => "user",
            "content" => $message,
        ];
        $response = OpenAi::chat()
            ->create([
                    "model" => "gpt-3.5-turbo",
                    "messages" => $this->messages,
                ]
            )->choices[0]->message->content;

        if ($response) {
            $this->messages[] = [
                "role" => "assistant",
                "content" => $response,
            ];
        }

        return $response;
    }

    public function systemMessage(string $message): static
    {
        $this->messages[] = [
            "role" => "system",
            "content" => $message,
        ];

        return $this;
    }
}
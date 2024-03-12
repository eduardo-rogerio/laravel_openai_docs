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

    public function send(string $message, bool $speech = false): ?string
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

        return $speech ? $this->speech($response) : $response;
    }

    public function speech(string $message)
    {
        return OpenAi::audio()
            ->speech([
                'model' => 'tts-1',
                'input' => $message,
                "voice" => "alloy",
            ]);
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
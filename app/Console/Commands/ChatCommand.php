<?php

namespace App\Console\Commands;

use App\AI\Assistant;
use Illuminate\Console\Command;
use function Laravel\Prompts\{info, spin, text};

class ChatCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat {--system=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicie um Bate-papo com OpenAi';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $chat = new Assistant();
        if ($this->option('system')) {
            $chat->systemMessage($this->option('system'));
        }
        $question = text(
            'Olá, eu sou um assistente de bate-papo. Como posso ajudar você?',
            required: true
        );
        $response = spin(fn() => $question ? $chat->send($question) : '', 'Enviando pergunta...');
        info($response);

        while ($question = text('Qual é a sua próxima pergunta?')) {
            $response = spin(fn() => $question ? $chat->send($question) : '', 'Enviando pergunta...');
            info($response);
        }
        info('Obrigado por usar o assistente de bate-papo!');
    }
}

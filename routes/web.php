<?php

use Illuminate\Support\Facades\Route;
use OpenAI\Laravel\Facades\OpenAI;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Route::get('/', function () {
//    $chat = new Assistant();
//    $chat->systemMessage('You are a poetic assistant, skilled in explaining complex programming concepts with creative flair.')
//        ->send('Compose a poem that explains the concept of recursion in programming.');
//    $sillyPoem = $chat->reply('Cool, can you make it much, much sillier.');
//
//    return view('welcome', ['response' => $sillyPoem]);
//});
//
//Route::get('/home', function () {
//    return view('roast');
//});
//
//Route::post('/roast', static function () {
//    $attributes = request()->validate([
//        'topic' => ['required', 'string', 'min:3', 'max:50'],
//    ]);
//
//    $prompt = "Please roast {$attributes['topic']} in a sarcastic tone.";
//
//    $mp3 = (new Assistant())->send(message: $prompt, speech: true);
//
//    $name = md5($mp3);
//    Storage::disk('local')
//        ->put('roasts/' . $name . '.mp3', $mp3);
//
//    return redirect('/home')->with([
//        'file' => $name . '.mp3',
//        'flash' => 'Audio gerado com sucesso!',
//    ]);
//});
//
//Route::get('/image', function () {
//    return view('image', [
//        'messages' => session('messages', []),
//    ]);
//});
//
//Route::post('/getimage', static function () {
//    $attributes = request()->validate([
//        'description' => ['required', 'string', 'min:3'],
//    ]);
//
//    $assistant = new Assistant(session('messages', []));
//
//    $assistant->visualize($attributes['description']);
//
//    session(['messages' => $assistant->messages()]);
//
//    return redirect('/image');
//});

Route::get('/', static function () {

    $file = OpenAI::files()
        ->upload([
            'purpose' => 'assistants',
            'file' => fopen(storage_path('docs/parsing.md'), 'rb'),
        ]);

    $assistant = OpenAI::assistants()
        ->create([
            'model' => 'gpt-4-1106-preview',
            'name' => 'Laraparse Tutor',
            'instructions' => 'You are a helpful programming teacher.',
            'tools' => [
                ['type' => 'retrieval'],
            ],
            'file_ids' => [
                $file->id,
            ],
        ]);

    $run = OpenAI::threads()
        ->createAndRun([
            'assistant_id' => $assistant->id,
            'thread' => [
                'messages' => [
                    ['role' => 'user', 'content' => 'How do I grab the first paragraph?'],
                ],
            ],
        ]);

    do {
        sleep(1);

        $run = OpenAI::threads()
            ->runs()
            ->retrieve(
                threadId: $run->threadId,
                runId: $run->id
            );

    } while ($run->status !== 'completed');

    $messages = OpenAI::threads()
        ->messages()
        ->list($run->threadId);

    dd($messages);

});
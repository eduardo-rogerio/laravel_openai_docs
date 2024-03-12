<?php

use App\AI\Assistant;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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

Route::get('/', function () {
    $chat = new Assistant();
    $chat->systemMessage('You are a poetic assistant, skilled in explaining complex programming concepts with creative flair.')
        ->send('Compose a poem that explains the concept of recursion in programming.');
    $sillyPoem = $chat->reply('Cool, can you make it much, much sillier.');

    return view('welcome', ['response' => $sillyPoem]);
});

Route::get('/home', function () {
    return view('roast');
});

Route::post('/roast', static function () {
    $attributes = request()->validate([
        'topic' => ['required', 'string', 'min:3', 'max:50'],
    ]);

    $prompt = "Please roast {$attributes['topic']} in a sarcastic tone.";

    $mp3 = (new Assistant())->send(message: $prompt, speech: true);

    $name = md5($mp3);
    Storage::disk('local')
        ->put('roasts/' . $name . '.mp3', $mp3);

    return redirect('/home')->with([
        'file' => $name . '.mp3',
        'flash' => 'Audio gerado com sucesso!',
    ]);
});

Route::get('/image', function () {
    return view('image', [
        'messages' => session('messages', []),
    ]);
});

Route::post('/getimage', static function () {
    $attributes = request()->validate([
        'description' => ['required', 'string', 'min:3'],
    ]);

    $assistant = new Assistant(session('messages', []));

    $assistant->visualize($attributes['description']);

    session(['messages' => $assistant->messages()]);

    return redirect('/image');
});
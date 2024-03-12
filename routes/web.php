<?php

use App\AI\Chat;
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
    $chat = new Chat();
    $chat->systemMessage('You are a poetic assistant, skilled in explaining complex programming concepts with creative flair.')
        ->send('Compose a poem that explains the concept of recursion in programming.');
    $sillyPoem = $chat->reply('Cool, can you make it much, much sillier.');

    return view('welcome', ['response' => $sillyPoem]);
});

Route::get('/home', function () {
    return view('roast');
});

Route::post('/roast', function () {
    $attributes = request()->validate([
        'topic' => ['required', 'string', 'min:3', 'max:50'],
    ]);

    $prompt = "Please roast {$attributes['topic']} in a sarcastic tone.";

    $mp3 = (new Chat())->send(message: $prompt, speech: true);

//    file_put_contents(public_path($name), $mp3);
    $name = md5($mp3);
    Storage::disk('local')
        ->put('roasts/' . $name . '.mp3', $mp3);

    return redirect('/home')->with([
        'file' => $name . '.mp3',
        'flash' => 'Audio gerado com sucesso!',
    ]);
});
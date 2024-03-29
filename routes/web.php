<?php

use App\AI\LaraparseAssistant;
use Illuminate\Support\Facades\Route;

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
Route::get('/', static function () {

    $assistant = new LaraparseAssistant(config('openai.assistant.id'));

    $messages = $assistant->createThread()
        ->write('Hello')
        ->write('How do I grab the first paragraph using Laraparse?')
        ->send();

    dd($messages);

});
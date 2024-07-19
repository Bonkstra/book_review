<?php

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;

// INDEX
Route::get('/', function () {
    return redirect()->route('books.index');
})->name('home');

// BOOK
Route::resource('books', App\Http\Controllers\BookController::class);

// FALLBACK
Route::fallback(function () {
    return redirect()->route('home');
});

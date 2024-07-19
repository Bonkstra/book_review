<?php

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;

// INDEX
Route::get('/', function () {
    return redirect()->route('books.index');
})->name('home');

// BOOKS
Route::resource('books', App\Http\Controllers\BookController::class)->only(['index', 'show']);

// REVIEWS
Route::resource('books.reviews', App\Http\Controllers\ReviewController::class)->scoped(['review' => 'book'])->only(['create', 'store']);

// FALLBACK
Route::fallback(function () {
    return redirect()->route('home');
});

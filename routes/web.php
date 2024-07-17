<?php

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// INDEX
Route::get('tasks', function () {
    return "HOME";
})->name('home');

// FALLBACK
Route::fallback(function () {
    return redirect()->route('home');
});

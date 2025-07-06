<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('contacts', ContactController::class);
Route::post('contacts/merge', [ContactController::class,'merge'])->name('contacts.merge');
Route::resource('custom-fields', CustomFieldController::class)->except('show');
